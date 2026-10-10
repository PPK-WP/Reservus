@extends('layouts.app')

@section('title', 'Antrian Laporan | '.config('app.name'))

@push('styles')
<style>
    .tabel-antrean-laporan { min-width: 960px; }

    .inisial-pelapor {
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
            <h1 class="h3 mb-1">Antrian Laporan</h1>
            <p class="text-secondary mb-0">Kelola semua laporan kerusakan dan masalah fasilitas.</p>
        </div>
        <a href="/petugas" class="btn btn-outline-secondary flex-shrink-0">Kembali ke dashboard</a>
    </div>

    {{-- Tab status --}}
    <div class="overflow-auto mb-3" aria-label="Pilih kelompok laporan">
        <ul class="nav nav-pills flex-nowrap gap-1 pb-1">
            <li class="nav-item">
                <a class="nav-link text-nowrap {{ $tab === 'baru' ? 'active' : '' }}"
                   href="{{ route('petugas.reports.index', ['status' => 'baru']) }}"
                   @if ($tab === 'baru') aria-current="page" @endif>Baru</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-nowrap {{ $tab === 'diproses' ? 'active' : '' }}"
                   href="{{ route('petugas.reports.index', ['status' => 'diproses']) }}"
                   @if ($tab === 'diproses') aria-current="page" @endif>Diproses</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-nowrap {{ $tab === 'selesai' ? 'active' : '' }}"
                   href="{{ route('petugas.reports.index', ['status' => 'selesai']) }}"
                   @if ($tab === 'selesai') aria-current="page" @endif>Selesai</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-nowrap {{ $tab === 'ditolak' ? 'active' : '' }}"
                   href="{{ route('petugas.reports.index', ['status' => 'ditolak']) }}"
                   @if ($tab === 'ditolak') aria-current="page" @endif>Ditolak</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-nowrap {{ $tab === 'semua' ? 'active' : '' }}"
                   href="{{ route('petugas.reports.index', ['status' => 'semua']) }}"
                   @if ($tab === 'semua') aria-current="page" @endif>Semua</a>
            </li>
        </ul>
    </div>

    @if ($reports->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5 px-3">
                <span class="ikon-kotak mb-3" aria-hidden="true">
                    <x-ikon nama="inbox" ukuran="1.4rem" />
                </span>
                <h2 class="h6 mb-2">Tidak ada laporan pada kelompok ini</h2>
                <p class="text-secondary mb-0">Laporan akan muncul di sini saat tersedia.</p>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                <div>
                    <h2 class="h6 mb-1">Daftar laporan</h2>
                    <p class="small text-secondary mb-0">Laporan baru ditandai dengan latar kobalt muda.</p>
                </div>
                <span class="badge text-bg-light border text-dark fw-medium">{{ $reports->total() }} data</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 tabel-antrean-laporan">
                    <thead class="table-light small fw-semibold">
                        <tr>
                            <th scope="col">Pelapor</th>
                            <th scope="col">Fasilitas</th>
                            <th scope="col">Kategori & Tanggal</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr class="{{ $report->status === 'baru' ? 'table-row-aktif' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="inisial-pelapor rounded-circle border bg-white text-primary d-inline-flex align-items-center justify-content-center fw-bold flex-shrink-0" aria-hidden="true">
                                            {{ Str::upper(Str::substr($report->user->name, 0, 1)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <div class="fw-semibold">{{ $report->user->name }}</div>
                                            <small class="text-secondary">{{ $report->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $report->facility->name }}</div>
                                    <small class="text-secondary">#{{ $report->id }} · {{ $report->facility->location }}</small>
                                </td>
                                <td>
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        <span class="badge text-bg-light border fw-medium">
                                            {{ \App\Models\Report::CATEGORIES[$report->category] ?? $report->category }}
                                        </span>
                                        <small class="text-secondary">{{ $report->created_at->format('d/m/Y') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-body-secondary mb-1">{{ Str::limit($report->description, 70) }}</div>
                                    <div class="d-flex gap-2">
                                        <x-status-badge :status="$report->status" />
                                        @if ($report->photo)
                                            <span class="badge text-bg-light border text-secondary" title="Ada lampiran foto">
                                                📷 Foto
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('petugas.reports.show', $report) }}"
                                       class="btn btn-sm btn-outline-primary text-nowrap"
                                       aria-label="Buka detail laporan nomor {{ $report->id }}">
                                        Tinjau detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection
