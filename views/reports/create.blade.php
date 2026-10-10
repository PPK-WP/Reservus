@extends('layouts.app')

@section('title', 'Buat Laporan | '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('reports.index') }}" class="d-inline-flex align-items-center gap-2 small text-decoration-none mb-3">
            <span aria-hidden="true">&larr;</span>
            <span>Kembali ke laporan saya</span>
        </a>
        <h1 class="h3 mb-1">Buat Laporan</h1>
        <p class="text-secondary mb-0">Laporkan kerusakan, kebersihan, atau masalah pada fasilitas kampus.</p>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" id="formLaporan" class="card">
                @csrf
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="ikon-kotak flex-shrink-0" aria-hidden="true">
                            <x-ikon nama="exclamation-triangle" ukuran="1.2rem" />
                        </span>
                        <div>
                            <h2 class="h6 mb-1">Informasi laporan</h2>
                            <p class="small text-secondary mb-0">Lengkapi rincian masalah fasilitas yang ditemukan.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    {{-- Fasilitas --}}
                    <div class="mb-4">
                        <label for="facility_id" class="form-label fw-medium">Fasilitas <span class="text-danger">*</span></label>
                        <select name="facility_id" id="facility_id" class="form-select @error('facility_id') is-invalid @enderror" required>
                            <option value="">— Pilih Fasilitas —</option>
                            @foreach ($facilities as $facility)
                                <option value="{{ $facility->id }}"
                                    {{ old('facility_id', $selectedFacility?->id) == $facility->id ? 'selected' : '' }}>
                                    {{ $facility->name }} · {{ \App\Models\Facility::TYPES[$facility->type] ?? $facility->type }} · {{ $facility->location }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih fasilitas yang mengalami kendala atau kerusakan.</div>
                        @error('facility_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="mb-4">
                        <label for="category" class="form-label fw-medium">Kategori <span class="text-danger">*</span></label>
                        <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="">— Pilih Kategori —</option>
                            @foreach ($categories as $value => $label)
                                <option value="{{ $value }}" {{ old('category') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih kategori masalah yang paling sesuai.</div>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <label for="description" class="form-label fw-medium">Deskripsi <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="5"
                                  class="form-control @error('description') is-invalid @enderror"
                                  required minlength="10" maxlength="2000"
                                  placeholder="Jelaskan masalah yang Anda temukan secara rinci (min. 10 karakter)">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text"><span id="charCount">0</span>/2000 karakter (minimal 10 karakter).</div>
                    </div>

                    {{-- Foto (opsional) --}}
                    <div class="mb-2">
                        <label for="photo" class="form-label fw-medium">Foto kendala (opsional)</label>
                        <input type="file" name="photo" id="photo"
                               class="form-control @error('photo') is-invalid @enderror"
                               accept="image/jpeg,image/png">
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Format: JPG, JPEG, atau PNG. Maksimal 2 MB. Lampirkan foto agar penanganan lebih cepat.</div>
                        <div id="photoPreview" class="mt-3 d-none">
                            <img id="previewImg" src="" alt="Pratinjau foto kendala" class="rounded border" style="max-height: 220px; max-width: 100%; object-fit: contain;">
                        </div>
                        <div id="photoError" class="text-danger small mt-1 d-none"></div>
                    </div>
                </div>

                <div class="card-footer bg-white d-flex flex-column-reverse flex-sm-row justify-content-end align-items-stretch align-items-sm-center gap-2 py-3">
                    <a href="{{ route('reports.index') }}" class="btn btn-link text-decoration-none">Batal</a>
                    <button type="submit" class="btn btn-primary">Kirim Laporan</button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <aside class="card border-top border-primary border-3" aria-labelledby="panduan-pelaporan">
                <div class="card-body p-3 p-md-4">
                    <span class="ikon-kotak mb-3" aria-hidden="true">
                        <x-ikon nama="info-circle" ukuran="1.35rem" />
                    </span>
                    <h2 id="panduan-pelaporan" class="h6 mb-3">Panduan Pelaporan</h2>
                    <ul class="small text-secondary ps-3 mb-0 vstack gap-2">
                        <li>Laporan akan segera ditinjau oleh petugas fasilitas terkait.</li>
                        <li>Sertakan deskripsi yang jelas dan spesifik terkait lokasi kerusakan.</li>
                        <li>Foto bukti mempercepat proses verifikasi dan perbaikan fasilitas.</li>
                        <li>Status perkembangan penanganan dapat dipantau di halaman Laporan Saya.</li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Hitung karakter deskripsi
    const desc = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    if (desc && charCount) {
        const updateCount = () => { charCount.textContent = desc.value.length; };
        desc.addEventListener('input', updateCount);
        updateCount();
    }

    // Preview foto & validasi ukuran client-side
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');
    const previewImg = document.getElementById('previewImg');
    const photoError = document.getElementById('photoError');

    if (photoInput) {
        photoInput.addEventListener('change', function () {
            photoPreview.classList.add('d-none');
            photoError.classList.add('d-none');

            if (this.files && this.files[0]) {
                const file = this.files[0];

                // Validasi tipe
                const allowedTypes = ['image/jpeg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    photoError.textContent = 'Format file harus JPG, JPEG, atau PNG.';
                    photoError.classList.remove('d-none');
                    this.value = '';
                    return;
                }

                // Validasi ukuran (2 MB = 2 * 1024 * 1024)
                if (file.size > 2 * 1024 * 1024) {
                    photoError.textContent = 'Ukuran foto melebihi 2 MB. Silakan pilih foto yang lebih kecil.';
                    photoError.classList.remove('d-none');
                    this.value = '';
                    return;
                }

                // Tampilkan preview
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    photoPreview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush
