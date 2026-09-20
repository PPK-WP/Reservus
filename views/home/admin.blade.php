@extends('layouts.app')

@section('title', 'Beranda Admin — '.config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Beranda Admin</h1>
            <p class="text-muted mb-0">Kelola akun, fasilitas, dan rekap pemakaian.</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Verifikasi Akun</h2>
                    <p class="card-text text-muted">
                        {{ $pendingCount }} akun menunggu verifikasi.
                    </p>
                    <a href="/admin/verifications" class="btn btn-primary btn-sm">
                        Buka Verifikasi
                        @if ($pendingCount > 0)
                            <span class="badge text-bg-warning ms-1">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Kelola User</h2>
                    <p class="card-text text-muted">Daftar akun dan pembuatan akun petugas.</p>
                    <a href="/admin/users" class="btn btn-outline-primary btn-sm">Buka Kelola User</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Kelola Fasilitas</h2>
                    <p class="card-text text-muted">Master data fasilitas kampus.</p>
                    <a href="/admin/facilities" class="btn btn-outline-primary btn-sm">Buka Kelola Fasilitas</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h2 class="h5 card-title">Rekap</h2>
                    <p class="card-text text-muted">Okupansi fasilitas dan frekuensi laporan.</p>
                    <a href="/admin/recap" class="btn btn-outline-primary btn-sm">Buka Rekap</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
