@extends('layouts.app')

@section('title', 'Antrian Reservasi — '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="h3 mb-1">Antrian Reservasi</h1>
        <p class="text-muted mb-0">Periksa dan proses pengajuan reservasi pengguna.</p>
    </div>

    <ul class="nav nav-pills mb-3">
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

    @if ($reservations->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">Tidak ada reservasi pada tab ini.</p>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
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
                            <tr>
                                <td>
                                    <div>{{ $reservation->user->name }}</div>
                                    <small class="text-muted">{{ $reservation->user->email }}</small>
                                </td>
                                <td>
                                    <div>{{ $reservation->facility->name }}</div>
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
                                        <div><span class="badge text-bg-warning mt-1">Berpotensi bentrok</span></div>
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
