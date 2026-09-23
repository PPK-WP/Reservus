@extends('layouts.app')

@section('title', 'Reservasi Saya — '.config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Reservasi Saya</h1>
            <p class="text-muted mb-0">Riwayat pengajuan penggunaan fasilitas kampus.</p>
        </div>
        <a href="{{ route('reservations.create') }}" class="btn btn-primary">Ajukan Reservasi</a>
    </div>

    <ul class="nav nav-pills mb-3">
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

    @if ($reservations->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-3">Belum ada reservasi.</p>
                <a href="{{ route('reservations.create') }}" class="btn btn-outline-primary">Ajukan Reservasi</a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach ($reservations as $reservation)
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <h2 class="h6 mb-1">
                                        <a href="{{ route('reservations.show', $reservation) }}" class="text-decoration-none">
                                            {{ $reservation->facility->name }}
                                        </a>
                                    </h2>
                                    <div class="text-muted small">
                                        {{ $reservation->reservation_date->format('d/m/Y') }} · {{ $reservation->timeRange() }} WIB
                                    </div>
                                    <p class="small mb-0 mt-2">{{ Str::limit($reservation->purpose, 140) }}</p>
                                </div>
                                <x-status-badge :status="$reservation->status" />
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $reservations->links() }}</div>
    @endif
</div>
@endsection
