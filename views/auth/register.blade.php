@extends('layouts.app')

@section('title', 'Daftar Akun — '.config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-semibold">Daftar Akun</div>

                <div class="card-body">
                    <p class="text-muted small">
                        Akun baru akan diperiksa admin terlebih dahulu. Anda bisa masuk setelah akun disetujui.
                    </p>

                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">Nama Lengkap</label>
                            <div class="col-md-6">
                                <input id="name" type="text" name="name" value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       required maxlength="255" autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">Alamat Email</label>
                            <div class="col-md-6">
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       required maxlength="255" autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="user_type" class="col-md-4 col-form-label text-md-end">Jenis Pengguna</label>
                            <div class="col-md-6">
                                <select id="user_type" name="user_type"
                                        class="form-select @error('user_type') is-invalid @enderror" required>
                                    <option value="">— Pilih jenis pengguna —</option>
                                    @foreach (\App\Models\User::USER_TYPES as $nilai => $label)
                                        <option value="{{ $nilai }}" @selected(old('user_type') === $nilai)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('user_type')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="identity_number" class="col-md-4 col-form-label text-md-end">
                                NIM / NIP <span class="text-muted fw-normal">(opsional)</span>
                            </label>
                            <div class="col-md-6">
                                <input id="identity_number" type="text" name="identity_number"
                                       value="{{ old('identity_number') }}"
                                       class="form-control @error('identity_number') is-invalid @enderror"
                                       maxlength="30">
                                <div class="form-text">Maksimal 30 karakter. Memudahkan admin memverifikasi akun Anda.</div>
                                @error('identity_number')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">Kata Sandi</label>
                            <div class="col-md-6">
                                <input id="password" type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required minlength="8" autocomplete="new-password">
                                <div class="form-text">Minimal 8 karakter.</div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Ulangi Kata Sandi</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" name="password_confirmation"
                                       class="form-control" required minlength="8" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">Daftar</button>
                                <a href="/login" class="btn btn-link">Sudah punya akun? Masuk</a>
                            </div>
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
    document.querySelector('form[action="{{ route('register') }}"]').addEventListener('submit', function (e) {
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
