@extends('layouts.app')

@section('title', 'Beranda — '.config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Halo, {{ Auth::user()->name }}</h1>
            <p class="text-secondary mb-0">Cek ketersediaan fasilitas, ajukan reservasi, atau laporkan kerusakan.</p>
        </div>
        {{-- Satu aksi utama yang menonjol, aksi kedua bergaya outline (heuristik #8) --}}
        <div class="d-flex flex-wrap gap-2">
            <a href="/reports/create" class="btn btn-outline-primary">Laporkan kerusakan</a>
            <a href="/reservations/create" class="btn btn-primary">Ajukan reservasi</a>
        </div>
    </div>

    <x-beranda.aktivitas-pengguna />

    <div class="row g-3">
        <div class="col-lg-8">
            <h2 class="h6 text-secondary mb-3">Pintasan</h2>
            <div class="row g-3">
                <div class="col-md-4">
                    {{-- Teks "Cari Fasilitas" dicek BaselineAccessTest — jangan diubah --}}
                    <x-kartu-pintasan href="/facilities" judul="Cari Fasilitas" ikon="search"
                                      deskripsi="Daftar fasilitas dan ketersediaan per slot 30 menit." />
                </div>
                <div class="col-md-4">
                    <x-kartu-pintasan href="/reservations" judul="Reservasi Saya" ikon="calendar-check"
                                      deskripsi="Riwayat pengajuan beserta statusnya." />
                </div>
                <div class="col-md-4">
                    <x-kartu-pintasan href="/reports" judul="Laporan Saya" ikon="clipboard-check"
                                      deskripsi="Pantau tindak lanjut laporan Anda." />
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <h2 class="h6 text-secondary mb-3">Aturan singkat</h2>
            {{-- Bantuan & dokumentasi di tempat (heuristik #10); isinya aturan bisnis AGENTS.md bagian B --}}
            <div class="card bg-kobalt-muda border-0">
                <div class="card-body">
                    <ul class="small mb-0 ps-3 d-grid gap-2">
                        <li>Fasilitas bisa dipesan pukul <strong>07.00–20.00 WIB</strong>, per slot 30 menit.</li>
                        <li>Tanggal reservasi paling jauh <strong>30 hari</strong> dari hari ini.</li>
                        <li>Reservasi yang masih menunggu persetujuan <strong>belum mengunci</strong> slot.</li>
                        <li>Pembatalan paling lambat <strong>2 jam</strong> sebelum waktu mulai.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
