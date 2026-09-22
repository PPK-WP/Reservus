@extends('layouts.app')

@section('title', 'Katalog Fasilitas — '.config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Katalog Fasilitas</h1>
            <p class="text-muted mb-0">Cari fasilitas kampus lalu buka detailnya untuk melihat slot yang tersedia.</p>
        </div>
    </div>

    {{-- Filter bar (alur Booking.com): seluruh kriteria bersifat opsional --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="/facilities" class="row g-2 align-items-end">
                <div class="col-sm-6 col-lg-3">
                    <label for="q" class="form-label small text-muted mb-1">Kata kunci nama</label>
                    <input id="q" type="search" name="q" value="{{ $filter['q'] }}"
                           class="form-control" placeholder="mis. Lab Komputer" maxlength="100">
                </div>
                <div class="col-sm-6 col-lg-2">
                    <label for="type" class="form-label small text-muted mb-1">Tipe</label>
                    <select id="type" name="type" class="form-select">
                        <option value="">Semua tipe</option>
                        @foreach (\App\Models\Facility::TYPES as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filter['type'] === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <label for="location" class="form-label small text-muted mb-1">Lokasi</label>
                    <select id="location" name="location" class="form-select">
                        <option value="">Semua lokasi</option>
                        @foreach ($lokasi as $lokasiItem)
                            <option value="{{ $lokasiItem }}" @selected($filter['location'] === $lokasiItem)>{{ $lokasiItem }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-lg-2">
                    <label for="min_capacity" class="form-label small text-muted mb-1">Kapasitas minimal</label>
                    <input id="min_capacity" type="number" name="min_capacity" value="{{ $filter['min_capacity'] }}"
                           class="form-control" min="1" step="1" placeholder="mis. 30">
                </div>
                <div class="col-sm-6 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Saring</button>
                    @if ($filter['type'] || $filter['location'] || $filter['min_capacity'] || $filter['q'])
                        <a href="/facilities" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if ($facilities->isEmpty())
        <div class="alert alert-light border text-center py-4 mb-0">
            <p class="mb-2">Tidak ada fasilitas yang cocok dengan saringan ini.</p>
            <a href="/facilities" class="btn btn-outline-primary btn-sm">Tampilkan semua fasilitas</a>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 mb-4">
            @foreach ($facilities as $facility)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <div>
                                    <span class="badge text-bg-light border">{{ \App\Models\Facility::TYPES[$facility->type] ?? $facility->type }}</span>
                                    <h2 class="h6 fw-bold mt-2 mb-0">{{ $facility->name }}</h2>
                                </div>
                                <x-status-badge :status="$facility->status" />
                            </div>

                            <p class="small text-muted mb-2 mt-2">
                                Lokasi: {{ $facility->location }}<br>
                                Kapasitas: {{ $facility->capacity }} orang
                            </p>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Slot tersedia hari ini</span>
                                    <span class="fw-semibold">{{ $facility->slot_tersedia_hari_ini }}/{{ $totalSlot }}</span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;" role="img"
                                     aria-label="{{ $facility->slot_tersedia_hari_ini }} dari {{ $totalSlot }} slot tersedia">
                                    <div class="progress-bar {{ $facility->slot_tersedia_hari_ini > 0 ? 'bg-success' : 'bg-secondary' }}"
                                         style="width: {{ $totalSlot > 0 ? round($facility->slot_tersedia_hari_ini / $totalSlot * 100) : 0 }}%"></div>
                                </div>
                                <a href="/facilities/{{ $facility->id }}" class="btn btn-outline-primary w-100">
                                    Lihat ketersediaan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($facilities->hasPages())
            <div>{{ $facilities->links() }}</div>
        @endif
    @endif
</div>
@endsection
