@extends('layouts.app')

@section('title', 'Laporan Saya | '.config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Laporan Saya</h1>
            <p class="text-muted mb-0">Riwayat laporan kerusakan dan masalah fasilitas yang Anda kirim.</p>
        </div>
        <a href="{{ route('reports.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Ajukan Laporan
        </a>
    </div>

    {{-- Filter status --}}
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link {{ !$currentStatus ? 'active' : '' }}"
               href="{{ route('reports.index') }}">Semua</a>
        </li>
        @foreach ($statuses as $value => $label)
            <li class="nav-item">
                <a class="nav-link {{ $currentStatus === $value ? 'active' : '' }}"
                   href="{{ route('reports.index', ['status' => $value]) }}">{{ $label }}</a>
            </li>
        @endforeach
    </ul>

    @if ($reports->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5 px-3">
                <span class="ikon-kotak mb-3" aria-hidden="true">
                    <x-ikon nama="inbox" ukuran="1.4rem" />
                </span>
                @if ($currentStatus)
                    <h2 class="h6 mb-2">Belum ada laporan dengan status ini</h2>
                    <p class="text-secondary mb-3">Pilih status lain untuk melihat riwayat laporan Anda.</p>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-primary">Lihat semua laporan</a>
                @else
                    <h2 class="h6 mb-2">Belum ada laporan</h2>
                    <p class="text-secondary mb-3">Pilih fasilitas di katalog untuk mengajukan laporan.</p>
                    <a href="/facilities" class="btn btn-outline-primary">Lihat katalog</a>
                @endif
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach ($reports as $report)
                <article class="card card-interaktif">
                    <div class="card-body">
                        <div class="row align-items-center">
                            {{-- Thumbnail foto --}}
                            <div class="col-auto">
                                @if ($report->photoUrl())
                                    <img src="{{ $report->photoUrl() }}" alt="Foto laporan"
                                         class="rounded" style="width: 56px; height: 56px; object-fit: cover;">
                                @else
                                    <div class="bg-kobalt-muda rounded d-flex align-items-center justify-content-center"
                                         style="width: 56px; height: 56px;">
                                        <span class="text-secondary small">Tanpa foto</span>
                                    </div>
                                @endif
                            </div>
                            <div class="col">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="{{ route('reports.show', $report) }}" class="text-decoration-none">
                                                {{ $report->facility->name }}
                                            </a>
                                        </h6>
                                        <span class="badge text-bg-light text-dark me-1">{{ \App\Models\Report::CATEGORIES[$report->category] ?? $report->category }}</span>
                                        <x-status-badge :status="$report->status" />
                                    </div>
                                    <small class="text-muted">{{ $report->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
