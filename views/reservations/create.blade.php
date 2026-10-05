@extends('layouts.app')

@section('title', 'Ajukan Reservasi — '.config('app.name'))

@section('content')
<div class="container reservasi-shell">
    <div class="mb-4">
        <p class="text-primary fw-semibold mb-2 text-uppercase small">Reservasi</p>
        <h1 class="h3 mb-1">Ajukan Reservasi</h1>
        <p class="text-secondary mb-0">Pilih fasilitas, jadwal, dan tujuan penggunaan dengan format yang jelas.</p>
    </div>

    @if ($errors->has('reservation'))
        <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
            <x-ikon nama="exclamation-triangle" class="mt-1" />
            <div>
                @foreach ($errors->get('reservation') as $message)
                    <div>{{ $message }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row justify-content-center g-4">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('reservations.store') }}" class="reservasi-form-card">
                @csrf
                <div class="card-header">
                    <h2 class="h5 mb-0">Form pemesanan</h2>
                </div>
                <div class="card-body">
                    <div class="reservasi-help mb-4">
                        <div class="d-flex align-items-start gap-3">
                            <span class="icon-wrap align-self-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg>
                            </span>
                            <div>
                                <div class="fw-semibold">Informasi penting</div>
                                <small class="text-secondary d-block">Jam operasional dan validasi bentrok dijamin oleh sistem. Reservasi yang belum disetujui tetap dapat dibatalkan sesuai ketentuan.</small>
                            </div>
                        </div>
                    </div>

                    <div class="reservasi-form-grid">
                        <div class="mb-3">
                            <label for="facility_id" class="form-label">Fasilitas</label>
                            <select id="facility_id" name="facility_id" class="form-select @error('facility_id') is-invalid @enderror" required>
                                <option value="">Pilih fasilitas</option>
                                @foreach ($facilities as $facility)
                                    <option value="{{ $facility->id }}" @selected(old('facility_id', $selectedFacility?->id) == $facility->id)>
                                        {{ $facility->name }} — {{ $facility->location }}
                                    </option>
                                @endforeach
                            </select>
                            @error('facility_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="reservation_date" class="form-label">Tanggal</label>
                                <input type="date" id="reservation_date" name="reservation_date"
                                       value="{{ old('reservation_date', $selectedDate) }}"
                                       class="form-control @error('reservation_date') is-invalid @enderror" required>
                                @error('reservation_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="start_time" class="form-label">Jam mulai</label>
                                <select id="start_time" name="start_time" class="form-select @error('start_time') is-invalid @enderror" required>
                                    <option value="">Pilih jam mulai</option>
                                    @foreach ($availability->startOptions() as $time)
                                        <option value="{{ $time }}" @selected(old('start_time') === $time)>{{ str_replace(':', '.', $time) }}</option>
                                    @endforeach
                                </select>
                                @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="end_time" class="form-label">Jam selesai</label>
                                <select id="end_time" name="end_time" class="form-select @error('end_time') is-invalid @enderror" required>
                                    <option value="">Pilih jam selesai</option>
                                    @foreach ($availability->endOptions() as $time)
                                        <option value="{{ $time }}" @selected(old('end_time') === $time)>{{ str_replace(':', '.', $time) }}</option>
                                    @endforeach
                                </select>
                                @error('end_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-0 mt-3">
                            <label for="purpose" class="form-label">Tujuan penggunaan</label>
                            <textarea id="purpose" name="purpose" rows="5" maxlength="2000"
                                      class="form-control @error('purpose') is-invalid @enderror" required>{{ old('purpose') }}</textarea>
                            @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2 flex-wrap">
                    <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
