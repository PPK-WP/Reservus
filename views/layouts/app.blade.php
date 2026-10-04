<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Reservus'))</title>

    <link rel="icon" href="{{ asset('images/brand/favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div id="app" class="d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-md navbar-light bg-white navbar-undip border-bottom">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/facilities">
                    <img src="{{ asset('images/brand/undip-logo.png') }}" alt="Logo Universitas Diponegoro" height="32">
                    <span>{{ config('app.name', 'Reservus') }}</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarUtama" aria-controls="navbarUtama"
                        aria-expanded="false" aria-label="Buka navigasi">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarUtama">
                    {{-- Menu kiri: URL literal, bukan route() milik SRS lain (E6) --}}
                    <ul class="navbar-nav me-auto">
                        @auth
                            @if (Auth::user()->isPetugas())
                                <x-nav-link href="/petugas" aktif="petugas">Dashboard</x-nav-link>
                                <x-nav-link href="/petugas/reservations" aktif="petugas/reservations*">Antrian Reservasi</x-nav-link>
                                <x-nav-link href="/petugas/reports" aktif="petugas/reports*">Antrian Laporan</x-nav-link>
                                <x-nav-link href="/facilities" aktif="facilities*">Fasilitas</x-nav-link>
                            @elseif (Auth::user()->isAdmin())
                                <x-nav-link href="/home" aktif="home">Beranda</x-nav-link>
                                <x-nav-link href="/admin/verifications" aktif="admin/verifications*">Verifikasi</x-nav-link>
                                <x-nav-link href="/admin/users" aktif="admin/users*">Kelola User</x-nav-link>
                                <x-nav-link href="/admin/facilities" aktif="admin/facilities*">Kelola Fasilitas</x-nav-link>
                                <x-nav-link href="/admin/recap" aktif="admin/recap*">Rekap</x-nav-link>
                                <x-nav-link href="/facilities" aktif="facilities*">Fasilitas</x-nav-link>
                            @else
                                <x-nav-link href="/home" aktif="home">Beranda</x-nav-link>
                                <x-nav-link href="/facilities" aktif="facilities*">Fasilitas</x-nav-link>
                                <x-nav-link href="/reservations" aktif="reservations*">Reservasi Saya</x-nav-link>
                                <x-nav-link href="/reports" aktif="reports*">Laporan Saya</x-nav-link>
                            @endif
                        @else
                            <x-nav-link href="/facilities" aktif="facilities*">Fasilitas</x-nav-link>
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto align-items-md-center">
                        @guest
                            <x-nav-link href="/login" aktif="login">Masuk</x-nav-link>
                            <li class="nav-item ms-md-2">
                                <a class="btn btn-primary btn-sm" href="/register">Daftar</a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span>{{ Auth::user()->name }}</span>
                                    <span class="badge text-bg-light border">{{ \App\Models\User::ROLES[Auth::user()->role] ?? Auth::user()->role }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <span class="dropdown-item-text small text-secondary">{{ Auth::user()->email }}</span>
                                    <div class="dropdown-divider"></div>
                                    <form action="/logout" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Keluar</button>
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4 flex-grow-1">
            <div class="container">
                <x-flash />
            </div>

            @yield('content')
        </main>

        {{-- Footer (DESIGN.md §6.1) + jam operasional dipertahankan sesuai keputusan PM --}}
        <footer class="border-top bg-white mt-5 py-3">
            <div class="container small text-secondary d-flex flex-wrap justify-content-between gap-2">
                <span>{{ config('app.name', 'Reservus') }} — Sistem Reservasi &amp; Pelaporan Fasilitas Kampus · Jam operasional 07.00–20.00 WIB</span>
                <span>Proyek tugas kuliah Informatika FSM Undip · bukan sistem resmi Universitas Diponegoro</span>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
