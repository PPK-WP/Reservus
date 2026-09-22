@php
    $facility = $facility ?? null;
    $action = $facility ? route('admin.facilities.update', $facility) : route('admin.facilities.store');
    $method = $facility ? 'PUT' : 'POST';
    $judul = $facility ? 'Edit Fasilitas' : 'Tambah Fasilitas';
    $namaLama = old('name', $facility?->name ?? '');
    $tipeLama = old('type', $facility?->type ?? '');
    $lokasiLama = old('location', $facility?->location ?? '');
    $kapasitasLama = old('capacity', $facility?->capacity ?? '');
    $deskripsiLama = old('description', $facility?->description ?? '');
@endphp

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ $action }}" novalidate>
            @csrf
            @method($method)

            <div class="row g-3">
                <div class="col-md-8">
                    <label for="name" class="form-label">Nama fasilitas <span class="text-danger">*</span></label>
                    <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ $namaLama }}" required maxlength="100" placeholder="mis. Lab Jaringan">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="type" class="form-label">Tipe <span class="text-danger">*</span></label>
                    <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="" disabled @selected($tipeLama === '')>Pilih tipe…</option>
                        @foreach (\App\Models\Facility::TYPES as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($tipeLama === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-8">
                    <label for="location" class="form-label">Lokasi <span class="text-danger">*</span></label>
                    <input id="location" type="text" name="location" class="form-control @error('location') is-invalid @enderror"
                        value="{{ $lokasiLama }}" required maxlength="100" placeholder="mis. Gedung B">
                    @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="capacity" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                    <input id="capacity" type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror"
                        value="{{ $kapasitasLama }}" required min="1" max="9999" inputmode="numeric">
                    @error('capacity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                        rows="3" maxlength="1000" placeholder="Opsional, mis. fasilitas dan perlengkapan yang tersedia.">{{ $deskripsiLama }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/admin/facilities" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>