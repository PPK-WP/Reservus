@extends('layouts.app')

@section('title', 'Ajukan Reservasi — '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="h3 mb-1">Ajukan Reservasi</h1>
        <p class="text-muted mb-0">Pilih fasilitas dan slot waktu yang tersedia.</p>
    </div>

    @if ($errors->has('reservation'))
        <div class="alert alert-danger">
            @foreach ($errors->get('reservation') as $message)
                <div>{{ $message }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('reservations.store') }}" class="card shadow-sm">
        @csrf
        <div class="card-body">
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

            <div class="mt-3 mb-0">
                <label for="purpose" class="form-label">Tujuan penggunaan</label>
                <textarea id="purpose" name="purpose" rows="4" maxlength="2000"
                          class="form-control @error('purpose') is-invalid @enderror" required>{{ old('purpose') }}</textarea>
                @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-end gap-2">
            <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
        </div>
    </form>
</div>
@endsection
