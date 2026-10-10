@extends('layouts.app')

@section('title', 'Detail Laporan — '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('reports.index') }}" class="d-inline-flex align-items-center gap-2 small text-decoration-none mb-3">
            <span aria-hidden="true">&larr;</span>
            <span>Kembali ke laporan saya</span>
        </a>
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-2">
            <div>
                <h1 class="h3 mb-1">Detail Laporan</h1>
                <p class="text-secondary mb-0">Informasi laporan dan status penanganannya.</p>
            </div>
            <x-status-badge :status="$report->status" class="align-self-start" />
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <article class="card">
                <div class="card-header bg-kobalt-muda py-3">
                    <div class="d-flex align-items-start gap-3">
                        <span class="ikon-kotak flex-shrink-0" aria-hidden="true">
                            <x-ikon nama="building" ukuran="1.3rem" />
                        </span>
                        <div class="min-w-0">
                            <h2 class="h5 mb-1">{{ $report->facility->name }}</h2>
                            <p class="text-secondary mb-0">{{ $report->facility->location }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <dl class="row gy-3 mb-0">
                        <dt class="col-sm-4 text-secondary fw-medium">Kategori</dt>
                        <dd class="col-sm-8 mb-0">
                            <span class="badge text-bg-light text-dark border">{{ \App\Models\Report::CATEGORIES[$report->category] ?? $report->category }}</span>
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Tipe fasilitas</dt>
                        <dd class="col-sm-8 mb-0">
                            {{ \App\Models\Facility::TYPES[$report->facility->type] ?? $report->facility->type }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Tanggal lapor</dt>
                        <dd class="col-sm-8 mb-0">{{ $report->created_at->format('d/m/Y H:i') }} WIB</dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Deskripsi</dt>
                        <dd class="col-sm-8 mb-0 teks-ringkas">{!! nl2br(e($report->description)) !!}</dd>
                    </dl>

                    @if ($report->photoUrl())
                        <div class="mt-4">
                            <h3 class="h6 text-secondary mb-2">Foto</h3>
                            <a href="{{ $report->photoUrl() }}" target="_blank">
                                <img src="{{ $report->photoUrl() }}" alt="Foto laporan"
                                     class="img-fluid rounded border" style="max-height: 400px;">
                            </a>
                        </div>
                    @endif

                    @if ($report->processor)
                        <div class="border-top mt-4 pt-4">
                            <dl class="row gy-3 mb-0">
                                <dt class="col-sm-4 text-secondary fw-medium">Diproses oleh</dt>
                                <dd class="col-sm-8 mb-0">{{ $report->processor->name }}</dd>

                                <dt class="col-sm-4 text-secondary fw-medium">Waktu proses</dt>
                                <dd class="col-sm-8 mb-0">{{ $report->processed_at->format('d/m/Y H:i') }} WIB</dd>
                            </dl>
                        </div>
                    @endif

                    @if ($report->resolution_note)
                        <div class="alert alert-light border mt-4 mb-0">
                            <h3 class="h6 alert-heading mb-1">Catatan resolusi</h3>
                            <div>{!! nl2br(e($report->resolution_note)) !!}</div>
                        </div>
                    @endif
                </div>
            </article>
        </div>

        <div class="col-lg-4">
            <aside class="card border-top border-primary border-3" aria-labelledby="status-laporan">
                <div class="card-body p-3 p-md-4">
                    <h2 id="status-laporan" class="h6 mb-3">Status laporan</h2>
                    <div class="d-flex align-items-center justify-content-between gap-3 pb-3 border-bottom">
                        <span class="small text-secondary">Status saat ini</span>
                        <x-status-badge :status="$report->status" />
                    </div>
                    <p class="small text-secondary pt-3 mb-0">Laporan ini akan diproses oleh petugas. Pantau status penanganan di halaman ini.</p>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
