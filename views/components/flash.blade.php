{{-- Pesan flash & ringkasan error validasi. Sudah dipasang di layouts.app (E4).
     Ikon menemani warna supaya status tidak hanya dibedakan warna (DESIGN.md §7). --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-start gap-2" role="alert">
        <x-ikon nama="check-circle" class="mt-1" />
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-start gap-2" role="alert">
        <x-ikon nama="exclamation-triangle" class="mt-1" />
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if (session('status'))
    <div class="alert alert-info alert-dismissible fade show d-flex align-items-start gap-2" role="alert">
        <x-ikon nama="info-circle" class="mt-1" />
        <div>{{ session('status') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
        <x-ikon nama="exclamation-triangle" class="mt-1" />
        <div>
            <strong>Periksa kembali isian Anda:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
