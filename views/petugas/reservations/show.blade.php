@extends('layouts.app')

@section('title', 'Detail Reservasi #'.$reservation->id.' | '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('petugas.reservations.index') }}" class="d-inline-flex align-items-center gap-2 small text-decoration-none mb-3">
            <span aria-hidden="true">&larr;</span>
            <span>Kembali ke antrian reservasi</span>
        </a>
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-2">
            <div>
                <h1 class="h3 mb-1">Reservasi #{{ $reservation->id }}</h1>
                <p class="text-secondary mb-0">Tinjau informasi pengajuan sebelum menentukan keputusan.</p>
            </div>
            <x-status-badge :status="$reservation->status" class="align-self-start" />
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-7">
            <article class="card">
                <div class="card-header bg-kobalt-muda py-3">
                    <h2 class="h6 mb-0">Informasi pengajuan</h2>
                </div>
                <div class="card-body p-3 p-md-4">
                    <section class="border rounded-3 p-3 mb-4" aria-label="Jadwal reservasi">
                        <div class="row g-3">
                            <div class="col-sm-6 d-flex align-items-center gap-3">
                                <span class="ikon-kotak flex-shrink-0" aria-hidden="true">
                                    <x-ikon nama="calendar-check" />
                                </span>
                                <div>
                                    <span class="small text-secondary d-block">Tanggal</span>
                                    <time datetime="{{ $reservation->reservation_date->format('Y-m-d') }}" class="fw-semibold">
                                        {{ $reservation->reservation_date->format('d/m/Y') }}
                                    </time>
                                </div>
                            </div>
                            <div class="col-sm-6 d-flex align-items-center gap-3">
                                <span class="ikon-kotak flex-shrink-0" aria-hidden="true">
                                    <x-ikon nama="clock-history" />
                                </span>
                                <div>
                                    <span class="small text-secondary d-block">Waktu</span>
                                    <span class="fw-semibold">{{ $reservation->timeRange() }} WIB</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <dl class="row gy-3 mb-0">
                        <dt class="col-sm-4 text-secondary fw-medium">Pemohon</dt>
                        <dd class="col-sm-8 mb-0">
                            <span class="d-block fw-semibold">{{ $reservation->user->name }}</span>
                            <span class="small text-secondary">{{ $reservation->user->email }}</span>
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Fasilitas</dt>
                        <dd class="col-sm-8 mb-0">
                            <span class="d-block fw-semibold">{{ $reservation->facility->name }}</span>
                            <span class="small text-secondary">{{ $reservation->facility->location }}</span>
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Status fasilitas</dt>
                        <dd class="col-sm-8 mb-0"><x-status-badge :status="$reservation->facility->status" /></dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Tujuan</dt>
                        <dd class="col-sm-8 mb-0 teks-ringkas">{!! nl2br(e($reservation->purpose)) !!}</dd>
                    </dl>

                    @if ($reservation->potential_conflict)
                        <div class="alert alert-warning d-flex gap-3 mt-4 mb-0" role="status">
                            <x-ikon nama="exclamation-triangle" ukuran="1.25rem" class="mt-1" />
                            <div>
                                <h3 class="h6 alert-heading mb-1">Perlu pemeriksaan jadwal</h3>
                                <p class="mb-0">Reservasi ini berpotensi bentrok. Sistem akan memeriksa ulang saat persetujuan diproses.</p>
                            </div>
                        </div>
                    @endif

                    @if ($reservation->rejection_reason)
                        <div class="alert alert-danger mt-4 mb-0" role="status">
                            <h3 class="h6 alert-heading mb-1">Alasan penolakan</h3>
                            <div>{!! nl2br(e($reservation->rejection_reason)) !!}</div>
                        </div>
                    @endif

                    @if ($reservation->cancel_reason)
                        <div class="alert alert-secondary mt-4 mb-0" role="status">
                            <h3 class="h6 alert-heading mb-1">Alasan pembatalan</h3>
                            <div>{!! nl2br(e($reservation->cancel_reason)) !!}</div>
                        </div>
                    @endif
                </div>
            </article>
        </div>

        <div class="col-lg-5">
            @if ($reservation->status === 'menunggu')
                <aside class="card border-top border-primary border-3" aria-labelledby="keputusan-petugas">
                    <div class="card-header bg-white py-3">
                        <h2 id="keputusan-petugas" class="h6 mb-0">Keputusan petugas</h2>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <section class="border rounded-3 p-3 mb-3">
                            <h3 class="h6 d-flex align-items-center gap-2 mb-1">
                                <x-ikon nama="check-circle" class="text-success" />
                                <span>Setujui reservasi</span>
                            </h3>
                            <p class="small text-secondary mb-3">Jadwal dan status fasilitas akan diperiksa ulang secara otomatis.</p>
                            <form method="POST" action="{{ route('petugas.reservations.approve', $reservation) }}"
                                  onsubmit="return confirm('Setujui reservasi ini?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100">Setujui reservasi</button>
                            </form>
                        </section>

                        <section class="border rounded-3 p-3">
                            <h3 class="h6 d-flex align-items-center gap-2 mb-1">
                                <x-ikon nama="exclamation-triangle" class="text-danger" />
                                <span>Tolak reservasi</span>
                            </h3>
                            <p class="small text-secondary mb-3">Sertakan alasan yang jelas agar dapat dipahami pemohon.</p>
                            <form method="POST" action="{{ route('petugas.reservations.reject', $reservation) }}">
                                @csrf
                                @method('PATCH')
                                <label for="rejection_reason" class="form-label fw-medium">Alasan penolakan</label>
                                <textarea id="rejection_reason" name="rejection_reason" rows="4" minlength="5" maxlength="1000"
                                          class="form-control @error('rejection_reason') is-invalid @enderror"
                                          aria-describedby="rejection-help" required>{{ old('rejection_reason') }}</textarea>
                                <div id="rejection-help" class="form-text">Minimal 5 karakter.</div>
                                @error('rejection_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <button type="submit" class="btn btn-outline-danger w-100 mt-3">Tolak reservasi</button>
                            </form>
                        </section>
                    </div>
                </aside>
            @elseif ($reservation->status === 'disetujui' && $reservation->startsAt()->isFuture())
                <aside class="card border-top border-danger border-3" aria-labelledby="pembatalan-darurat">
                    <div class="card-header bg-white py-3">
                        <h2 id="pembatalan-darurat" class="h6 mb-0">Pembatalan darurat</h2>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <p class="small text-secondary">Pembatalan hanya berlaku sebelum waktu mulai dan wajib menyertakan alasan.</p>
                        <form method="POST" action="{{ route('petugas.reservations.cancel', $reservation) }}">
                            @csrf
                            @method('PATCH')
                            <label for="cancel_reason" class="form-label fw-medium">Alasan pembatalan</label>
                            <textarea id="cancel_reason" name="cancel_reason" rows="4" minlength="5" maxlength="1000"
                                      class="form-control @error('cancel_reason') is-invalid @enderror"
                                      aria-describedby="cancel-help" required>{{ old('cancel_reason') }}</textarea>
                            <div id="cancel-help" class="form-text">Minimal 5 karakter.</div>
                            @error('cancel_reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <button type="submit" class="btn btn-outline-danger w-100 mt-3">Batalkan reservasi</button>
                        </form>
                    </div>
                </aside>
            @else
                <aside class="card border-top border-primary border-3">
                    <div class="card-body p-3 p-md-4 text-center">
                        <span class="ikon-kotak mb-3" aria-hidden="true">
                            <x-ikon nama="check-circle" ukuran="1.35rem" />
                        </span>
                        <h2 class="h6 mb-2">Reservasi sudah diproses</h2>
                        <p class="small text-secondary mb-0">Tidak ada tindakan yang tersedia untuk status reservasi ini.</p>
                    </div>
                </aside>
            @endif
        </div>
    </div>
</div>
@endsection
