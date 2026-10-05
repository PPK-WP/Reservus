@extends('layouts.app')

@section('title', 'Antrian Reservasi — '.config('app.name'))

@section('content')
<div class="container petugas-shell">
    <div class="petugas-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <p class="text-primary fw-semibold mb-2 text-uppercase small">Petugas</p>
            <h1 class="h3 mb-1">Antrian Reservasi</h1>
            <p class="text-secondary mb-0">Periksa dan proses pengajuan reservasi pengguna dengan prioritas yang jelas.</p>
        </div>
    </div>

    <div class="petugas-tabs mb-4">
        <ul class="nav nav-pills flex-wrap gap-2">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'menunggu' ? 'active' : '' }}"
                   href="{{ route('petugas.reservations.index', ['tab' => 'menunggu']) }}">Menunggu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'disetujui' ? 'active' : '' }}"
                   href="{{ route('petugas.reservations.index', ['tab' => 'disetujui']) }}">Disetujui Mendatang</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'semua' ? 'active' : '' }}"
                   href="{{ route('petugas.reservations.index', ['tab' => 'semua']) }}">Semua</a>
            </li>
        </ul>
    </div>

    @if ($reservations->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="text-muted">Tidak ada reservasi pada tab ini.</div>
            </div>
        </div>
    @else
        <div class="petugas-table-card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Pemohon</th>
                            <th>Fasilitas</th>
                            <th>Jadwal</th>
                            <th>Tujuan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservations as $reservation)
                            <tr class="petugas-queue-row {{ $reservation->status === 'menunggu' ? 'table-row-aktif' : '' }}">
                                <td>
                                    <div class="fw-semibold">{{ $reservation->user->name }}</div>
                                    <small class="text-muted">{{ $reservation->user->email }}</small>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $reservation->facility->name }}</div>
                                    <small class="text-muted">{{ $reservation->facility->location }}</small>
                                </td>
                                <td>
                                    <div>{{ $reservation->reservation_date->format('d/m/Y') }}</div>
                                    <small>{{ $reservation->timeRange() }} WIB</small>
                                </td>
                                <td>{{ Str::limit($reservation->purpose, 70) }}</td>
                                <td>
                                    <x-status-badge :status="$reservation->status" />
                                    @if ($reservation->potential_conflict)
                                        <div><span class="badge text-bg-warning mt-2">Berpotensi bentrok</span></div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('petugas.reservations.show', $reservation) }}"
                                       class="btn btn-sm btn-outline-primary">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">{{ $reservations->links() }}</div>
    @endif
</div>
@endsection
