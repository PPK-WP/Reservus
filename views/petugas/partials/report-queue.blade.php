{{-- Partial: Antrian Laporan Masuk untuk dashboard petugas (SRS-008, US 8) --}}
@php
    $laporanBaru = \App\Models\Report::with(['user', 'facility'])
        ->whereIn('status', ['baru', 'diproses'])
        ->oldest()
        ->limit(5)
        ->get();

    $jumlahBaru = \App\Models\Report::status('baru')->count();
    $jumlahDiproses = \App\Models\Report::status('diproses')->count();
@endphp

@if ($laporanBaru->isEmpty())
    <div class="text-center py-3">
        <span class="ikon-kotak mb-3" aria-hidden="true">
            <x-ikon nama="inbox" ukuran="1.3rem" />
        </span>
        <p class="text-secondary mb-0">Tidak ada laporan yang perlu ditindaklanjuti.</p>
    </div>
@else
    <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
        <p class="small text-secondary mb-0">Laporan terbaru yang perlu ditangani</p>
        <div class="d-flex gap-1">
            <span class="badge text-bg-primary">{{ $jumlahBaru }} baru</span>
            <span class="badge text-bg-info text-white">{{ $jumlahDiproses }} diproses</span>
        </div>
    </div>

    <div class="list-group list-group-flush">
        @foreach ($laporanBaru as $laporan)
            <a href="{{ route('petugas.reports.show', $laporan) }}"
               class="list-group-item list-group-item-action px-0 py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-kobalt-muda rounded-2 text-primary text-center flex-shrink-0 px-2 py-1">
                        <strong class="d-block lh-1">{{ $laporan->created_at->format('d') }}</strong>
                        <small>{{ $laporan->created_at->format('m/Y') }}</small>
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <strong class="d-block text-truncate">{{ $laporan->facility->name }}</strong>
                        <span class="small text-secondary d-block text-truncate">
                            {{ \App\Models\Report::CATEGORIES[$laporan->category] ?? $laporan->category }} · {{ $laporan->user->name }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <x-status-badge :status="$laporan->status" />
                        <div class="text-primary" aria-hidden="true">
                            <x-ikon nama="arrow-right" />
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endif
