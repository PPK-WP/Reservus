@extends('layouts.app')

@section('title', 'Buat Akun — '.config('app.name'))

@section('content')
<div class="container admin-shell">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="admin-user-form-card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h5 mb-0">Buat Akun Baru</h1>
                </div>

                <div class="card-body">
                    <p class="text-secondary small mb-4">
                        Akun yang dibuat di sini langsung berstatus aktif dan siap digunakan.
                        Pembuatan akun petugas hanya bisa dilakukan lewat halaman ini.
                    </p>

                    <form method="POST" action="/admin/users" id="form-buat-akun" class="admin-user-form" novalidate>
                        @csrf

                        <div class="row mb-3 align-items-center">
                            <label for="name" class="col-md-4 col-form-label row-label text-md-end">Nama Lengkap</label>
                            <div class="col-md-6">
                                <input id="name" type="text" name="name" value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       required maxlength="255" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label for="email" class="col-md-4 col-form-label row-label text-md-end">Alamat Email</label>
                            <div class="col-md-6">
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       required maxlength="255">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label for="role" class="col-md-4 col-form-label row-label text-md-end">Peran</label>
                            <div class="col-md-6">
                                <select id="role" name="role"
                                        class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">— Pilih peran —</option>
                                    @foreach ($peranPilihan as $nilai => $label)
                                        <option value="{{ $nilai }}" @selected(old('role') === $nilai)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Peran admin tidak dapat dibuat dari halaman ini.</div>
                                @error('role')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center" id="baris-jenis">
                            <label for="user_type" class="col-md-4 col-form-label row-label text-md-end">Jenis Pengguna</label>
                            <div class="col-md-6">
                                <select id="user_type" name="user_type"
                                        class="form-select @error('user_type') is-invalid @enderror">
                                    <option value="">— Pilih jenis pengguna —</option>
                                    @foreach (\App\Models\User::USER_TYPES as $nilai => $label)
                                        <option value="{{ $nilai }}" @selected(old('user_type') === $nilai)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Wajib diisi bila perannya pengguna.</div>
                                @error('user_type')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label for="identity_number" class="col-md-4 col-form-label row-label text-md-end">
                                NIM / NIP <span class="text-muted fw-normal">(opsional)</span>
                            </label>
                            <div class="col-md-6">
                                <input id="identity_number" type="text" name="identity_number"
                                       value="{{ old('identity_number') }}"
                                       class="form-control @error('identity_number') is-invalid @enderror"
                                       maxlength="30">
                                @error('identity_number')
                                    <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <label for="password" class="col-md-4 col-form-label row-label text-md-end">Kata Sandi</label>
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

                        <div class="row mb-4 align-items-center">
                            <label for="password-confirm" class="col-md-4 col-form-label row-label text-md-end">Ulangi Kata Sandi</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" name="password_confirmation"
                                       class="form-control" required minlength="8" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4 d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-primary">Simpan Akun</button>
                                <a href="/admin/users" class="btn btn-outline-secondary">Batal</a>
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
    (function () {
        const peran = document.getElementById('role');
        const jenis = document.getElementById('user_type');
        const baris = document.getElementById('baris-jenis');
        const form = document.getElementById('form-buat-akun');

        function sesuaikan() {
            const pengguna = peran.value === 'pengguna';
            baris.style.display = pengguna ? '' : 'none';
            jenis.required = pengguna;
            if (!pengguna) {
                jenis.value = '';
            }
        }

        peran.addEventListener('change', sesuaikan);
        sesuaikan();

        form.addEventListener('submit', function (e) {
            const sandi = document.getElementById('password');
            const ulangi = document.getElementById('password-confirm');

            if (sandi.value !== ulangi.value) {
                e.preventDefault();
                ulangi.setCustomValidity('Kata sandi tidak cocok.');
                ulangi.reportValidity();
                return;
            }

            ulangi.setCustomValidity('');

            if (!this.checkValidity()) {
                e.preventDefault();
                this.reportValidity();
            }
        });
    })();
</script>
@endpush
