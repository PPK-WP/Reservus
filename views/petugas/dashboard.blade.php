@extends('layouts.app')

@section('title', 'Dashboard Petugas — '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="h3 mb-1">Dashboard Petugas</h1>
        <p class="text-muted mb-0">Ringkasan antrian reservasi dan laporan yang perlu ditindaklanjuti.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Reservasi Menunggu</span>
                    <a href="/petugas/reservations" class="btn btn-sm btn-outline-primary">Buka Antrian</a>
                </div>
                <div class="card-body">
                    @if (view()->exists('petugas.partials.reservation-queue'))
                        @include('petugas.partials.reservation-queue')
                    @else
                        <div class="alert alert-light border mb-0">Modul belum terpasang.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Laporan Masuk</span>
                    <a href="/petugas/reports" class="btn btn-sm btn-outline-primary">Buka Antrian</a>
                </div>
                <div class="card-body">
                    @if (view()->exists('petugas.partials.report-queue'))
                        @include('petugas.partials.report-queue')
                    @else
                        <div class="alert alert-light border mb-0">Modul belum terpasang.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
