<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Antrian dan pemrosesan reservasi oleh petugas (SRS-006, US 8, 9, 10).
 */
class ReservationController extends Controller
{
    public function __construct(private readonly AvailabilityService $availability)
    {
    }

    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'menunggu');
        $query = Reservation::with(['user', 'facility', 'processor'])->latest('reservation_date')->latest('start_time');

        if ($tab === 'menunggu') {
            $query->where('status', 'menunggu');
        } elseif ($tab === 'disetujui') {
            $query->where('status', 'disetujui')
                ->whereDate('reservation_date', '>=', Carbon::now('Asia/Jakarta')->toDateString());
        } else {
            $tab = 'semua';
        }

        $reservations = $query->paginate(15)->withQueryString();
        $reservations->getCollection()->each(function (Reservation $reservation): void {
            $reservation->setAttribute('potential_conflict', $this->hasPotentialConflict($reservation));
        });

        return view('petugas.reservations.index', [
            'reservations' => $reservations,
            'tab' => $tab,
        ]);
    }

    public function show(Reservation $reservation): View
    {
        $reservation->load(['user', 'facility', 'processor']);
        $reservation->setAttribute('potential_conflict', $this->hasPotentialConflict($reservation));

        return view('petugas.reservations.show', compact('reservation'));
    }

    public function approve(Reservation $reservation): RedirectResponse
    {
        $outcome = DB::transaction(function () use ($reservation): string {
            $lockedReservation = Reservation::query()->lockForUpdate()->findOrFail($reservation->id);
            $facility = Facility::query()->lockForUpdate()->findOrFail($lockedReservation->facility_id);

            if ($lockedReservation->status !== 'menunggu') {
                return 'already_processed';
            }

            if (! $facility->isReservable()) {
                $this->rejectLockedReservation($lockedReservation, 'Fasilitas tidak aktif saat reservasi diproses.');

                return 'facility_unavailable';
            }

            if ($this->availability->hasConflict(
                $facility->id,
                $lockedReservation->reservation_date->format('Y-m-d'),
                $lockedReservation->start_time,
                $lockedReservation->end_time,
                $lockedReservation->id
            )) {
                $this->rejectLockedReservation($lockedReservation, 'Ditolak otomatis karena bentrok dengan reservasi yang disetujui.');

                return 'conflict';
            }

            $lockedReservation->status = 'disetujui';
            $lockedReservation->processed_by = request()->user()->id;
            $lockedReservation->processed_at = Carbon::now('Asia/Jakarta');
            $lockedReservation->save();

            return 'approved';
        });

        $messages = [
            'approved' => ['success', 'Reservasi berhasil disetujui.'],
            'conflict' => ['error', 'Reservasi ditolak otomatis karena bentrok dengan reservasi yang disetujui.'],
            'facility_unavailable' => ['error', 'Reservasi ditolak otomatis karena fasilitas tidak aktif.'],
            'already_processed' => ['error', 'Reservasi sudah diproses sebelumnya.'],
        ];
        [$type, $message] = $messages[$outcome];

        return redirect()->route('petugas.reservations.show', $reservation)->with($type, $message);
    }

    public function reject(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min' => 'Alasan penolakan minimal 5 karakter.',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        $updated = DB::transaction(function () use ($reservation, $validated, $request): bool {
            $lockedReservation = Reservation::query()->lockForUpdate()->findOrFail($reservation->id);
            if ($lockedReservation->status !== 'menunggu') {
                return false;
            }

            $lockedReservation->status = 'ditolak';
            $lockedReservation->rejection_reason = $validated['rejection_reason'];
            $lockedReservation->processed_by = $request->user()->id;
            $lockedReservation->processed_at = Carbon::now('Asia/Jakarta');
            $lockedReservation->save();

            return true;
        });

        return redirect()->route('petugas.reservations.show', $reservation)
            ->with($updated ? 'success' : 'error', $updated
                ? 'Reservasi berhasil ditolak.'
                : 'Reservasi sudah diproses sebelumnya.');
    }

    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        $validated = $request->validate([
            'cancel_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'cancel_reason.required' => 'Alasan pembatalan wajib diisi.',
            'cancel_reason.min' => 'Alasan pembatalan minimal 5 karakter.',
            'cancel_reason.max' => 'Alasan pembatalan maksimal 1000 karakter.',
        ]);

        $updated = DB::transaction(function () use ($reservation, $validated, $request): bool {
            $lockedReservation = Reservation::query()->lockForUpdate()->findOrFail($reservation->id);
            $now = Carbon::now('Asia/Jakarta');

            if ($lockedReservation->status !== 'disetujui' || $lockedReservation->startsAt()->lte($now)) {
                return false;
            }

            $lockedReservation->status = 'dibatalkan';
            $lockedReservation->cancel_reason = $validated['cancel_reason'];
            $lockedReservation->cancelled_by = $request->user()->id;
            $lockedReservation->cancelled_at = $now;
            $lockedReservation->save();

            return true;
        });

        return redirect()->route('petugas.reservations.show', $reservation)
            ->with($updated ? 'success' : 'error', $updated
                ? 'Reservasi mendesak berhasil dibatalkan.'
                : 'Hanya reservasi disetujui yang belum lewat yang dapat dibatalkan.');
    }

    private function rejectLockedReservation(Reservation $reservation, string $reason): void
    {
        $reservation->status = 'ditolak';
        $reservation->rejection_reason = $reason;
        $reservation->processed_by = request()->user()->id;
        $reservation->processed_at = Carbon::now('Asia/Jakarta');
        $reservation->save();
    }

    private function hasPotentialConflict(Reservation $reservation): bool
    {
        if (! in_array($reservation->status, ['menunggu', 'disetujui'], true)) {
            return false;
        }

        if ($this->availability->hasConflict(
            $reservation->facility_id,
            $reservation->reservation_date->format('Y-m-d'),
            $reservation->start_time,
            $reservation->end_time,
            $reservation->id
        )) {
            return true;
        }

        return Reservation::query()
            ->where('status', 'menunggu')
            ->where('facility_id', $reservation->facility_id)
            ->whereDate('reservation_date', $reservation->reservation_date->format('Y-m-d'))
            ->where('id', '!=', $reservation->id)
            ->where('start_time', '<', $reservation->end_time)
            ->where('end_time', '>', $reservation->start_time)
            ->exists();
    }
}
