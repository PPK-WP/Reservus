@extends('layouts.app')

@section('title', 'Akses Ditolak — '.config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm text-center">
                <div class="card-body py-5">
                    <p class="display-6 mb-2">403</p>
                    <h1 class="h4 mb-3">Akses Ditolak</h1>
                    <p class="text-muted">
                        {{ $exception?->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.' }}
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        @auth
                            <a href="/home" class="btn btn-primary">Kembali ke Beranda</a>
                        @endauth
                        <a href="/facilities" class="btn btn-outline-secondary">Lihat Fasilitas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
