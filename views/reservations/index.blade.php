@extends('layouts.app')

@section('title', 'Reservasi Saya | '.config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Reservasi Saya</h1>
            <p class="text-secondary mb-0">Pantau seluruh pengajuan dan status reservasi fasilitas Anda.</p>
        </div>
        <a href="{{ route('reservations.create') }}" class="btn btn-primary flex-shrink-0">
            <span class="d-inline-flex align-items-center gap-2">
                <x-ikon nama="calendar-check" />
                <span>Ajukan reservasi</span>
            </span>
        </a>
    </div>

    <div class="overflow-auto mb-3" aria-label="Saring reservasi berdasarkan status">
        <ul class="nav nav-pills flex-nowrap gap-1 pb-1">
            <li class="nav-item">
                <a class="nav-link text-nowrap {{ !$currentStatus ? 'active' : '' }}"
                   href="{{ route('reservations.index') }}"
                   @if (!$currentStatus) aria-current="page" @endif>Semua</a>
            </li>
            @foreach ($statuses as $value => $label)
                <li class="nav-item">
                    <a class="nav-link text-nowrap {{ $currentStatus === $value ? 'active' : '' }}"
                       href="{{ route('reservations.index', ['status' => $value]) }}"
                       @if ($currentStatus === $value) aria-current="page" @endif>{{ $label }}</a>
                </li>
            @endforeach
        </ul>
    </div>

    @if ($reservations->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5 px-3">
                <span class="ikon-kotak mb-3" aria-hidden="true">
                    <x-ikon nama="calendar-check" ukuran="1.4rem" />
                </span>
                @if ($currentStatus)
                    <h2 class="h6 mb-2">Belum ada reservasi dengan status ini</h2>
                    <p class="text-secondary mb-3">Pilih status lain untuk melihat riwayat reservasi Anda.</p>
                    <a href="{{ route('reservations.index') }}" class="btn btn-outline-primary">Lihat semua reservasi</a>
                @else
                    <h2 class="h6 mb-2">Belum ada reservasi</h2>
                    <p class="text-secondary mb-3">Pilih fasilitas di katalog untuk mulai mengajukan reservasi.</p>
                    <a href="/facilities" class="btn btn-outline-primary">Lihat katalog</a>
                @endif
            </div>
        </div>
    @else
        <div class="vstack gap-3">
            @foreach ($reservations as $reservation)
                <article class="card card-interaktif position-relative">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3">
                            <div class="min-w-0">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                    <h2 class="h6 mb-0">
                                        <a href="{{ route('reservations.show', $reservation) }}"
                                           class="stretched-link text-decoration-none">
                                            {{ $reservation->facility->name }}
                                        </a>
                                    </h2>
                                    <span class="small text-secondary">#{{ $reservation->id }}</span>
                                </div>

                                <div class="d-flex flex-wrap gap-2 gap-md-3 small text-secondary mb-3">
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <x-ikon nama="calendar-check" />
                                        {{ $reservation->reservation_date->format('d/m/Y') }}
                                    </span>
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <x-ikon nama="clock-history" />
                                        {{ $reservation->timeRange() }} WIB
                                    </span>
                                </div>

                                <p class="mb-0 text-body-secondary">{{ Str::limit($reservation->purpose, 140) }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <x-status-badge :status="$reservation->status" />
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
