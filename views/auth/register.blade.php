@extends('layouts.app')

@section('title', 'Daftar Akun | '.config('app.name'))

@section('content')
<div class="container">
    <div class="card auth-kartu mx-auto overflow-hidden">
        <div class="row g-0">
            {{-- Foto kampus: hanya di layar ≥ lg, tanpa lapisan warna (DESIGN.md §6.2) --}}
            <div class="col-lg-5 d-none d-lg-block position-relative auth-foto">
                <img src="{{ asset('images/brand/monumen-undip.webp') }}"
                     alt="Monumen bertuliskan Universitas Diponegoro di halaman kampus"
                     class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
            </div>

            <div class="col-lg-7 bg-white">
                <div class="px-4 py-5 px-md-5">
                    <img src="{{ asset('images/brand/undip-logo.png') }}" alt="Logo Universitas Diponegoro"
                         height="72" class="mb-4">

                    <h1 class="h3 text-kobalt-tua mb-1">Daftar akun Reservus</h1>
                    <p class="text-secondary mb-4 teks-ringkas">
                        Untuk mahasiswa, dosen, dan staf. Akun baru diperiksa admin lebih dulu sebelum bisa dipakai masuk.
                    </p>

                    {{-- Alur pendaftaran dibuat terlihat (heuristik #1 visibilitas status) --}}
                    <ol class="list-unstyled d-flex flex-wrap gap-2 mb-4" aria-label="Alur pendaftaran">
                        <li class="badge badge-langkah rounded-pill text-bg-primary">1 · Isi data</li>
                        <li class="badge badge-langkah rounded-pill bg-kobalt-muda text-primary">2 · Diperiksa admin</li>
                        <li class="badge badge-langkah rounded-pill bg-kobalt-muda text-primary">3 · Masuk setelah disetujui</li>
                    </ol>

                    <form method="POST" action="{{ route('register') }}" id="form-daftar" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama lengkap</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   required maxlength="255" autocomplete="name" autofocus>
                            @error('name')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required maxlength="255" autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label for="user_type" class="form-label">Jenis pengguna</label>
                                <select id="user_type" name="user_type"
                                        class="form-select @error('user_type') is-invalid @enderror" required>
                                    <option value="">Pilih jenis pengguna</option>
                                    @foreach (\App\Models\User::USER_TYPES as $nilai => $label)
                                        <option value="{{ $nilai }}" @selected(old('user_type') === $nilai)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('user_type')
                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-sm-6">
                                <label for="identity_number" class="form-label">
                                    NIM / NIP <span class="text-secondary fw-normal">(opsional)</span>
                                </label>
                                <input id="identity_number" type="text" name="identity_number"
                                       value="{{ old('identity_number') }}"
                                       class="form-control @error('identity_number') is-invalid @enderror"
                                       maxlength="30" inputmode="numeric" pattern="[0-9]*"
                                       title="NIM/NIP hanya boleh berisi angka." aria-describedby="identity-bantuan">
                                <div id="identity-bantuan" class="form-text">Hanya angka, maks. 30 digit. Mempercepat verifikasi.</div>
                                @error('identity_number')
                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label for="password" class="form-label">Kata sandi</label>
                                <input id="password" type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required minlength="8" autocomplete="new-password" aria-describedby="sandi-bantuan">
                                <div id="sandi-bantuan" class="form-text">Minimal 8 karakter.</div>
                                @error('password')
                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-sm-6">
                                <label for="password-confirm" class="form-label">Ulangi kata sandi</label>
                                <input id="password-confirm" type="password" name="password_confirmation"
                                       class="form-control" required minlength="8" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <button type="submit" class="btn btn-primary px-4">Daftar</button>
                            <span class="text-secondary">Sudah punya akun? <a href="/login" class="fw-semibold">Masuk</a></span>
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
    // Pengecekan ringan di sisi client; validasi sesungguhnya tetap di server.
    document.getElementById('form-daftar').addEventListener('submit', function (e) {
        const sandi = document.getElementById('password');
        const ulangi = document.getElementById('password-confirm');

        if (sandi.value !== ulangi.value) {
            e.preventDefault();
            ulangi.setCustomValidity('Ulangi kata sandi harus sama dengan kata sandi.');
            ulangi.reportValidity();
            return;
        }

        ulangi.setCustomValidity('');

        if (!this.checkValidity()) {
            e.preventDefault();
            this.reportValidity();
        }
    });
</script>
@endpush
