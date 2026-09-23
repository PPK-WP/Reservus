@extends('layouts.app')

@section('title', 'Detail Reservasi — '.config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Detail Reservasi</h1>
            <p class="text-muted mb-0">Informasi pengajuan dan status pemrosesan.</p>
        </div>
        <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h2 class="h5 mb-1">{{ $reservation->facility->name }}</h2>
                    <div class="text-muted">{{ $reservation->facility->location }}</div>
                </div>
                <x-status-badge :status="$reservation->status" />
            </div>

            <dl class="row mb-0">
                <dt class="col-sm-4">Tanggal</dt>
                <dd class="col-sm-8">{{ $reservation->reservation_date->format('d/m/Y') }}</dd>
                <dt class="col-sm-4">Waktu</dt>
                <dd class="col-sm-8">{{ $reservation->timeRange() }} WIB</dd>
                <dt class="col-sm-4">Tujuan</dt>
                <dd class="col-sm-8">{!! nl2br(e($reservation->purpose)) !!}</dd>
                @if ($reservation->status === 'ditolak' && $reservation->rejection_reason)
                    <dt class="col-sm-4">Alasan penolakan</dt>
                    <dd class="col-sm-8">{!! nl2br(e($reservation->rejection_reason)) !!}</dd>
                @endif
                @if ($reservation->status === 'dibatalkan' && $reservation->cancel_reason)
                    <dt class="col-sm-4">Alasan pembatalan</dt>
                    <dd class="col-sm-8">{!! nl2br(e($reservation->cancel_reason)) !!}</dd>
                @endif
            </dl>
        </div>
        @if (app(\App\Services\AvailabilityService::class)->canUserCancel($reservation, auth()->user()))
            <div class="card-footer bg-white">
                <form method="POST" action="{{ route('reservations.cancel', $reservation) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger">Batalkan Reservasi</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
