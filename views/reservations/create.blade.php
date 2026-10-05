@extends('layouts.app')

@section('title', 'Ajukan Reservasi | '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('reservations.index') }}" class="d-inline-flex align-items-center gap-2 small text-decoration-none mb-3">
            <span aria-hidden="true">&larr;</span>
            <span>Kembali ke reservasi saya</span>
        </a>
        <h1 class="h3 mb-1">Ajukan Reservasi</h1>
        <p class="text-secondary mb-0">Pilih fasilitas, tanggal, dan waktu penggunaan yang Anda butuhkan.</p>
    </div>

    @if ($errors->has('reservation'))
        <div class="alert alert-danger d-flex gap-3" role="alert">
            <x-ikon nama="exclamation-triangle" ukuran="1.25rem" class="mt-1" />
            <div>
                <h2 class="h6 alert-heading mb-1">Jadwal belum dapat diajukan</h2>
                @foreach ($errors->get('reservation') as $message)
                    <div>{{ $message }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('reservations.store') }}" class="card">
                @csrf
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center gap-3">
                        <span class="ikon-kotak flex-shrink-0" aria-hidden="true">
                            <x-ikon nama="calendar-check" ukuran="1.2rem" />
                        </span>
                        <div>
                            <h2 class="h6 mb-1">Fasilitas dan jadwal</h2>
                            <p class="small text-secondary mb-0">Lengkapi detail penggunaan fasilitas kampus.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    <div class="mb-4">
                        <label for="facility_id" class="form-label fw-medium">Fasilitas</label>
                        <select id="facility_id" name="facility_id"
                                class="form-select @error('facility_id') is-invalid @enderror"
                                aria-describedby="facility-help" required>
                            <option value="">Pilih fasilitas</option>
                            @foreach ($facilities as $facility)
                                <option value="{{ $facility->id }}" @selected(old('facility_id', $selectedFacility?->id) == $facility->id)>
                                    {{ $facility->name }} - {{ $facility->location }}
                                </option>
                            @endforeach
                        </select>
                        <div id="facility-help" class="form-text">Hanya fasilitas aktif yang dapat dipilih.</div>
                        @error('facility_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <fieldset class="bg-kobalt-muda border rounded-3 p-3 p-md-4 mb-4">
                        <legend class="float-none w-auto h6 px-2 mb-2">Jadwal penggunaan</legend>
                        <p class="small text-secondary mb-3">Jam operasional 07.00 sampai 20.00 WIB dengan interval 30 menit.</p>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="reservation_date" class="form-label fw-medium">Tanggal reservasi</label>
                                <input type="date" id="reservation_date" name="reservation_date"
                                       value="{{ old('reservation_date', $selectedDate) }}"
                                       class="form-control @error('reservation_date') is-invalid @enderror"
                                       aria-describedby="date-help" required>
                                <div id="date-help" class="form-text">Pilih tanggal hari ini sampai 30 hari ke depan.</div>
                                @error('reservation_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="start_time" class="form-label fw-medium">Jam mulai</label>
                                <select id="start_time" name="start_time"
                                        class="form-select @error('start_time') is-invalid @enderror" required>
                                    <option value="">Pilih jam mulai</option>
                                    @foreach ($availability->startOptions() as $time)
                                        <option value="{{ $time }}" @selected(old('start_time') === $time)>{{ str_replace(':', '.', $time) }}</option>
                                    @endforeach
                                </select>
                                @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_time" class="form-label fw-medium">Jam selesai</label>
                                <select id="end_time" name="end_time"
                                        class="form-select @error('end_time') is-invalid @enderror" required>
                                    <option value="">Pilih jam selesai</option>
                                    @foreach ($availability->endOptions() as $time)
                                        <option value="{{ $time }}" @selected(old('end_time') === $time)>{{ str_replace(':', '.', $time) }}</option>
                                    @endforeach
                                </select>
                                @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </fieldset>

                    <div>
                        <label for="purpose" class="form-label fw-medium">Tujuan penggunaan</label>
                        <textarea id="purpose" name="purpose" rows="5" maxlength="2000"
                                  class="form-control @error('purpose') is-invalid @enderror"
                                  aria-describedby="purpose-help" required>{{ old('purpose') }}</textarea>
                        <div id="purpose-help" class="form-text">Jelaskan kegiatan secara ringkas, minimal 10 karakter.</div>
                        @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="card-footer bg-white d-flex flex-column-reverse flex-sm-row justify-content-end align-items-stretch align-items-sm-center gap-2 py-3">
                    <a href="{{ route('reservations.index') }}" class="btn btn-link text-decoration-none">Batal</a>
                    <button type="submit" class="btn btn-primary">Ajukan reservasi</button>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <aside class="card border-top border-primary border-3" aria-labelledby="info-pengajuan">
                <div class="card-body p-3 p-md-4">
                    <span class="ikon-kotak mb-3" aria-hidden="true">
                        <x-ikon nama="info-circle" ukuran="1.35rem" />
                    </span>
                    <h2 id="info-pengajuan" class="h6 mb-3">Sebelum mengajukan</h2>
                    <ul class="small text-secondary ps-3 mb-0 vstack gap-2">
                        <li>Jam operasional fasilitas pukul 07.00 sampai 20.00 WIB.</li>
                        <li>Setiap pilihan waktu menggunakan interval 30 menit.</li>
                        <li>Pengajuan akan masuk ke antrean persetujuan petugas.</li>
                        <li>Reservasi dapat dibatalkan paling lambat 2 jam sebelum dimulai.</li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
