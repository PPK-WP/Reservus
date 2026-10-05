@extends('layouts.app')

@section('title', 'Detail Reservasi | '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('reservations.index') }}" class="d-inline-flex align-items-center gap-2 small text-decoration-none mb-3">
            <span aria-hidden="true">&larr;</span>
            <span>Kembali ke reservasi saya</span>
        </a>
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-2">
            <div>
                <h1 class="h3 mb-1">Detail Reservasi</h1>
                <p class="text-secondary mb-0">Informasi pengajuan dan status pemrosesan reservasi.</p>
            </div>
            <x-status-badge :status="$reservation->status" class="align-self-start" />
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <article class="card">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-start gap-3">
                        <span class="ikon-kotak flex-shrink-0" aria-hidden="true">
                            <x-ikon nama="building" ukuran="1.3rem" />
                        </span>
                        <div class="min-w-0">
                            <h2 class="h5 mb-1">{{ $reservation->facility->name }}</h2>
                            <p class="text-secondary mb-0">{{ $reservation->facility->location }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <dl class="row gy-3 mb-0">
                        <dt class="col-sm-4 text-secondary fw-medium">Nomor reservasi</dt>
                        <dd class="col-sm-8 mb-0">#{{ $reservation->id }}</dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Tanggal</dt>
                        <dd class="col-sm-8 mb-0">{{ $reservation->reservation_date->format('d/m/Y') }}</dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Waktu</dt>
                        <dd class="col-sm-8 mb-0">{{ $reservation->timeRange() }} WIB</dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Tujuan</dt>
                        <dd class="col-sm-8 mb-0 teks-ringkas">{!! nl2br(e($reservation->purpose)) !!}</dd>
                    </dl>

                    @if ($reservation->status === 'ditolak' && $reservation->rejection_reason)
                        <div class="alert alert-danger mt-4 mb-0" role="status">
                            <h3 class="h6 alert-heading mb-1">Alasan penolakan</h3>
                            <div>{!! nl2br(e($reservation->rejection_reason)) !!}</div>
                        </div>
                    @endif

                    @if ($reservation->status === 'dibatalkan' && $reservation->cancel_reason)
                        <div class="alert alert-secondary mt-4 mb-0" role="status">
                            <h3 class="h6 alert-heading mb-1">Alasan pembatalan</h3>
                            <div>{!! nl2br(e($reservation->cancel_reason)) !!}</div>
                        </div>
                    @endif
                </div>
            </article>
        </div>

        <div class="col-lg-4">
            <aside class="card" aria-labelledby="status-reservasi">
                <div class="card-body p-3 p-md-4">
                    <h2 id="status-reservasi" class="h6 mb-3">Status reservasi</h2>
                    <div class="d-flex align-items-center justify-content-between gap-3 pb-3 border-bottom">
                        <span class="small text-secondary">Status saat ini</span>
                        <x-status-badge :status="$reservation->status" />
                    </div>

                    @if (app(\App\Services\AvailabilityService::class)->canUserCancel($reservation, auth()->user()))
                        <div class="pt-3">
                            <p class="small text-secondary mb-3">Reservasi masih dapat dibatalkan paling lambat 2 jam sebelum waktu mulai.</p>
                            <form method="POST" action="{{ route('reservations.cancel', $reservation) }}"
                                  onsubmit="return confirm('Batalkan reservasi ini?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger w-100">Batalkan reservasi</button>
                            </form>
                        </div>
                    @else
                        <p class="small text-secondary pt-3 mb-0">Tidak ada tindakan yang tersedia untuk reservasi ini.</p>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
