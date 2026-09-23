<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Services\AvailabilityService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Katalog fasilitas & ketersediaan slot untuk pengunjung (US-1, US-2).
 *
 * Bersifat publik (tanpa login). Halaman detail hanya menampilkan status slot
 * hasil AvailabilityService::slotStatuses() sehingga TIDAK PERNAH memuat nama
 * pemohon, tujuan, maupun ID reservasi (privasi US-1).
 */
class FacilityCatalogController extends Controller
{
    /** Zona waktu aplikasi (WIB, D-04). */
    private const TZ = 'Asia/Jakarta';

    /** Jumlah kartu per halaman katalog. */
    private const PER_HALAMAN = 12;

    public function __construct(private readonly AvailabilityService $availability)
    {
    }

    /** Katalog + filter tipe/lokasi/kapasitas/kata kunci (US-1). */
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'type' => ['nullable', 'string', Rule::in(array_keys(Facility::TYPES))],
            'location' => ['nullable', 'string', 'max:100'],
            'min_capacity' => ['nullable', 'integer', 'min:1'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $type = $filter['type'] ?? null;
        $location = $filter['location'] ?? null;
        $minCapacity = isset($filter['min_capacity']) ? (int) $filter['min_capacity'] : null;
        $q = trim((string) ($filter['q'] ?? ''));

        $lokasi = Facility::query()
            ->publicCatalog()
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        $facilities = Facility::query()
            ->publicCatalog()
            ->when($type !== null && $type !== '', fn ($query) => $query->where('type', $type))
            ->when($location !== null && $location !== '', fn ($query) => $query->where('location', $location))
            ->when($minCapacity !== null, fn ($query) => $query->where('capacity', '>=', $minCapacity))
            ->when($q !== '', function ($query) use ($q) {
                // Wildcard % dan _ di-escape agar pencarian tetap harfiah (LIKE).
                $escaped = addcslashes($q, '\\%_');
                $query->where('name', 'like', "%{$escaped}%");
            })
            ->orderBy('name')
            ->paginate(self::PER_HALAMAN)
            ->withQueryString();

        // Ringkasan ketersediaan per kartu: "X/26 slot tersedia hari ini".
        $hariIni = Carbon::now(self::TZ)->toDateString();
        $totalSlot = count($this->availability->slots());
        $facilities->getCollection()->transform(function (Facility $facility) use ($hariIni): Facility {
            $facility->slot_tersedia_hari_ini = $this->availability->availableCount($facility, $hariIni);

            return $facility;
        });

        return view('facilities.index', [
            'facilities' => $facilities,
            'lokasi' => $lokasi,
            'totalSlot' => $totalSlot,
            'filter' => [
                'type' => $type,
                'location' => $location,
                'min_capacity' => $minCapacity,
                'q' => $q,
            ],
        ]);
    }

    /** Detail fasilitas + grid 26 slot per tanggal (US-2). */
    public function show(Request $request, Facility $facility): View
    {
        $hariIni = Carbon::now(self::TZ)->startOfDay();
        $tanggal = $hariIni->copy();
        $peringatan = null;
        $diminta = $request->query('date');

        if ($diminta !== null && trim((string) $diminta) !== '') {
            $parsed = $this->parseTanggal($diminta);

            if (! $parsed
                || $parsed->lt($hariIni)
                || $parsed->gt($hariIni->copy()->addDays(AvailabilityService::MAX_DAYS_AHEAD))) {
                $peringatan = 'Tanggal tidak valid atau di luar rentang (hari ini s.d. '
                    .AvailabilityService::MAX_DAYS_AHEAD
                    .' hari ke depan). Menampilkan hari ini.';
                $tanggal = $hariIni->copy();
            } else {
                $tanggal = $parsed;
            }
        }

        $slots = $this->availability->slotStatuses($facility, $tanggal->toDateString());
        $jumlahTersedia = count(array_filter(
            $slots,
            fn (array $slot): bool => $slot['status'] === 'tersedia'
        ));

        $batasAtas = $hariIni->copy()->addDays(AvailabilityService::MAX_DAYS_AHEAD);

        return view('facilities.show', [
            'facility' => $facility,
            'tanggal' => $tanggal,
            'slots' => $slots,
            'jumlahTersedia' => $jumlahTersedia,
            'totalSlot' => count($slots),
            'peringatan' => $peringatan,
            'minDate' => $hariIni->toDateString(),
            'maxDate' => $batasAtas->toDateString(),
            'bisaSebelum' => $tanggal->gt($hariIni),
            'bisaSesudah' => $tanggal->lt($batasAtas),
        ]);
    }

    /** Parse 'Y-m-d' (WIB) menjadi Carbon awal hari, null bila formatnya salah. */
    private function parseTanggal(string $nilai): ?Carbon
    {
        $nilai = trim($nilai);

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $nilai, self::TZ);
        } catch (\Throwable) {
            return null;
        }

        if (! $parsed || $parsed->format('Y-m-d') !== $nilai) {
            return null;
        }

        return $parsed->startOfDay();
    }
}
