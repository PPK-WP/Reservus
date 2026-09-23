@extends('layouts.app')

@section('title', 'Detail Laporan — '.config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <a href="{{ route('reports.index') }}" class="text-decoration-none">← Kembali ke Riwayat</a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detail Laporan #{{ $report->id }}</h5>
                    <x-status-badge :status="$report->status" />
                </div>
                <div class="card-body">
                    {{-- Info fasilitas --}}
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Fasilitas</div>
                        <div class="col-sm-8">
                            {{ $report->facility->name }}
                            <span class="text-muted small">({{ \App\Models\Facility::TYPES[$report->facility->type] ?? $report->facility->type }})</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Lokasi</div>
                        <div class="col-sm-8">{{ $report->facility->location }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Kategori</div>
                        <div class="col-sm-8">
                            <span class="badge text-bg-light text-dark">{{ \App\Models\Report::CATEGORIES[$report->category] ?? $report->category }}</span>
                        </div>
                    </div>

                    <hr>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <h6 class="text-muted">Deskripsi</h6>
                        <div>{!! nl2br(e($report->description)) !!}</div>
                    </div>

                    {{-- Foto --}}
                    @if ($report->photoUrl())
                        <div class="mb-3">
                            <h6 class="text-muted">Foto</h6>
                            <a href="{{ $report->photoUrl() }}" target="_blank">
                                <img src="{{ $report->photoUrl() }}" alt="Foto laporan"
                                     class="img-fluid rounded" style="max-height: 400px;">
                            </a>
                        </div>
                    @endif

                    <hr>

                    {{-- Informasi pemrosesan --}}
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Tanggal Lapor</div>
                        <div class="col-sm-8">{{ $report->created_at->format('d/m/Y H:i') }} WIB</div>
                    </div>

                    @if ($report->processor)
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Diproses Oleh</div>
                            <div class="col-sm-8">{{ $report->processor->name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4 text-muted">Waktu Proses</div>
                            <div class="col-sm-8">{{ $report->processed_at->format('d/m/Y H:i') }} WIB</div>
                        </div>
                    @endif

                    @if ($report->resolution_note)
                        <div class="mb-3">
                            <h6 class="text-muted">Catatan Resolusi</h6>
                            <div class="alert alert-light border mb-0">
                                {!! nl2br(e($report->resolution_note)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
