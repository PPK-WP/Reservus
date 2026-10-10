@extends('layouts.app')

@section('title', $facility->name.' — '.config('app.name'))

@section('content')
<div class="container py-2">
    <div class="mb-3">
        <a href="/facilities" class="small text-decoration-none">&larr; Kembali ke katalog</a>
    </div>

    {{-- Banner status fasilitas (A7): dalam_perbaikan/nonaktif tetap bisa dibuka lewat URL --}}
    @if ($facility->status === 'dalam_perbaikan')
        <div class="alert alert-warning rounded-4 border-0" role="status">
            <strong>Fasilitas dalam perbaikan.</strong> Seluruh slot ditandai tidak tersedia sampai perbaikan selesai.
        </div>
    @elseif ($facility->status === 'nonaktif')
        <div class="alert alert-secondary rounded-4 border-0" role="status">
            <strong>Fasilitas nonaktif.</strong> Fasilitas ini tidak dapat direservasi untuk saat ini.
        </div>
    @endif

    @if ($peringatan)
        <div class="alert alert-danger rounded-4 border-0" role="alert">{{ $peringatan }}</div>
    @endif

    <div class="row g-4 align-items-start">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                        <h1 class="h4 mb-0">{{ $facility->name }}</h1>
                        <x-status-badge :status="$facility->status" />
                    </div>

                    <span class="badge text-bg-light border rounded-pill px-2 py-1">
                        {{ \App\Models\Facility::TYPES[$facility->type] ?? $facility->type }}
                    </span>

                    <div class="mt-3 mb-4">
                        <x-facility-photo :facility="$facility" ratio="4x3" class="rounded-4 border" />
                    </div>

                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted fw-normal">Lokasi</dt>
                        <dd class="col-7 mb-2">{{ $facility->location }}</dd>

                        <dt class="col-5 text-muted fw-normal">Kapasitas</dt>
                        <dd class="col-7 mb-2">{{ $facility->capacity }} orang</dd>

                        @if ($facility->description)
                            <dt class="col-5 text-muted fw-normal">Deskripsi</dt>
                            <dd class="col-7 mb-2">{!! nl2br(e($facility->description)) !!}</dd>
                        @endif
                    </dl>

                    {{-- Aksi: URL literal ke SRS-005/SRS-007 (E6) --}}
                    <div class="d-grid gap-2 mt-4">
                        @auth
                            @if ($facility->isReservable())
                                <a class="btn btn-primary rounded-pill"
                                   href="/reservations/create?facility={{ $facility->id }}&date={{ $tanggal->toDateString() }}">
                                    Ajukan Reservasi
                                </a>
                            @endif
                            <a class="btn btn-outline-primary rounded-pill"
                               href="/reports/create?facility={{ $facility->id }}">
                                Laporkan Masalah
                            </a>
                        @else
                            <a class="btn btn-outline-primary rounded-pill" href="/login">Masuk untuk mengajukan reservasi</a>
                        @endauth
                    </div>

                    <p class="small text-muted mt-3 mb-0">
                        Jam operasional {{ \App\Services\AvailabilityService::OPEN }}–{{ \App\Services\AvailabilityService::CLOSE }}
                        WIB, {{ $totalSlot }} slot per hari.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 px-3 px-md-4 py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h2 class="h5 mb-0">Ketersediaan Slot</h2>
                            <span class="small text-muted">{{ $tanggal->translatedFormat('l, j F Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="/facilities/{{ $facility->id }}?date={{ $tanggal->copy()->subDay()->toDateString() }}"
                               class="btn btn-sm btn-outline-secondary rounded-pill {{ $bisaSebelum ? '' : 'disabled' }}"
                               aria-label="Hari sebelumnya">&larr; Sebelumnya</a>
                            <label for="pilih-tanggal" class="visually-hidden">Pilih tanggal</label>
                            <input type="date" id="pilih-tanggal" class="form-control form-control-sm rounded-pill"
                                   style="max-width: 165px;"
                                   value="{{ $tanggal->toDateString() }}"
                                   min="{{ $minDate }}" max="{{ $maxDate }}">
                            <a href="/facilities/{{ $facility->id }}?date={{ $tanggal->copy()->addDay()->toDateString() }}"
                               class="btn btn-sm btn-outline-secondary rounded-pill {{ $bisaSesudah ? '' : 'disabled' }}"
                               aria-label="Hari berikutnya">Berikutnya &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <span class="small">
                            <strong>{{ $jumlahTersedia }}</strong> dari <strong>{{ $totalSlot }}</strong> slot tersedia
                        </span>
                        <div class="d-flex gap-3 small text-muted">
                            <span><span class="badge text-bg-success rounded-pill">Tersedia</span></span>
                            <span><span class="badge text-bg-secondary rounded-pill">Tidak tersedia</span></span>
                        </div>
                    </div>

                    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-2">
                        @foreach ($slots as $slot)
                            <div class="col">
                                @if ($slot['status'] === 'tersedia')
                                    <div class="border border-success rounded-4 p-2 text-center h-100 bg-success bg-opacity-10">
                                        <div class="fw-semibold small">{{ $slot['label'] }}</div>
                                        <span class="badge text-bg-success rounded-pill mt-2">Tersedia</span>
                                    </div>
                                @else
                                    <div class="border rounded-4 p-2 text-center h-100 bg-secondary bg-opacity-10 text-muted">
                                        <div class="fw-semibold small">{{ $slot['label'] }}</div>
                                        <span class="badge text-bg-secondary rounded-pill mt-2">Tidak tersedia</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <p class="small text-muted mt-3 mb-0">
                        Slot ditandai tidak tersedia bila tumpang tindih dengan reservasi yang sudah disetujui.
                        Nama pemohon dan tujuan penggunaan tidak pernah ditampilkan di halaman ini.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Pemilih tanggal: langsung memuat ulang halaman dengan tanggal baru.
    (function () {
        const picker = document.getElementById('pilih-tanggal');
        if (!picker) {
            return;
        }
        picker.addEventListener('change', function () {
            if (!this.value) {
                return;
            }
            window.location.href = '/facilities/{{ $facility->id }}?date=' + encodeURIComponent(this.value);
        });
    })();
</script>
@endpush
