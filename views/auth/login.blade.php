@extends('layouts.app')

@section('title', 'Masuk — '.config('app.name'))

@section('content')
<div class="container">
    <div class="card auth-kartu mx-auto overflow-hidden">
        <div class="row g-0">
            {{-- Foto kampus: hanya di layar ≥ lg, tanpa lapisan warna (DESIGN.md §6.2) --}}
            <div class="col-lg-6 d-none d-lg-block position-relative auth-foto">
                <img src="{{ asset('images/brand/kampus-tembalang.webp') }}"
                     alt="Patung Pangeran Diponegoro di depan gedung Dekanat Fakultas Teknik, kampus Undip Tembalang"
                     class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover">
                <span class="position-absolute bottom-0 start-0 m-2 px-2 py-1 rounded bg-white bg-opacity-75 small text-secondary">
                    Foto: Pinterest · kampus Tembalang
                </span>
            </div>

            <div class="col-lg-6 d-flex align-items-center bg-white">
                <div class="auth-form w-100 mx-auto px-4 py-5 px-md-5">
                    {{-- Logo resmi di atas latar putih, tinggi 72px (DESIGN.md §5.1) --}}
                    <img src="{{ asset('images/brand/undip-logo.png') }}" alt="Logo Universitas Diponegoro"
                         height="72" class="mb-4">

                    <h1 class="h3 text-kobalt-tua mb-1">Masuk ke Reservus</h1>
                    <p class="text-secondary mb-4">Reservasi dan laporkan fasilitas kampus dari satu tempat.</p>

                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required autocomplete="email" autofocus
                                   @error('email') aria-describedby="email-galat" @enderror>
                            @error('email')
                                <div id="email-galat" class="invalid-feedback" role="alert">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Kata sandi</label>
                            <div class="input-group has-validation">
                                <input id="password" type="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required autocomplete="current-password">
                                <button type="button" class="btn btn-outline-secondary" data-tampil-sandi="password"
                                        aria-label="Tampilkan kata sandi" aria-pressed="false">
                                    <x-ikon nama="eye" class="ikon-tampil" />
                                    <x-ikon nama="eye-slash" class="ikon-sembunyi d-none" />
                                </button>
                                @error('password')
                                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Masuk</button>
                    </form>

                    <p class="mt-4 mb-0 text-secondary">
                        Belum punya akun? <a href="/register" class="fw-semibold">Daftar di sini</a>
                    </p>
                    <p class="small text-secondary mt-2 mb-0">
                        Akun baru perlu disetujui admin sebelum bisa dipakai masuk.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tombol tampilkan/sembunyikan kata sandi (mencegah salah ketik, heuristik #5).
    document.querySelectorAll('[data-tampil-sandi]').forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            const isian = document.getElementById(tombol.dataset.tampilSandi);
            const tampil = isian.type === 'password';
            isian.type = tampil ? 'text' : 'password';
            tombol.setAttribute('aria-pressed', tampil ? 'true' : 'false');
            tombol.setAttribute('aria-label', tampil ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            tombol.querySelector('.ikon-tampil').classList.toggle('d-none', tampil);
            tombol.querySelector('.ikon-sembunyi').classList.toggle('d-none', !tampil);
        });
    });
</script>
@endpush
