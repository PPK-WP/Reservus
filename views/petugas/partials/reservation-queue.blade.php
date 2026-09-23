@php
    $pendingReservations = \App\Models\Reservation::with(['user', 'facility'])
        ->where('status', 'menunggu')
        ->latest('reservation_date')
        ->latest('start_time')
        ->limit(5)
        ->get();
@endphp

@if ($pendingReservations->isEmpty())
    <p class="text-muted mb-0">Tidak ada reservasi menunggu.</p>
@else
    <div class="list-group list-group-flush">
        @foreach ($pendingReservations as $reservation)
            <a href="{{ route('petugas.reservations.show', $reservation) }}" class="list-group-item list-group-item-action px-0">
                <div class="d-flex justify-content-between gap-2">
                    <strong>{{ $reservation->facility->name }}</strong>
                    <small>{{ $reservation->reservation_date->format('d/m/Y') }}</small>
                </div>
                <div class="small text-muted">{{ $reservation->user->name }} · {{ $reservation->timeRange() }} WIB</div>
            </a>
        @endforeach
    </div>
@endif
