{{-- Ringkasan angka untuk petugas. Hanya membaca data (pola partial E4), tidak mengubah apa pun. --}}
@php
    $hariIni = \Illuminate\Support\Carbon::now('Asia/Jakarta')->toDateString();

    $reservasiMenunggu = \App\Models\Reservation::query()->status('menunggu')->count();
    $disetujuiHariIni = \App\Models\Reservation::query()->approved()->whereDate('reservation_date', $hariIni)->count();
    $laporanBaru = \App\Models\Report::query()->status('baru')->count();
    $laporanDiproses = \App\Models\Report::query()->status('diproses')->count();
    $dalamPerbaikan = \App\Models\Facility::query()->where('status', 'dalam_perbaikan')->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <x-kartu-angka label="Reservasi menunggu" :angka="$reservasiMenunggu" ikon="clock-history"
                       href="/petugas/reservations" :sorot="$reservasiMenunggu > 0" keterangan="Perlu disetujui atau ditolak" />
    </div>
    <div class="col-6 col-lg-3">
        <x-kartu-angka label="Dipakai hari ini" :angka="$disetujuiHariIni" ikon="calendar-check"
                       keterangan="Reservasi disetujui" />
    </div>
    <div class="col-6 col-lg-3">
        <x-kartu-angka label="Laporan baru" :angka="$laporanBaru" ikon="clipboard-check"
                       href="/petugas/reports" :sorot="$laporanBaru > 0" keterangan="Belum ditindaklanjuti" />
    </div>
    <div class="col-6 col-lg-3">
        <x-kartu-angka label="Laporan diproses" :angka="$laporanDiproses" ikon="tools"
                       href="/petugas/reports" :keterangan="$dalamPerbaikan.' fasilitas dalam perbaikan'" />
    </div>
</div>
