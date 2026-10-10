{{-- Ringkasan aktivitas milik pengguna yang sedang masuk. Hanya membaca data (pola partial E4),
     tidak mengubah apa pun. Semua tautan memakai URL literal (E6). --}}
@php
    $pengguna = auth()->user();
    $hariIni = \Illuminate\Support\Carbon::now('Asia/Jakarta')->toDateString();

    $reservasiMendatang = $pengguna->reservations()
        ->with('facility')
        ->whereIn('status', ['menunggu', 'disetujui'])
        ->whereDate('reservation_date', '>=', $hariIni)
        ->orderBy('reservation_date')
        ->orderBy('start_time')
        ->limit(3)
        ->get();

    $jumlahDisetujui = $pengguna->reservations()->approved()->whereDate('reservation_date', '>=', $hariIni)->count();
    $jumlahMenunggu = $pengguna->reservations()->status('menunggu')->whereDate('reservation_date', '>=', $hariIni)->count();
    $jumlahLaporanAktif = $pengguna->reports()->whereIn('status', ['baru', 'diproses'])->count();

    $laporanTerakhir = $pengguna->reports()->with('facility')->latest()->limit(3)->get();
@endphp

<div class="row g-3 mb-4">
    <div class="col-4">
        <x-kartu-angka label="Reservasi disetujui" :angka="$jumlahDisetujui" ikon="calendar-check"
                       href="/reservations" keterangan="Mulai hari ini ke depan" />
    </div>
    <div class="col-4">
        <x-kartu-angka label="Menunggu persetujuan" :angka="$jumlahMenunggu" ikon="clock-history"
                       href="/reservations" keterangan="Belum mengunci slot" />
    </div>
    <div class="col-4">
        <x-kartu-angka label="Laporan aktif" :angka="$jumlahLaporanAktif" ikon="tools"
                       href="/reports" keterangan="Baru atau sedang diproses" />
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Reservasi terdekat</h2>
                <a href="/reservations" class="small">Lihat semua</a>
            </div>
            @if ($reservasiMendatang->isEmpty())
                <div class="card-body text-center py-5">
                    <span class="ikon-kotak mb-2"><x-ikon nama="calendar-check" /></span>
                    <p class="text-secondary mb-3">Belum ada reservasi mendatang. Pilih fasilitas di katalog untuk mengajukan.</p>
                    <a href="/facilities" class="btn btn-outline-primary btn-sm">Lihat katalog</a>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach ($reservasiMendatang as $reservasi)
                        <a href="/reservations/{{ $reservasi->id }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3 py-3">
                            <div class="min-w-0">
                                <div class="fw-semibold text-truncate judul-baris">{{ $reservasi->facility->name }}</div>
                                <div class="small text-secondary">
                                    {{ $reservasi->reservation_date->translatedFormat('l, d M Y') }} · {{ $reservasi->timeRange() }}
                                </div>
                            </div>
                            <x-status-badge :status="$reservasi->status" />
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Laporan terakhir</h2>
                <a href="/reports" class="small">Lihat semua</a>
            </div>
            @if ($laporanTerakhir->isEmpty())
                <div class="card-body text-center py-5">
                    <span class="ikon-kotak mb-2"><x-ikon nama="tools" /></span>
                    <p class="text-secondary mb-3">Belum ada laporan. Temukan fasilitas yang rusak atau kotor? Laporkan di sini.</p>
                    <a href="/reports/create" class="btn btn-outline-primary btn-sm">Buat laporan</a>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach ($laporanTerakhir as $laporan)
                        <a href="/reports/{{ $laporan->id }}"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3 py-3">
                            <div class="min-w-0">
                                <div class="fw-semibold text-truncate judul-baris">{{ $laporan->facility->name }}</div>
                                <div class="small text-secondary">
                                    {{ \App\Models\Report::CATEGORIES[$laporan->category] ?? $laporan->category }}
                                    · {{ $laporan->created_at->timezone('Asia/Jakarta')->diffForHumans() }}
                                </div>
                            </div>
                            <x-status-badge :status="$laporan->status" />
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
