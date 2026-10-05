@php
    $pendingReservations = \App\Models\Reservation::with(['user', 'facility'])
        ->where('status', 'menunggu')
        ->latest('reservation_date')
        ->latest('start_time')
        ->limit(5)
        ->get();
@endphp

@if ($pendingReservations->isEmpty())
    <div class="text-muted mb-0">Tidak ada reservasi menunggu.</div>
@else
    <div class="petugas-widget">
        @foreach ($pendingReservations as $reservation)
            <a href="{{ route('petugas.reservations.show', $reservation) }}" class="petugas-widget-item">
                <div class="item-top">
                    <strong>{{ $reservation->facility->name }}</strong>
                    <span class="badge text-bg-warning">Menunggu</span>
                </div>
                <div class="small text-muted mb-1">{{ $reservation->user->name }}</div>
                <small>{{ $reservation->reservation_date->format('d/m/Y') }} · {{ $reservation->timeRange() }} WIB</small>
            </a>
        @endforeach
    </div>
@endif
