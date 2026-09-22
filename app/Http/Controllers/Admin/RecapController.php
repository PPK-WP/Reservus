<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RecapExport;
use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Report;
use App\Models\Reservation;
use App\Services\AvailabilityService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Rekap okupansi reservasi & frekuensi laporan (US-17, asumsi A11).
 *
 * READ-ONLY: menghitung dari data tersimpan tanpa mengubah apa pun. Semua
 * ekspor (CSV/XLSX/PDF) memakai data yang persis sama dengan tabel.
 */
class RecapController extends Controller
{
    private const ZONA = 'Asia/Jakarta';

    /** Halaman rekap dengan saringan from/to/group_by. */
    public function index(Request $request): Renderable
    {
        $filter = $this->ambilFilter($request);

        return view('admin.recap.index', [
            'filter' => $filter,
            'rekap' => $this->hitungRekap($filter['from'], $filter['to'], $filter['group_by']),
        ]);
    }

    /** Ekspor CSV/XLSX/PDF dari data rekap yang sama dengan tabel. */
    public function export(Request $request)
    {
        $filter = $this->ambilFilter($request);
        $rekap = $this->hitungRekap($filter['from'], $filter['to'], $filter['group_by']);

        $format = $request->query('format', 'csv');
        if (! in_array($format, ['csv', 'xlsx', 'pdf'], true)) {
            $format = 'csv';
        }

        $namaFile = 'rekap-reservus-'.$filter['from'].'-'.$filter['to'].'.'.$format;
        $barisEkspor = array_merge(array_values($rekap['baris']), [$rekap['totals']]);

        if ($format === 'xlsx') {
            return (new RecapExport($rekap['header'], $barisEkspor))->downloadXlsx($namaFile);
        }

        if ($format === 'pdf') {
            return Pdf::loadView('admin.recap.pdf', compact('rekap', 'filter'))
                ->setPaper('a4', 'landscape')
                ->download($namaFile);
        }

        return (new RecapExport($rekap['header'], $barisEkspor))->streamCsv($namaFile);
    }

