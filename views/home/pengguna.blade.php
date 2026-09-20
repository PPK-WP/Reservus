@extends('layouts.app')

@section('title', 'Beranda — '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="h3 mb-1">Halo, {{ Auth::user()->name }}</h1>
        <p class="text-muted mb-0">Cek ketersediaan fasilitas, ajukan reservasi, atau laporkan kerusakan.</p>
    </div>

    <div class="row g-3">
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Cari Fasilitas</h2>
                    <p class="card-text text-muted">Lihat daftar fasilitas dan ketersediaan per slot 30 menit.</p>
                    <a href="/facilities" class="btn btn-primary btn-sm">Lihat Fasilitas</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Ajukan Reservasi</h2>
                    <p class="card-text text-muted">Pilih tanggal dan jam dalam jam operasional 07.00–20.00.</p>
                    <a href="/reservations/create" class="btn btn-primary btn-sm">Ajukan Sekarang</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Laporkan Kerusakan</h2>
                    <p class="card-text text-muted">Sampaikan kerusakan atau kebersihan fasilitas.</p>
                    <a href="/reports/create" class="btn btn-primary btn-sm">Buat Laporan</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Reservasi Saya</h2>
                    <p class="card-text text-muted">Riwayat pengajuan beserta statusnya.</p>
                    <a href="/reservations" class="btn btn-outline-primary btn-sm">Lihat Reservasi</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Laporan Saya</h2>
                    <p class="card-text text-muted">Pantau tindak lanjut laporan yang Anda kirim.</p>
                    <a href="/reports" class="btn btn-outline-primary btn-sm">Lihat Laporan</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
