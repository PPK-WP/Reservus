@extends('layouts.app')

@section('title', 'Rekap Fasilitas — '.config('app.name'))

@php
    $kategori = array_keys(\App\Models\Report::CATEGORIES);
    $referensi = $filter['group_by'] . '|' . $filter['from'] . '|' . $filter['to'];
@endphp

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Rekap Fasilitas</h1>
            <p class="text-muted mb-0">Okupansi reservasi disetujui dan frekuensi laporan per fasilitas/lokasi. Tampilan hanya-baca.</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="/admin/recap" class="row g-2 align-items-end">
                <div class="col-sm-3">
                    <label for="from" class="form-label small text-muted mb-1">Dari tanggal</label>
                    <input id="from" type="date" name="from" class="form-control" value="{{ $filter['from'] }}" maxlength="10">
                </div>
                <div class="col-sm-3">
                    <label for="to" class="form-label small text-muted mb-1">Sampai tanggal</label>
                    <input id="to" type="date" name="to" class="form-control" value="{{ $filter['to'] }}" maxlength="10">
                </div>
                <div class="col-sm-3">
                    <label for="group_by" class="form-label small text-muted mb-1">Kelompokkan per</label>
                    <select id="group_by" name="group_by" class="form-select">
                        <option value="facility" @selected($filter['group_by'] === 'facility')>Fasilitas</option>
                        <option value="location" @selected($filter['group_by'] === 'location')>Lokasi (gedung)</option>
                    </select>
                </div>
                <div class="col-sm-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">Terapkan</button>
                    <a href="/admin/recap" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
            <p class="text-muted small mt-3 mb-0">
                Okupansi = total jam reservasi <strong>disetujui</strong> ÷ (13 jam × {{ $rekap['jumlah_hari'] }} hari) × 100.
                @if ($filter['group_by'] === 'location')
                    Untuk grup lokasi, dibagi juga jumlah fasilitas aktif di lokasi tersebut.
                @endif
            </p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        @foreach ($rekap['header'] as $kolom)
                            <th>{{ $kolom }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rekap['baris'] as $baris)
                        <tr>
                            <td class="fw-semibold">{{ $baris['kelompok'] }}</td>
                            <td>{{ $baris['lokasi'] }}</td>
                            <td class="text-end">{{ $baris['reservasi'] }}</td>
                            <td class="text-end">{{ $baris['jam'] }}</td>
                            <td class="text-end">{{ $baris['persen'] }}</td>
                            <td class="text-end">{{ $baris['laporan'] }}</td>
                            @foreach ($kategori as $kat)
                                <td class="text-end">{{ $baris['k_'.$kat] }}</td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                Tidak ada data reservasi atau laporan pada rentang ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="table-light fw-semibold">
                        <td>{{ $rekap['totals']['kelompok'] }}</td>
                        <td>{{ $rekap['totals']['lokasi'] }}</td>
                        <td class="text-end">{{ $rekap['totals']['reservasi'] }}</td>
                        <td class="text-end">{{ $rekap['totals']['jam'] }}</td>
                        <td class="text-end">{{ $rekap['totals']['persen'] }}</td>
                        <td class="text-end">{{ $rekap['totals']['laporan'] }}</td>
                        @foreach ($kategori as $kat)
                            <td class="text-end">{{ $rekap['totals']['k_'.$kat] }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    @if ($rekap['baris'] !== [])
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted small">Ekspor (format sama dengan tabel):</span>
            <a href="{{ route('admin.recap.export', ['format' => 'csv', 'from' => $filter['from'], 'to' => $filter['to'], 'group_by' => $filter['group_by']]) }}" class="btn btn-sm btn-outline-success">CSV</a>
            <a href="{{ route('admin.recap.export', ['format' => 'xlsx', 'from' => $filter['from'], 'to' => $filter['to'], 'group_by' => $filter['group_by']]) }}" class="btn btn-sm btn-outline-success">XLSX</a>
            <a href="{{ route('admin.recap.export', ['format' => 'pdf', 'from' => $filter['from'], 'to' => $filter['to'], 'group_by' => $filter['group_by']]) }}" class="btn btn-sm btn-outline-danger">PDF</a>
        </div>
    @endif
</div>
@endsection