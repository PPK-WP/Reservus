@extends('layouts.app')

@section('title', 'Buat Laporan — '.config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <h1 class="h3 mb-1">Buat Laporan</h1>
                <p class="text-muted mb-0">Laporkan kerusakan, kebersihan, atau masalah pada fasilitas kampus.</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" id="formLaporan">
                        @csrf

                        {{-- Fasilitas --}}
                        <div class="mb-3">
                            <label for="facility_id" class="form-label">Fasilitas <span class="text-danger">*</span></label>
                            <select name="facility_id" id="facility_id" class="form-select @error('facility_id') is-invalid @enderror" required>
                                <option value="">— Pilih Fasilitas —</option>
                                @foreach ($facilities as $facility)
                                    <option value="{{ $facility->id }}"
                                        {{ old('facility_id', $selectedFacility?->id) == $facility->id ? 'selected' : '' }}>
                                        {{ $facility->name }} · {{ \App\Models\Facility::TYPES[$facility->type] ?? $facility->type }} · {{ $facility->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('facility_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">— Pilih Kategori —</option>
                                @foreach ($categories as $value => $label)
                                    <option value="{{ $value }}" {{ old('category') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="5"
                                      class="form-control @error('description') is-invalid @enderror"
                                      required minlength="10" maxlength="2000"
                                      placeholder="Jelaskan masalah yang Anda temukan (min. 10 karakter)">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text"><span id="charCount">0</span>/2000 karakter</div>
                        </div>

                        {{-- Foto (opsional) --}}
                        <div class="mb-4">
                            <label for="photo" class="form-label">Foto (opsional)</label>
                            <input type="file" name="photo" id="photo"
                                   class="form-control @error('photo') is-invalid @enderror"
                                   accept="image/jpeg,image/png">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Format: JPG, JPEG, atau PNG. Maksimal 2 MB.</div>
                            <div id="photoPreview" class="mt-2 d-none">
                                <img id="previewImg" src="" alt="Preview" class="rounded" style="max-height: 200px;">
                            </div>
                            <div id="photoError" class="text-danger small mt-1 d-none"></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Kirim Laporan</button>
                        </div>
                    </form>
                </div>
            </div>
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
