@extends('layouts.app')

@section('title', 'Akses Ditolak | '.config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card text-center">
                <div class="card-body px-4 py-5">
                    <span class="ikon-kotak mb-3">
                        <x-ikon nama="shield-lock" ukuran="1.5rem" />
                    </span>
                    <p class="small text-secondary fw-semibold mb-1">Kode 403</p>
                    <h1 class="h3 mb-3">Akses ditolak</h1>
                    <p class="text-secondary mx-auto teks-ringkas">
                        {{ $exception?->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.' }}
                    </p>
                    <p class="small text-secondary mx-auto teks-ringkas mb-4">
                        Halaman ini hanya untuk peran tertentu. Kembali ke beranda untuk melihat menu yang tersedia
                        bagi akun Anda, atau masuk dengan akun lain.
                    </p>
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        @auth
                            <a href="/home" class="btn btn-primary">Kembali ke beranda</a>
                        @else
                            <a href="/login" class="btn btn-primary">Masuk</a>
                        @endauth
                        <a href="/facilities" class="btn btn-outline-secondary">Lihat fasilitas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
