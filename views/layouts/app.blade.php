<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Reservus'))</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-light">
    <div id="app" class="d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/facilities">{{ config('app.name', 'Reservus') }}</a>

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
                                <li class="nav-item"><a class="nav-link" href="/petugas">Dashboard</a></li>
                                <li class="nav-item"><a class="nav-link" href="/petugas/reservations">Antrian Reservasi</a></li>
                                <li class="nav-item"><a class="nav-link" href="/petugas/reports">Antrian Laporan</a></li>
                                <li class="nav-item"><a class="nav-link" href="/facilities">Fasilitas</a></li>
                            @elseif (Auth::user()->isAdmin())
                                <li class="nav-item"><a class="nav-link" href="/home">Beranda</a></li>
                                <li class="nav-item"><a class="nav-link" href="/admin/verifications">Verifikasi</a></li>
                                <li class="nav-item"><a class="nav-link" href="/admin/users">Kelola User</a></li>
                                <li class="nav-item"><a class="nav-link" href="/admin/facilities">Kelola Fasilitas</a></li>
                                <li class="nav-item"><a class="nav-link" href="/admin/recap">Rekap</a></li>
                                <li class="nav-item"><a class="nav-link" href="/facilities">Fasilitas</a></li>
                            @else
                                <li class="nav-item"><a class="nav-link" href="/home">Beranda</a></li>
                                <li class="nav-item"><a class="nav-link" href="/facilities">Fasilitas</a></li>
                                <li class="nav-item"><a class="nav-link" href="/reservations">Reservasi Saya</a></li>
                                <li class="nav-item"><a class="nav-link" href="/reports">Laporan Saya</a></li>
                            @endif
                        @else
                            <li class="nav-item"><a class="nav-link" href="/facilities">Fasilitas</a></li>
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item"><a class="nav-link" href="/login">Masuk</a></li>
                            <li class="nav-item"><a class="nav-link" href="/register">Daftar</a></li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ Auth::user()->name }}
                                    <span class="badge bg-secondary">{{ \App\Models\User::ROLES[Auth::user()->role] ?? Auth::user()->role }}</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <span class="dropdown-item-text small text-muted">{{ Auth::user()->email }}</span>
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

        <footer class="py-3 border-top bg-white">
            <div class="container small text-muted d-flex justify-content-between flex-wrap gap-2">
                <span>{{ config('app.name', 'Reservus') }} — Sistem Reservasi &amp; Pelaporan Fasilitas Kampus</span>
                <span>Jam operasional 07.00–20.00 WIB</span>
            </div>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
