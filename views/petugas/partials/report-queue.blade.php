{{-- Partial: Antrian Laporan Masuk untuk dashboard petugas (SRS-008, US 8) --}}
@php
    $laporanBaru = \App\Models\Report::with(['user', 'facility'])
        ->whereIn('status', ['baru', 'diproses'])
        ->oldest()
        ->limit(10)
        ->get();

    $jumlahBaru = \App\Models\Report::status('baru')->count();
    $jumlahDiproses = \App\Models\Report::status('diproses')->count();
@endphp

<div class="mb-2">
    <span class="badge text-bg-primary">{{ $jumlahBaru }} baru</span>
    <span class="badge text-bg-info">{{ $jumlahDiproses }} diproses</span>
</div>

@if ($laporanBaru->isEmpty())
    <p class="text-muted mb-0">Tidak ada laporan yang perlu ditindaklanjuti.</p>
@else
    <div class="list-group list-group-flush">
        @foreach ($laporanBaru as $laporan)
            <a href="/petugas/reports/{{ $laporan->id }}"
               class="list-group-item list-group-item-action px-0">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-semibold">{{ $laporan->facility->name }}</div>
                        <small class="text-muted">
                            {{ \App\Models\Report::CATEGORIES[$laporan->category] ?? $laporan->category }}
                            · {{ $laporan->user->name }}
                        </small>
                        <div class="small text-muted mt-1">{{ Str::limit($laporan->description, 80) }}</div>
                    </div>
                    <div class="text-end ms-2">
                        <x-status-badge :status="$laporan->status" />
                        @if ($laporan->photo)
                            <div class="mt-1"><span class="badge text-bg-light text-dark">📷</span></div>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    @if ($jumlahBaru + $jumlahDiproses > 10)
        <div class="mt-2 text-center">
            <a href="/petugas/reports" class="small">Lihat semua →</a>
        </div>
    @endif
@endif
