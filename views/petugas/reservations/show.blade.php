@extends('layouts.app')

@section('title', 'Detail Reservasi #'.$reservation->id.' — '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('petugas.reservations.index') }}" class="text-decoration-none">&larr; Kembali ke Antrian</a>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h1 class="h5 mb-0">Reservasi #{{ $reservation->id }}</h1>
                    <x-status-badge :status="$reservation->status" />
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Pemohon</dt>
                        <dd class="col-sm-8">{{ $reservation->user->name }}<br><small class="text-muted">{{ $reservation->user->email }}</small></dd>
                        <dt class="col-sm-4">Fasilitas</dt>
                        <dd class="col-sm-8">{{ $reservation->facility->name }}<br><small class="text-muted">{{ $reservation->facility->location }}</small></dd>
                        <dt class="col-sm-4">Tanggal</dt>
                        <dd class="col-sm-8">{{ $reservation->reservation_date->format('d/m/Y') }}</dd>
                        <dt class="col-sm-4">Waktu</dt>
                        <dd class="col-sm-8">{{ $reservation->timeRange() }} WIB</dd>
                        <dt class="col-sm-4">Status fasilitas</dt>
                        <dd class="col-sm-8"><x-status-badge :status="$reservation->facility->status" /></dd>
                        <dt class="col-sm-4">Tujuan</dt>
                        <dd class="col-sm-8">{!! nl2br(e($reservation->purpose)) !!}</dd>
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
        </div>

        <div class="col-lg-5">
            @if ($reservation->status === 'menunggu')
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white"><h2 class="h6 mb-0">Setujui Reservasi</h2></div>
                    <div class="card-body">
                        <p class="small text-muted">Sistem akan mengunci fasilitas dan memeriksa ulang bentrok sebelum menyetujui.</p>
                        <form method="POST" action="{{ route('petugas.reservations.approve', $reservation) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success w-100">Setujui</button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white"><h2 class="h6 mb-0">Tolak Reservasi</h2></div>
                    <div class="card-body">
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
                </div>
            @elseif ($reservation->status === 'disetujui' && $reservation->startsAt()->isFuture())
                <div class="card shadow-sm">
                    <div class="card-header bg-white"><h2 class="h6 mb-0">Pembatalan Darurat</h2></div>
                    <div class="card-body">
                        <p class="small text-muted">Pembatalan petugas wajib menyertakan alasan dan hanya berlaku sebelum waktu mulai.</p>
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
                </div>
            @else
                <div class="alert alert-light border">Tidak ada aksi yang tersedia untuk status reservasi ini.</div>
            @endif
        </div>
    </div>
</div>
@endsection
