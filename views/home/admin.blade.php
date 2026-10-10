@extends('layouts.app')

@section('title', 'Beranda Admin | '.config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            {{-- Teks "Beranda Admin" dicek BaselineAccessTest — jangan diubah --}}
            <h1 class="h3 mb-1">Beranda Admin</h1>
            <p class="text-secondary mb-0">Kelola akun, fasilitas, dan rekap pemakaian kampus.</p>
        </div>
        @if ($pendingCount > 0)
            <a href="/admin/verifications" class="btn btn-primary">
                Tinjau {{ $pendingCount }} pendaftar
            </a>
        @endif
    </div>

    <x-beranda.ringkasan-admin :pending-count="$pendingCount" />

    <h2 class="h6 text-secondary mb-3">Menu admin</h2>
    <div class="row g-3">
        <div class="col-sm-6 col-xl-3">
            <x-kartu-pintasan href="/admin/verifications" judul="Verifikasi Akun" ikon="person-check"
                              deskripsi="Setujui atau tolak pendaftaran mandiri." />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-kartu-pintasan href="/admin/users" judul="Kelola User" ikon="people"
                              deskripsi="Daftar akun dan pembuatan akun petugas." />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-kartu-pintasan href="/admin/facilities" judul="Kelola Fasilitas" ikon="building"
                              deskripsi="Master data dan status fasilitas kampus." />
        </div>
        <div class="col-sm-6 col-xl-3">
            <x-kartu-pintasan href="/admin/recap" judul="Rekap" ikon="bar-chart-line"
                              deskripsi="Okupansi fasilitas dan frekuensi laporan." />
        </div>
    </div>
</div>
@endsection
