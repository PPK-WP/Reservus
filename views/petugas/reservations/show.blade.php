@extends('layouts.app')

@section('title', 'Detail Reservasi #'.$reservation->id.' — '.config('app.name'))

@section('content')
<div class="container petugas-shell">
    <div class="mb-4">
        <a href="{{ route('petugas.reservations.index') }}" class="text-decoration-none">&larr; Kembali ke Antrian</a>
    </div>

    <div class="petugas-detail-layout">
        <div class="petugas-detail-card">
            <div class="card-header d-flex justify-content-between align-items-center gap-3">
                <h1 class="h5 mb-0">Reservasi #{{ $reservation->id }}</h1>
                <x-status-badge :status="$reservation->status" />
            </div>
            <div class="card-body">
                <dl class="petugas-info-grid mb-0">
                    <dt>Pemohon</dt>
                    <dd>{{ $reservation->user->name }}<br><small class="text-muted">{{ $reservation->user->email }}</small></dd>

                    <dt>Fasilitas</dt>
                    <dd>{{ $reservation->facility->name }}<br><small class="text-muted">{{ $reservation->facility->location }}</small></dd>

                    <dt>Tanggal</dt>
                    <dd>{{ $reservation->reservation_date->format('d/m/Y') }}</dd>

                    <dt>Waktu</dt>
                    <dd>{{ $reservation->timeRange() }} WIB</dd>

                    <dt>Status fasilitas</dt>
                    <dd><x-status-badge :status="$reservation->facility->status" /></dd>

                    <dt>Tujuan</dt>
                    <dd>{!! nl2br(e($reservation->purpose)) !!}</dd>
                </dl>

                @if ($reservation->potential_conflict)
                    <div class="alert alert-warning mt-4 mb-0">
                        Reservasi ini berpotensi bentrok dengan jadwal lain. Persetujuan akan memeriksa ulang bentrok di dalam transaksi.
                    </div>
                @endif

                @if ($reservation->rejection_reason)
                    <div class="alert alert-danger mt-4 mb-0">
                        <strong>Alasan penolakan:</strong><br>{!! nl2br(e($reservation->rejection_reason)) !!}
                    </div>
                @endif

                @if ($reservation->cancel_reason)
                    <div class="alert alert-secondary mt-4 mb-0">
                        <strong>Alasan pembatalan:</strong><br>{!! nl2br(e($reservation->cancel_reason)) !!}
                    </div>
                @endif
            </div>
        </div>

        <div class="petugas-actions-card">
            <div class="card-header"><h2 class="h6 mb-0">Aksi reservasi</h2></div>
            <div class="card-body">
                <div class="petugas-status-panel mb-3">
                    <div class="small text-secondary mb-1">Status saat ini</div>
                    <div><x-status-badge :status="$reservation->status" /></div>
                </div>

                @if ($reservation->status === 'menunggu')
                    <div class="petugas-action-box mb-3">
                        <h3 class="h6 mb-2">Setujui Reservasi</h3>
                        <p class="small text-muted mb-3">Sistem akan mengunci fasilitas dan memeriksa ulang bentrok sebelum menyetujui.</p>
                        <form method="POST" action="{{ route('petugas.reservations.approve', $reservation) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100">Setujui</button>
                        </form>
                    </div>

                    <div class="petugas-action-box">
                        <h3 class="h6 mb-2">Tolak Reservasi</h3>
                        <form method="POST" action="{{ route('petugas.reservations.reject', $reservation) }}">
                            @csrf
                            @method('PATCH')
                            <label for="rejection_reason" class="form-label">Alasan penolakan</label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="4" minlength="5" maxlength="1000"
                                      class="form-control @error('rejection_reason') is-invalid @enderror" required>{{ old('rejection_reason') }}</textarea>
                            @error('rejection_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <button type="submit" class="btn btn-outline-danger w-100 mt-3">Tolak</button>
                        </form>
                    </div>
                @elseif ($reservation->status === 'disetujui' && $reservation->startsAt()->isFuture())
                    <div class="petugas-action-box">
                        <h3 class="h6 mb-2">Pembatalan Darurat</h3>
                        <p class="small text-muted mb-3">Pembatalan petugas wajib menyertakan alasan dan hanya berlaku sebelum waktu mulai.</p>
                        <form method="POST" action="{{ route('petugas.reservations.cancel', $reservation) }}">
                            @csrf
                            @method('PATCH')
                            <label for="cancel_reason" class="form-label">Alasan pembatalan</label>
                            <textarea id="cancel_reason" name="cancel_reason" rows="4" minlength="5" maxlength="1000"
                                      class="form-control @error('cancel_reason') is-invalid @enderror" required>{{ old('cancel_reason') }}</textarea>
                            @error('cancel_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <button type="submit" class="btn btn-outline-danger w-100 mt-3">Batalkan Reservasi</button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-light border mb-0">Tidak ada aksi yang tersedia untuk status reservasi ini.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
