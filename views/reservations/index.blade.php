@extends('layouts.app')

@section('title', 'Reservasi Saya — '.config('app.name'))

@section('content')
<div class="container reservasi-shell">
    <div class="reservasi-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <p class="text-primary fw-semibold mb-2 text-uppercase small letter-spacing">Reservasi Saya</p>
            <h1 class="h3 mb-2">Riwayat pengajuan fasilitas</h1>
            <p class="text-secondary subtitle mb-0">Pantau status pengajuan, lihat detail slot, dan kelola reservasi yang masih bisa dibatalkan.</p>
        </div>
        <a href="{{ route('reservations.create') }}" class="btn btn-primary px-3">Ajukan Reservasi</a>
    </div>

    <div class="reservasi-filters mb-4">
        <ul class="nav nav-pills flex-wrap gap-2">
            <li class="nav-item">
                <a class="nav-link {{ !$currentStatus ? 'active' : '' }}" href="{{ route('reservations.index') }}">Semua</a>
            </li>
            @foreach ($statuses as $value => $label)
                <li class="nav-item">
                    <a class="nav-link {{ $currentStatus === $value ? 'active' : '' }}"
                       href="{{ route('reservations.index', ['status' => $value]) }}">{{ $label }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    @if ($reservations->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="reservasi-pengantar d-inline-flex align-items-center gap-3 mb-3 text-start">
                    <span class="icon-wrap">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M3 10h18"></path></svg>
                    </span>
                    <div>
                        <div class="fw-semibold">Belum ada reservasi</div>
                        <small class="text-secondary">Mulai dengan mengajukan slot fasilitas yang Anda butuhkan.</small>
                    </div>
                </div>
                <div>
                    <a href="{{ route('reservations.create') }}" class="btn btn-outline-primary">Ajukan Reservasi</a>
                </div>
            </div>
        </div>
    @else
        <div class="reservasi-list">
            @foreach ($reservations as $reservation)
                <article class="reservasi-card">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                            <div class="min-w-0">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h2 class="h5 mb-0 text-break">
                                        <a href="{{ route('reservations.show', $reservation) }}" class="text-decoration-none text-reset">
                                            {{ $reservation->facility->name }}
                                        </a>
                                    </h2>
                                </div>

                                <div class="reservasi-meta mb-2">
                                    <span>{{ $reservation->reservation_date->format('d/m/Y') }}</span>
                                    <span>•</span>
                                    <span>{{ $reservation->timeRange() }} WIB</span>
                                    <span>•</span>
                                    <span>{{ $reservation->facility->location }}</span>
                                </div>

                                <p class="text-secondary mb-0">{{ Str::limit($reservation->purpose, 150) }}</p>
                            </div>

                            <div class="d-flex flex-column align-items-md-end gap-2">
                                <x-status-badge :status="$reservation->status" />
                                <a href="{{ route('reservations.show', $reservation) }}" class="detail-link">
                                    Lihat detail
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-4">{{ $reservations->links() }}</div>
    @endif
</div>
@endsection
