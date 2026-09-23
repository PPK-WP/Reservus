@extends('layouts.app')

@section('title', 'Kelola Fasilitas — '.config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Kelola Fasilitas</h1>
            <p class="text-muted mb-0">Daftar fasilitas kampus beserta statusnya.</p>
        </div>
        <a href="/admin/facilities/create" class="btn btn-primary">+ Tambah Fasilitas</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="/admin/facilities" class="row g-2 align-items-end">
                <div class="col-sm-5">
                    <label for="q" class="form-label small text-muted mb-1">Cari nama / lokasi</label>
                    <input id="q" type="text" name="q" class="form-control" value="{{ $keyword }}" maxlength="100" placeholder="mis. R-101, Gedung A">
                </div>
                <div class="col-sm-4">
                    <label for="status" class="form-label small text-muted mb-1">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        @foreach (\App\Models\Facility::STATUSES as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filterStatus === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">Saring</button>
                    @if ($keyword !== '' || $filterStatus)
                        <a href="/admin/facilities" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Lokasi</th>
                        <th>Kapasitas</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($facilities as $facility)
                        <tr>
                            <td class="fw-semibold">{{ $facility->name }}</td>
                            <td>{{ \App\Models\Facility::TYPES[$facility->type] ?? $facility->type }}</td>
                            <td>{{ $facility->location }}</td>
                            <td>{{ $facility->capacity }}</td>
                            <td><x-status-badge :status="$facility->status" /></td>
                            <td class="text-end">
                                <a href="/admin/facilities/{{ $facility->id }}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                                <button type="button" class="btn btn-sm {{ $facility->status === 'nonaktif' ? 'btn-outline-success' : 'btn-outline-warning' }}"
                                    data-bs-toggle="modal" data-bs-target="#modalToggle"
                                    data-action="{{ route('admin.facilities.toggle', $facility) }}"
                                    data-name="{{ $facility->name }}"
                                    data-status="{{ $facility->status }}"
                                    data-warning="{{ $peringatan->get($facility->id, 0) }}"
                                    data-activate="{{ $facility->status === 'nonaktif' ? '1' : '0' }}">
                                    {{ $facility->status === 'nonaktif' ? 'Aktifkan' : 'Nonaktifkan' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Tidak ada fasilitas yang cocok dengan saringan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($facilities->hasPages())
            <div class="card-footer bg-white">{{ $facilities->links() }}</div>
        @endif
    </div>

    <p class="text-muted small mt-3 mb-0">
        Menonaktifkan fasilitas tidak menghapus data secara fisik (historis tetap tersimpan) dan
        tidak membatalkan reservasi yang sudah disetujui.
    </p>
</div>

<div class="modal fade" id="modalToggle" tabindex="-1" aria-labelledby="judulToggle" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="formToggle" action="">
            @csrf
            @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="judulToggle">Konfirmasi Perubahan Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p id="teksToggle" class="mb-0"></p>
                    <div id="peringatanToggle" class="alert alert-warning d-none mt-3 mb-0"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="tombolToggle" class="btn btn-warning">Ya, lanjutkan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('modalToggle');
    if (!modal) {
        return;
    }
    modal.addEventListener('show.bs.modal', function (event) {
        var tombol = event.relatedTarget;
        if (!tombol) {
            return;
        }
        var aktifkan = tombol.getAttribute('data-activate') === '1';
        var nama = tombol.getAttribute('data-name');
        var peringatan = parseInt(tombol.getAttribute('data-warning') || '0', 10);

        document.getElementById('formToggle').action = tombol.getAttribute('data-action');
        document.getElementById('judulToggle').textContent = aktifkan ? 'Aktifkan Fasilitas' : 'Nonaktifkan Fasilitas';

        var teks = document.getElementById('teksToggle');
        var kotakPeringatan = document.getElementById('peringatanToggle');
        var tombolKirim = document.getElementById('tombolToggle');

        if (aktifkan) {
            teks.textContent = 'Anda yakin mengaktifkan kembali fasilitas "' + nama + '"? Fasilitas aktif kembali dapat direservasi pengguna.';
            kotakPeringatan.classList.add('d-none');
            tombolKirim.className = 'btn btn-success';
            tombolKirim.textContent = 'Ya, Aktifkan';
        } else {
            teks.textContent = 'Fasilitas "' + nama + '" akan dinonaktifkan dan tidak lagi tampil di katalog publik.';
            if (peringatan > 0) {
                kotakPeringatan.textContent = 'PERHATIAN: terdapat ' + peringatan + ' reservasi yang sudah disetujui mulai hari ini atau nanti. Reservasi tersebut tetap berlaku seperti biasa.';
                kotakPeringatan.classList.remove('d-none');
            } else {
                kotakPeringatan.classList.add('d-none');
            }
            tombolKirim.className = 'btn btn-warning';
            tombolKirim.textContent = 'Ya, Nonaktifkan';
        }
    });
});
</script>
@endpush