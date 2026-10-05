@extends('layouts.app')

@section('title', 'Detail Reservasi — '.config('app.name'))

@section('content')
<div class="container reservasi-shell">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <p class="text-primary fw-semibold mb-2 text-uppercase small">Reservasi</p>
            <h1 class="h3 mb-1">Detail Reservasi</h1>
            <p class="text-secondary mb-0">Informasi pengajuan dan status pemrosesan fasilitas.</p>
        </div>
        <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">Kembali</a>
    </div>

    <div class="reservasi-detail-layout">
        <div class="reservasi-detail-card">
            <div class="reservasi-detail-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h2 class="h5 mb-1">{{ $reservation->facility->name }}</h2>
                    <div class="text-secondary small">{{ $reservation->facility->location }}</div>
                </div>
                <x-status-badge :status="$reservation->status" />
            </div>
            <div class="card-body">
                <dl class="reservasi-detail-grid mb-0">
                    <dt>Nomor</dt>
                    <dd>#{{ $reservation->id }}</dd>

                    <dt>Tanggal</dt>
                    <dd>{{ $reservation->reservation_date->format('d/m/Y') }}</dd>

                    <dt>Waktu</dt>
                    <dd>{{ $reservation->timeRange() }} WIB</dd>

                    <dt>Tujuan</dt>
                    <dd>{!! nl2br(e($reservation->purpose)) !!}</dd>

                    @if ($reservation->status === 'ditolak' && $reservation->rejection_reason)
                        <dt>Alasan penolakan</dt>
                        <dd>{!! nl2br(e($reservation->rejection_reason)) !!}</dd>
                    @endif

                    @if ($reservation->status === 'dibatalkan' && $reservation->cancel_reason)
                        <dt>Alasan pembatalan</dt>
                        <dd>{!! nl2br(e($reservation->cancel_reason)) !!}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <aside class="reservasi-side-card">
            <div class="card-header">
                <h2 class="h6 mb-0">Status reservasi</h2>
            </div>
            <div class="card-body">
                <div class="reservasi-status-panel mb-3">
                    <div class="small text-secondary mb-1">Status saat ini</div>
                    <div><x-status-badge :status="$reservation->status" /></div>
                </div>

                @if ($reservation->status === 'menunggu')
                    <div class="alert alert-info mb-0">
                        Pengajuan Anda sedang menunggu persetujuan petugas.
                    </div>
                @elseif ($reservation->status === 'ditolak')
                    <div class="alert alert-danger mb-0">
                        Pengajuan ditolak. Periksa alasan yang tercantum untuk langkah berikutnya.
                    </div>
                @elseif ($reservation->status === 'disetujui')
                    <div class="alert alert-success mb-0">
                        Reservasi telah disetujui dan siap digunakan sesuai jadwal.
                    </div>
                @elseif ($reservation->status === 'dibatalkan')
                    <div class="alert alert-secondary mb-0">
                        Reservasi ini telah dibatalkan.
                    </div>
                @endif

                @if (app(\App\Services\AvailabilityService::class)->canUserCancel($reservation, auth()->user()))
                    <form method="POST" action="{{ route('reservations.cancel', $reservation) }}" class="mt-3">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger w-100">Batalkan Reservasi</button>
                    </form>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection
