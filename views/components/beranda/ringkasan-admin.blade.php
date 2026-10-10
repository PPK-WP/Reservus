@props(['pendingCount' => 0])

{{-- Ringkasan untuk admin. Hanya membaca data (pola partial E4), tidak mengubah apa pun. --}}
@php
    $pendaftarTerlama = \App\Models\User::query()
        ->where('status', 'pending')
        ->orderBy('created_at')
        ->limit(3)
        ->get(['id', 'name', 'user_type', 'created_at']);

    $penggunaAktif = \App\Models\User::query()->where('role', 'pengguna')->where('status', 'aktif')->count();
    $petugasAktif = \App\Models\User::query()->where('role', 'petugas')->where('status', 'aktif')->count();

    $fasilitasPerStatus = \App\Models\Facility::query()
        ->selectRaw('status, count(*) as jumlah')
        ->groupBy('status')
        ->pluck('jumlah', 'status');

    $reservasiMenunggu = \App\Models\Reservation::query()->status('menunggu')->count();
    $laporanTerbuka = \App\Models\Report::query()->whereIn('status', ['baru', 'diproses'])->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <x-kartu-angka label="Akun menunggu verifikasi" :angka="$pendingCount" ikon="person-check"
                       href="/admin/verifications" :sorot="$pendingCount > 0"
                       :keterangan="$pendingCount > 0 ? 'Perlu keputusan Anda' : 'Semua sudah diproses'" />
    </div>
    <div class="col-6 col-xl-3">
        <x-kartu-angka label="Pengguna aktif" :angka="$penggunaAktif" ikon="people"
                       href="/admin/users" :keterangan="$petugasAktif.' petugas aktif'" />
    </div>
    <div class="col-6 col-xl-3">
        <x-kartu-angka label="Reservasi menunggu" :angka="$reservasiMenunggu" ikon="clock-history"
                       keterangan="Diproses oleh petugas" />
    </div>
    <div class="col-6 col-xl-3">
        <x-kartu-angka label="Laporan belum selesai" :angka="$laporanTerbuka" ikon="tools"
                       keterangan="Baru atau sedang diproses" />
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Pendaftar yang paling lama menunggu</h2>
                <a href="/admin/verifications" class="small">Buka verifikasi</a>
            </div>
            @if ($pendaftarTerlama->isEmpty())
                <div class="card-body text-center py-5">
                    <span class="ikon-kotak mb-2"><x-ikon nama="check-circle" /></span>
                    <p class="text-secondary mb-0">Tidak ada akun menunggu verifikasi. Pendaftaran baru akan muncul di sini.</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach ($pendaftarTerlama as $pendaftar)
                        <a href="/admin/verifications"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3 py-3">
                            <div class="min-w-0">
                                <div class="fw-semibold text-truncate judul-baris">{{ $pendaftar->name }}</div>
                                <div class="small text-secondary">
                                    {{ $pendaftar->user_type ? (\App\Models\User::USER_TYPES[$pendaftar->user_type] ?? $pendaftar->user_type) : 'Jenis belum diisi' }}
                                    · mendaftar {{ $pendaftar->created_at?->timezone('Asia/Jakarta')->diffForHumans() }}
                                </div>
                            </div>
                            <x-status-badge status="pending" />
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Status fasilitas</h2>
                <a href="/admin/facilities" class="small">Kelola</a>
            </div>
            <ul class="list-group list-group-flush">
                @foreach (\App\Models\Facility::STATUSES as $status => $label)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <x-status-badge :status="$status" />
                        <span class="fw-semibold">{{ $fasilitasPerStatus[$status] ?? 0 }} fasilitas</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
