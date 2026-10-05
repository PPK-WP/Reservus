@extends('layouts.app')

@section('title', 'Antrian Reservasi | '.config('app.name'))

@push('styles')
<style>
    .tabel-antrean-reservasi { min-width: 960px; }

    .inisial-pemohon {
        width: 2.35rem;
        height: 2.35rem;
        font-size: .8125rem;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Antrian Reservasi</h1>
            <p class="text-secondary mb-0">Periksa jadwal dan tentukan keputusan untuk setiap pengajuan.</p>
        </div>
        <a href="/petugas" class="btn btn-outline-secondary flex-shrink-0">Kembali ke dashboard</a>
    </div>

    <div class="overflow-auto mb-3" aria-label="Pilih kelompok reservasi">
        <ul class="nav nav-pills flex-nowrap gap-1 pb-1">
            <li class="nav-item">
                <a class="nav-link text-nowrap {{ $tab === 'menunggu' ? 'active' : '' }}"
                   href="{{ route('petugas.reservations.index', ['tab' => 'menunggu']) }}"
                   @if ($tab === 'menunggu') aria-current="page" @endif>Menunggu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-nowrap {{ $tab === 'disetujui' ? 'active' : '' }}"
                   href="{{ route('petugas.reservations.index', ['tab' => 'disetujui']) }}"
                   @if ($tab === 'disetujui') aria-current="page" @endif>Disetujui mendatang</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-nowrap {{ $tab === 'semua' ? 'active' : '' }}"
                   href="{{ route('petugas.reservations.index', ['tab' => 'semua']) }}"
                   @if ($tab === 'semua') aria-current="page" @endif>Semua</a>
            </li>
        </ul>
    </div>

    @if ($reservations->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5 px-3">
                <span class="ikon-kotak mb-3" aria-hidden="true">
                    <x-ikon nama="inbox" ukuran="1.4rem" />
                </span>
                <h2 class="h6 mb-2">Tidak ada reservasi pada kelompok ini</h2>
                <p class="text-secondary mb-0">Pengajuan akan muncul di sini saat tersedia.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                <div>
                    <h2 class="h6 mb-1">Daftar reservasi</h2>
                    <p class="small text-secondary mb-0">Reservasi menunggu ditandai dengan latar kobalt muda.</p>
                </div>
                <span class="badge text-bg-light border text-dark fw-medium">{{ $reservations->total() }} data</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 tabel-antrean-reservasi">
                    <thead class="table-light small fw-semibold">
                        <tr>
                            <th scope="col">Pemohon</th>
                            <th scope="col">Fasilitas</th>
                            <th scope="col">Jadwal</th>
                            <th scope="col">Tujuan</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reservations as $reservation)
                            <tr class="{{ $reservation->status === 'menunggu' ? 'table-row-aktif' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="inisial-pemohon rounded-circle border bg-white text-primary d-inline-flex align-items-center justify-content-center fw-bold flex-shrink-0" aria-hidden="true">
                                            {{ Str::upper(Str::substr($reservation->user->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <div class="fw-semibold">{{ $reservation->user->name }}</div>
                                            <small class="text-secondary">{{ $reservation->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $reservation->facility->name }}</div>
                                    <small class="text-secondary">#{{ $reservation->id }} · {{ $reservation->facility->location }}</small>
                                </td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-2 bg-kobalt-muda rounded-2 px-2 py-1">
                                        <x-ikon nama="calendar-check" />
                                        <span>
                                            <span class="d-block fw-semibold">{{ $reservation->reservation_date->format('d/m/Y') }}</span>
                                            <small class="text-secondary">{{ $reservation->timeRange() }} WIB</small>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-body-secondary">{{ Str::limit($reservation->purpose, 70) }}</td>
                                <td>
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        <x-status-badge :status="$reservation->status" />
                                        @if ($reservation->potential_conflict)
                                            <span class="badge text-bg-warning d-inline-flex align-items-center gap-1">
                                                <x-ikon nama="exclamation-triangle" />
                                                Berpotensi bentrok
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('petugas.reservations.show', $reservation) }}"
                                       class="btn btn-sm btn-outline-primary text-nowrap"
                                       aria-label="Buka detail reservasi nomor {{ $reservation->id }}">
                                        Tinjau detail
                                    </a>
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
