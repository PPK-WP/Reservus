@extends('layouts.app')

@section('title', 'Buat Akun | '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="/admin/users" class="d-inline-flex align-items-center gap-2 small text-decoration-none mb-3">
            <span aria-hidden="true">&larr;</span>
            <span>Kembali ke kelola user</span>
        </a>
        <h1 class="h3 mb-1">Buat Akun Baru</h1>
        <p class="text-secondary mb-0">Tambahkan akun petugas atau pengguna yang langsung aktif.</p>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <form method="POST" action="/admin/users" id="form-buat-akun" class="card" novalidate>
                @csrf

                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="ikon-kotak flex-shrink-0" aria-hidden="true">
                            <x-ikon nama="people" ukuran="1.2rem" />
                        </span>
                        <div>
                            <h2 class="h6 mb-1">Informasi akun</h2>
                            <p class="small text-secondary mb-0">Kolom bertanda wajib harus diisi.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <section aria-labelledby="bagian-identitas-akun">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-pill bg-kobalt-muda text-primary">1</span>
                            <h3 id="bagian-identitas-akun" class="h6 mb-0">Identitas utama</h3>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-medium">Nama lengkap</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   required maxlength="255" autocomplete="name" autofocus>
                            @error('name')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="email" class="form-label fw-medium">Alamat email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required maxlength="255" autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </section>

                    <section class="border-top mt-4 pt-4" aria-labelledby="bagian-akses-akun">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-pill bg-kobalt-muda text-primary">2</span>
                            <h3 id="bagian-akses-akun" class="h6 mb-0">Peran dan identitas kampus</h3>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label fw-medium">Peran</label>
                            <select id="role" name="role"
                                    class="form-select @error('role') is-invalid @enderror"
                                    aria-describedby="role-help" required>
                                <option value="">Pilih peran</option>
                                @foreach ($peranPilihan as $nilai => $label)
                                    <option value="{{ $nilai }}" @selected(old('role') === $nilai)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div id="role-help" class="form-text">Peran admin tidak dapat dibuat dari halaman ini.</div>
                            @error('role')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="baris-jenis">
                            <label for="user_type" class="form-label fw-medium">Jenis pengguna</label>
                            <select id="user_type" name="user_type"
                                    class="form-select @error('user_type') is-invalid @enderror"
                                    aria-describedby="user-type-help">
                                <option value="">Pilih jenis pengguna</option>
                                @foreach (\App\Models\User::USER_TYPES as $nilai => $label)
                                    <option value="{{ $nilai }}" @selected(old('user_type') === $nilai)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div id="user-type-help" class="form-text">Wajib diisi bila perannya pengguna.</div>
                            @error('user_type')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="identity_number" class="form-label fw-medium">
                                NIM / NIP <span class="text-secondary fw-normal">(opsional)</span>
                            </label>
                            <input id="identity_number" type="text" name="identity_number"
                                   value="{{ old('identity_number') }}"
                                   class="form-control @error('identity_number') is-invalid @enderror"
                                   maxlength="30" inputmode="numeric" pattern="[0-9]*"
                                   title="NIM/NIP hanya boleh berisi angka.">
                            <div class="form-text">Hanya angka, maks. 30 digit.</div>
                            @error('identity_number')
                                <div class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>
                    </section>

                    <section class="border-top mt-4 pt-4" aria-labelledby="bagian-keamanan-akun">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="badge rounded-pill bg-kobalt-muda text-primary">3</span>
                            <h3 id="bagian-keamanan-akun" class="h6 mb-0">Keamanan masuk</h3>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-medium">Kata sandi</label>
                                <input id="password" type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required minlength="8" autocomplete="new-password"
                                       aria-describedby="password-help">
                                <div id="password-help" class="form-text">Minimal 8 karakter.</div>
                                @error('password')
                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password-confirm" class="form-label fw-medium">Ulangi kata sandi</label>
                                <input id="password-confirm" type="password" name="password_confirmation"
                                       class="form-control" required minlength="8" autocomplete="new-password">
                            </div>
                        </div>
                    </section>
                </div>

                <div class="card-footer bg-white d-flex flex-column-reverse flex-sm-row justify-content-end align-items-stretch align-items-sm-center gap-2 py-3">
                    <a href="/admin/users" class="btn btn-link text-decoration-none">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan akun</button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <aside class="card border-top border-primary border-3" aria-labelledby="info-akun-baru">
                <div class="card-body p-3 p-md-4">
                    <span class="ikon-kotak mb-3" aria-hidden="true">
                        <x-ikon nama="shield-lock" ukuran="1.35rem" />
                    </span>
                    <h2 id="info-akun-baru" class="h6 mb-3">Akses akun baru</h2>
                    <div class="bg-kobalt-muda rounded-3 p-3 mb-3">
                        <span class="small text-secondary d-block mb-1">Status awal</span>
                        <strong class="text-primary">Aktif setelah disimpan</strong>
                    </div>
                    <ul class="small text-secondary ps-3 mb-0 vstack gap-2">
                        <li>Akun petugas hanya dapat dibuat oleh admin.</li>
                        <li>Jenis pengguna hanya diperlukan untuk peran pengguna.</li>
                        <li>Peran admin tidak tersedia pada formulir ini.</li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Jenis pengguna hanya relevan untuk peran pengguna; server tetap memvalidasi ulang.
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
    })();
</script>
@endpush
