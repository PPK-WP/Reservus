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

                        @php
                            // Rentang slot tersedia, mis. "07.00–09.00", untuk ringkasan di atas pilihan jam.
                            $rentangTersedia = [];
                            foreach ($slots as $slot) {
                                if ($slot['status'] !== 'tersedia') { continue; }
                                $akhirTerakhir = $rentangTersedia ? end($rentangTersedia)[1] : null;
                                if ($akhirTerakhir === $slot['start']) {
                                    $rentangTersedia[array_key_last($rentangTersedia)][1] = $slot['end'];
                                } else {
                                    $rentangTersedia[] = [$slot['start'], $slot['end']];
                                }
                            }
                            $adaSlot = $rentangTersedia !== [];
                        @endphp

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="reservation_date" class="form-label fw-medium">Tanggal reservasi</label>
                                <input type="date" id="reservation_date" name="reservation_date"
                                       value="{{ old('reservation_date', $selectedDate) }}"
                                       min="{{ $minDate }}" max="{{ $maxDate }}"
                                       class="form-control @error('reservation_date') is-invalid @enderror"
                                       aria-describedby="date-help" required>
                                <div id="date-help" class="form-text">
                                    Reservasi diajukan paling lambat sehari sebelumnya: mulai besok sampai 30 hari ke depan.
                                </div>
                                @error('reservation_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12" aria-live="polite">
                                @if (! $selectedFacility)
                                    <div class="small text-secondary">Pilih fasilitas untuk melihat jam yang masih tersedia.</div>
                                @elseif (! $adaSlot)
                                    <div class="alert alert-warning small mb-0">
                                        Tidak ada slot tersedia untuk {{ $selectedFacility->name }} pada tanggal ini. Pilih tanggal lain.
                                    </div>
                                @else
                                    <div class="small">
                                        <span class="fw-semibold">Tersedia:</span>
                                        @foreach ($rentangTersedia as [$dari, $sampai])
                                            <span class="badge text-bg-success fw-normal">{{ str_replace(':', '.', $dari) }}–{{ str_replace(':', '.', $sampai) }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="start_time" class="form-label fw-medium">Jam mulai</label>
                                <select id="start_time" name="start_time"
                                        class="form-select @error('start_time') is-invalid @enderror"
                                        required @disabled(! $adaSlot)>
                                    <option value="">{{ $selectedFacility ? ($adaSlot ? 'Pilih jam mulai' : 'Tidak ada slot tersedia') : 'Pilih fasilitas dulu' }}</option>
                                    @foreach ($slots as $slot)
                                        @if ($slot['status'] === 'tersedia')
                                            <option value="{{ $slot['start'] }}" @selected(old('start_time') === $slot['start'])>{{ str_replace(':', '.', $slot['start']) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('start_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="end_time" class="form-label fw-medium">Jam selesai</label>
                                <select id="end_time" name="end_time"
                                        class="form-select @error('end_time') is-invalid @enderror"
                                        required disabled data-lama="{{ old('end_time') }}">
                                    <option value="">Pilih jam mulai dulu</option>
                                </select>
                                <div class="form-text">Hanya sampai sebelum slot berikutnya yang sudah terisi.</div>
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

@push('scripts')
<script id="data-slot" type="application/json">@json(array_values($slots))</script>
<script>
    (function () {
        const slot = JSON.parse(document.getElementById('data-slot').textContent);
        const fasilitas = document.getElementById('facility_id');
        const tanggal = document.getElementById('reservation_date');
        const mulai = document.getElementById('start_time');
        const selesai = document.getElementById('end_time');
        const tujuan = document.getElementById('purpose');
        const kunciTujuan = 'reservus-tujuan-reservasi';
        const label = (jam) => jam.replace(':', '.');

        // Ganti fasilitas/tanggal → muat ulang slot dari server; tujuan yang sudah diketik disimpan sementara.
        function muatUlang() {
            if (!fasilitas.value || !tanggal.value) { return; }
            try { sessionStorage.setItem(kunciTujuan, tujuan.value); } catch (e) {}
            const url = new URL(window.location.href);
            url.searchParams.set('facility', fasilitas.value);
            url.searchParams.set('date', tanggal.value);
            window.location.assign(url.toString());
        }
        fasilitas.addEventListener('change', muatUlang);
        tanggal.addEventListener('change', muatUlang);
        try {
            const simpanan = sessionStorage.getItem(kunciTujuan);
            if (simpanan && !tujuan.value) { tujuan.value = simpanan; }
            sessionStorage.removeItem(kunciTujuan);
        } catch (e) {}

        // Jam selesai: slot berurutan dari jam mulai, berhenti di slot pertama yang tidak tersedia.
        function isiJamSelesai() {
            const pilihan = selesai.dataset.lama || selesai.value;
            selesai.innerHTML = '';
            const indeks = slot.findIndex((s) => s.start === mulai.value);
            if (indeks < 0) {
                selesai.add(new Option('Pilih jam mulai dulu', ''));
                selesai.disabled = true;
                return;
            }
            selesai.add(new Option('Pilih jam selesai', ''));
            for (let i = indeks; i < slot.length && slot[i].status === 'tersedia'; i++) {
                selesai.add(new Option(label(slot[i].end), slot[i].end, false, slot[i].end === pilihan));
            }
            selesai.disabled = false;
            selesai.dataset.lama = '';
        }
        mulai.addEventListener('change', isiJamSelesai);
        isiJamSelesai();
    })();
</script>
@endpush
