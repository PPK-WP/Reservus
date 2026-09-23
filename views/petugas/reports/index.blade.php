@extends('layouts.app')

@section('title', 'Antrian Laporan — '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="h3 mb-1">Antrian Laporan</h1>
        <p class="text-muted mb-0">Kelola semua laporan kerusakan dan masalah fasilitas.</p>
    </div>

    {{-- Tab status --}}
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'baru' ? 'active' : '' }}"
               href="{{ route('petugas.reports.index', ['status' => 'baru']) }}">Baru</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'diproses' ? 'active' : '' }}"
               href="{{ route('petugas.reports.index', ['status' => 'diproses']) }}">Diproses</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'selesai' ? 'active' : '' }}"
               href="{{ route('petugas.reports.index', ['status' => 'selesai']) }}">Selesai</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'ditolak' ? 'active' : '' }}"
               href="{{ route('petugas.reports.index', ['status' => 'ditolak']) }}">Ditolak</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'semua' ? 'active' : '' }}"
               href="{{ route('petugas.reports.index', ['status' => 'semua']) }}">Semua</a>
        </li>
    </ul>

    @if ($reports->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">Tidak ada laporan pada tab ini.</p>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Pelapor</th>
                            <th>Fasilitas</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Foto</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>{{ $report->user->name }}</td>
                                <td>{{ $report->facility->name }}</td>
                                <td>
                                    <span class="badge text-bg-light text-dark">
                                        {{ \App\Models\Report::CATEGORIES[$report->category] ?? $report->category }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($report->description, 60) }}</td>
                                <td class="text-center">
                                    @if ($report->photo)
                                        <span title="Ada foto">📷</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td><x-status-badge :status="$report->status" /></td>
                                <td class="small">{{ $report->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('petugas.reports.show', $report) }}"
                                       class="btn btn-sm btn-outline-primary">Detail</a>
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