    /**
     * Memvalidasi saringan rekap. Default: 30 hari lalu s.d. 30 hari ke depan;
     * from tidak boleh melewati to dan rentang maksimal 366 hari (A11).
     *
     * @return array{from: string, to: string, group_by: string}
     */
    private function ambilFilter(Request $request): array
    {
        $hariIni = Carbon::now(self::ZONA)->startOfDay();
        $fromBawaan = $hariIni->copy()->subDays(30)->toDateString();
        $toBawaan = $hariIni->copy()->addDays(30)->toDateString();

        $validator = Validator::make($request->only(['from', 'to', 'group_by']), [
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'group_by' => ['nullable', Rule::in(['facility', 'location'])],
        ]);

        $validator->after(function (\Illuminate\Validation\Validator $sudahValidasi) use ($request, $fromBawaan, $toBawaan): void {
            if ($sudahValidasi->errors()->has('from') || $sudahValidasi->errors()->has('to')) {
                return;
            }

            $from = $request->input('from', $fromBawaan);
            $to = $request->input('to', $toBawaan);

            if ($to < $from) {
                $sudahValidasi->errors()->add('to', 'Tanggal akhir tidak boleh sebelum tanggal awal.');

                return;
            }

            if (strtotime($to) - strtotime($from) > 366 * 86400) {
                $sudahValidasi->errors()->add('to', 'Rentang tanggal maksimal 366 hari.');
            }
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $groupBy = $request->input('group_by', 'facility');
        if (! in_array($groupBy, ['facility', 'location'], true)) {
            $groupBy = 'facility';
        }

        return [
            'from' => $request->input('from', $fromBawaan),
            'to' => $request->input('to', $toBawaan),
            'group_by' => $groupBy,
        ];
    }

    /**
     * Menghitung rekap (okupansi + frekuensi laporan) per kelompok.
     *
     * @return array{
     *     header: list<string>,
     *     baris: list<array<string, mixed>>,
     *     totals: array<string, mixed>,
     *     group_by: string,
     *     from: string,
     *     to: string,
     *     jumlah_hari: int
     * }
     */
    private function hitungRekap(string $from, string $to, string $groupBy): array
    {
        $dari = Carbon::createFromFormat('Y-m-d', $from, self::ZONA)->startOfDay();
        $ke = Carbon::createFromFormat('Y-m-d', $to, self::ZONA)->startOfDay();
        $jumlahHari = (int) $dari->diffInDays($ke) + 1;

        $reservasi = Reservation::query()->approved()
            ->whereBetween('reservation_date', [$from, $to])
            ->with('facility:id,name,location')
            ->get();

        $laporan = Report::query()
            ->whereBetween('created_at', [$dari, $ke->copy()->endOfDay()])
            ->with('facility:id,name,location')
            ->get();

        $kategori = array_keys(Report::CATEGORIES);

        $kelompok = [];
        foreach ($reservasi as $satu) {
            $kunci = $groupBy === 'location' ? $satu->facility->location : $satu->facility->name;
            $kelompok[$kunci] ??= ['lokasi' => $satu->facility->location, 'reservasi' => 0, 'jam' => 0.0, 'laporan' => 0, 'k' => array_fill_keys($kategori, 0)];
            $kelompok[$kunci]['reservasi']++;
            $kelompok[$kunci]['jam'] += $this->durasiJam($satu->start_time, $satu->end_time);
        }
        foreach ($laporan as $satu) {
            $kunci = $groupBy === 'location' ? $satu->facility->location : $satu->facility->name;
            $kelompok[$kunci] ??= ['lokasi' => $satu->facility->location, 'reservasi' => 0, 'jam' => 0.0, 'laporan' => 0, 'k' => array_fill_keys($kategori, 0)];
            $kelompok[$kunci]['laporan']++;
            $kelompok[$kunci]['k'][$satu->category]++;
        }

        // Jumlah fasilitas 'aktif' per lokasi (variabel pembagi untuk group_by=location).
        $fasilitasAktifPerLokasi = Facility::query()
            ->where('status', 'aktif')
            ->selectRaw('location, count(*) as jumlah')
            ->groupBy('location')
            ->pluck('jumlah', 'location');

        $baris = [];
        $totalJam = 0.0;
        $total = ['reservasi' => 0, 'laporan' => 0, 'k' => array_fill_keys($kategori, 0)];

        foreach ($kelompok as $nama => $d) {
            $pembagi = 13 * $jumlahHari;
            $jumlahFasilitas = 1;
            if ($groupBy === 'location') {
                $jumlahFasilitas = (int) ($fasilitasAktifPerLokasi[$nama] ?? 0);
                $pembagi *= max($jumlahFasilitas, 1);
            }
            $persen = $d['jam'] > 0.0 ? $d['jam'] / $pembagi * 100 : 0.0;

            $satuBaris = [
                'kelompok' => $nama,
                'lokasi' => $groupBy === 'location' ? $jumlahFasilitas.' fasilitas aktif' : $d['lokasi'],
                'reservasi' => $d['reservasi'],
                'jam' => number_format($d['jam'], 2, ',', '.'),
                'persen' => number_format($persen, 2, ',', '.').' %',
                'laporan' => $d['laporan'],
            ];
            foreach ($kategori as $kat) {
                $satuBaris['k_'.$kat] = $d['k'][$kat];
            }
            $baris[] = $satuBaris;

            $totalJam += $d['jam'];
            $total['reservasi'] += $d['reservasi'];
            $total['laporan'] += $d['laporan'];
            foreach ($kategori as $kat) {
                $total['k'][$kat] += $d['k'][$kat];
            }
        }

        usort($baris, static fn ($a, $b) => strcmp($a['kelompok'], $b['kelompok']));

        $persenTotal = null;
        if ($groupBy === 'facility' && $totalJam > 0.0) {
            $persenTotal = number_format($totalJam / (13 * $jumlahHari) * 100, 2, ',', '.').' %';
        }

        $totals = [
            'kelompok' => 'TOTAL',
            'lokasi' => '—',
            'reservasi' => $total['reservasi'],
            'jam' => number_format($totalJam, 2, ',', '.'),
            'persen' => $persenTotal ?? '—',
            'laporan' => $total['laporan'],
        ];
        foreach ($kategori as $kat) {
            $totals['k_'.$kat] = $total['k'][$kat];
        }

        return [
            'header' => [
                'Kelompok', 'Lokasi / Jml Fasilitas Aktif', 'Reservasi Disetujui',
                'Total Jam', 'Okupansi (%)', 'Laporan Total',
            ] + array_values(Report::CATEGORIES),
            'baris' => $baris,
            'totals' => $totals,
            'group_by' => $groupBy,
            'from' => $from,
            'to' => $to,
            'jumlah_hari' => $jumlahHari,
        ];
    }

    /** Durasi reservasi (jam) dari dua kolom TIME string 'H:i' (D-05). */
    private function durasiJam(string $start, string $end): float
    {
        $service = app(AvailabilityService::class);
        $awal = strtotime($service->normalizeTime($start));
        $akhir = strtotime($service->normalizeTime($end));

        return $akhir !== false && $awal !== false ? abs($akhir - $awal) / 3600 : 0.0;
    }
}