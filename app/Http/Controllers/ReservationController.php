<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Reservasi pengguna: ajukan, lihat riwayat/detail, dan batalkan (SRS-005).
 */
class ReservationController extends Controller
{
    public function __construct(private readonly AvailabilityService $availability)
    {
    }

    public function index(Request $request): View
    {
        $query = $request->user()->reservations()->with('facility')->latest('reservation_date');

        if ($request->filled('status') && array_key_exists($request->status, Reservation::STATUSES)) {
            $query->status($request->status);
        }

        return view('reservations.index', [
            'reservations' => $query->latest('start_time')->paginate(10)->withQueryString(),
            'statuses' => Reservation::STATUSES,
            'currentStatus' => $request->status,
        ]);
    }

    public function create(Request $request): View
    {
        $selectedFacility = null;
        if ($request->filled('facility')) {
            $selectedFacility = Facility::query()
                ->where('status', 'aktif')
                ->find($request->facility);
        }

        return view('reservations.create', [
            'facilities' => Facility::query()->where('status', 'aktif')->orderBy('name')->get(),
            'selectedFacility' => $selectedFacility,
            'availability' => $this->availability,
            'selectedDate' => $request->date,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'integer', 'exists:facilities,id'],
            'reservation_date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['required', 'string'],
            'end_time' => ['required', 'string'],
            'purpose' => ['required', 'string', 'min:10', 'max:2000'],
        ], [
            'facility_id.required' => 'Fasilitas wajib dipilih.',
            'facility_id.exists' => 'Fasilitas tidak ditemukan.',
            'reservation_date.required' => 'Tanggal reservasi wajib dipilih.',
            'reservation_date.date_format' => 'Format tanggal tidak valid.',
            'start_time.required' => 'Jam mulai wajib dipilih.',
            'end_time.required' => 'Jam selesai wajib dipilih.',
            'purpose.required' => 'Tujuan penggunaan wajib diisi.',
            'purpose.min' => 'Tujuan penggunaan minimal 10 karakter.',
            'purpose.max' => 'Tujuan penggunaan maksimal 2000 karakter.',
        ]);

        $rangeErrors = $this->availability->validateRange(
            $validated['reservation_date'],
            $validated['start_time'],
            $validated['end_time']
        );
        if ($rangeErrors !== []) {
            return back()->withErrors(['reservation' => $rangeErrors])->withInput();
        }

        $facility = Facility::find($validated['facility_id']);
        if (! $facility || ! $facility->isReservable()) {
            return back()->withErrors(['facility_id' => 'Fasilitas tidak aktif dan tidak dapat dipesan.'])
                ->withInput();
        }

        $start = $this->availability->normalizeTime($validated['start_time']);
        $end = $this->availability->normalizeTime($validated['end_time']);
        if ($this->availability->hasConflict(
            $facility->id,
            $validated['reservation_date'],
            $start,
            $end
        )) {
            return back()->withErrors(['reservation' => 'Slot yang dipilih sudah digunakan oleh reservasi yang disetujui.'])
                ->withInput();
        }

        $request->user()->reservations()->create([
            'facility_id' => $facility->id,
            'reservation_date' => $validated['reservation_date'],
            'start_time' => $start,
            'end_time' => $end,
            'purpose' => $validated['purpose'],
        ]);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservasi berhasil diajukan dan menunggu persetujuan petugas.');
    }

    public function show(Reservation $reservation): View
    {
        Gate::authorize('view', $reservation);

        return view('reservations.show', compact('reservation'));
    }

    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        Gate::authorize('cancel', $reservation);

        if (! $this->availability->canUserCancel($reservation, $request->user())) {
            return back()->withErrors(['reservation' => 'Reservasi hanya dapat dibatalkan minimal 2 jam sebelum waktu mulai.']);
        }

        $reservation->status = 'dibatalkan';
        $reservation->cancelled_by = $request->user()->id;
        $reservation->cancelled_at = Carbon::now('Asia/Jakarta');
        $reservation->save();

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Reservasi berhasil dibatalkan.');
    }
}
