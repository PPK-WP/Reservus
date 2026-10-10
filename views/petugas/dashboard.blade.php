@extends('layouts.app')

@section('title', 'Dashboard Petugas | '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        {{-- Teks "Dashboard Petugas" dicek BaselineAccessTest — jangan diubah --}}
        <h1 class="h3 mb-1">Dashboard Petugas</h1>
        <p class="text-secondary mb-0">Ringkasan antrian reservasi dan laporan yang perlu ditindaklanjuti.</p>
    </div>

    <x-beranda.ringkasan-petugas />

    <div class="row g-4 align-items-start">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0">Reservasi menunggu</h2>
                    <a href="/petugas/reservations" class="btn btn-sm btn-outline-primary">Buka antrian</a>
                </div>
                <div class="card-body">
                    {{-- Isi milik SRS-006 (P2) --}}
                    @if (view()->exists('petugas.partials.reservation-queue'))
                        @include('petugas.partials.reservation-queue')
                    @else
                        <div class="alert alert-light border mb-0">Modul belum terpasang.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h2 class="h6 mb-0">Laporan masuk</h2>
                    <a href="/petugas/reports" class="btn btn-sm btn-outline-primary">Buka antrian</a>
                </div>
                <div class="card-body">
                    {{-- Isi milik SRS-008 (P3) --}}
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
