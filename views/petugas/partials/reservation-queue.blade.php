@php
    $pendingReservations = \App\Models\Reservation::with(['user', 'facility'])
        ->where('status', 'menunggu')
        ->latest('reservation_date')
        ->latest('start_time')
        ->limit(5)
        ->get();
@endphp

@if ($pendingReservations->isEmpty())
    <div class="text-center py-3">
        <span class="ikon-kotak mb-3" aria-hidden="true">
            <x-ikon nama="inbox" ukuran="1.3rem" />
        </span>
        <p class="text-secondary mb-0">Tidak ada reservasi yang menunggu persetujuan.</p>
    </div>
@else
    <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
        <p class="small text-secondary mb-0">Pengajuan terbaru yang perlu ditinjau</p>
        <span class="badge text-bg-warning">{{ $pendingReservations->count() }} menunggu</span>
    </div>

    <div class="list-group list-group-flush">
        @foreach ($pendingReservations as $reservation)
            <a href="{{ route('petugas.reservations.show', $reservation) }}"
               class="list-group-item list-group-item-action px-0 py-3">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div class="min-w-0">
                        <strong class="d-block text-truncate">{{ $reservation->facility->name }}</strong>
                        <span class="small text-secondary d-block text-truncate">
                            {{ $reservation->user->name }} · {{ $reservation->timeRange() }} WIB
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <small class="text-secondary">{{ $reservation->reservation_date->format('d/m/Y') }}</small>
                        <x-ikon nama="arrow-right" />
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endif
