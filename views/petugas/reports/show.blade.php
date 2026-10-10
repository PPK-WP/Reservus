@extends('layouts.app')

@section('title', 'Detail Laporan #'.$report->id.' — '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('petugas.reports.index') }}" class="d-inline-flex align-items-center gap-2 small text-decoration-none mb-3">
            <span aria-hidden="true">&larr;</span>
            <span>Kembali ke antrian laporan</span>
        </a>
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-2">
            <div>
                <h1 class="h3 mb-1">Laporan #{{ $report->id }}</h1>
                <p class="text-secondary mb-0">Tinjau rincian laporan dan tentukan tindakan pemrosesan.</p>
            </div>
            <x-status-badge :status="$report->status" class="align-self-start" />
        </div>
    </div>

    <div class="row g-4 align-items-start">
        {{-- Kolom kiri: Detail laporan --}}
        <div class="col-lg-7">
            <article class="card">
                <div class="card-header bg-kobalt-muda py-3">
                    <h2 class="h6 mb-0">Informasi laporan</h2>
                </div>
                <div class="card-body p-3 p-md-4">
                    <dl class="row gy-3 mb-0">
                        <dt class="col-sm-4 text-secondary fw-medium">Pelapor</dt>
                        <dd class="col-sm-8 mb-0">
                            <span class="d-block fw-semibold">{{ $report->user->name }}</span>
                            <span class="small text-secondary">{{ $report->user->email }}</span>
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Fasilitas</dt>
                        <dd class="col-sm-8 mb-0">
                            <span class="d-block fw-semibold">{{ $report->facility->name }}</span>
                            <span class="small text-secondary">{{ $report->facility->location }} · {{ \App\Models\Facility::TYPES[$report->facility->type] ?? $report->facility->type }}</span>
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Status fasilitas</dt>
                        <dd class="col-sm-8 mb-0"><x-status-badge :status="$report->facility->status" /></dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Kategori</dt>
                        <dd class="col-sm-8 mb-0">
                            <span class="badge text-bg-light border text-dark">
                                {{ \App\Models\Report::CATEGORIES[$report->category] ?? $report->category }}
                            </span>
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Tanggal lapor</dt>
                        <dd class="col-sm-8 mb-0">{{ $report->created_at->format('d/m/Y H:i') }} WIB</dd>

                        <dt class="col-sm-4 text-secondary fw-medium">Deskripsi</dt>
                        <dd class="col-sm-8 mb-0 teks-ringkas">{!! nl2br(e($report->description)) !!}</dd>
                    </dl>

                    @if ($report->photoUrl())
                        <div class="mt-4">
                            <h3 class="h6 text-secondary mb-2">Foto bukti</h3>
                            <a href="{{ $report->photoUrl() }}" target="_blank">
                                <img src="{{ $report->photoUrl() }}" alt="Foto laporan"
                                     class="img-fluid rounded border" style="max-height: 400px;">
                            </a>
                        </div>
                    @endif

                    @if ($report->processor)
                        <div class="border-top mt-4 pt-4">
                            <dl class="row gy-3 mb-0">
                                <dt class="col-sm-4 text-secondary fw-medium">Diproses oleh</dt>
                                <dd class="col-sm-8 mb-0">{{ $report->processor->name }}</dd>

                                <dt class="col-sm-4 text-secondary fw-medium">Waktu proses</dt>
                                <dd class="col-sm-8 mb-0">{{ $report->processed_at->format('d/m/Y H:i') }} WIB</dd>
                            </dl>
                        </div>
                    @endif

                    @if ($report->resolution_note)
                        <div class="alert alert-light border mt-4 mb-0">
                            <h3 class="h6 alert-heading mb-1">Catatan resolusi</h3>
                            <div>{!! nl2br(e($report->resolution_note)) !!}</div>
                        </div>
                    @endif
                </div>
            </article>
        </div>

        {{-- Kolom kanan: Panel aksi --}}
        <div class="col-lg-5 vstack gap-3">
            {{-- Aksi Status Laporan --}}
            @if (!empty($allowedStatuses))
                <aside class="card border-top border-primary border-3" aria-labelledby="status-tindakan">
                    <div class="card-header bg-white py-3">
                        <h2 id="status-tindakan" class="h6 mb-0">Ubah status laporan</h2>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        @foreach ($allowedStatuses as $nextStatus)
                            @php
                                $needsNote = in_array($nextStatus, ['selesai', 'ditolak']);
                                $modalId = 'modal-' . $nextStatus;
                                $btnClass = match($nextStatus) {
                                    'diproses' => 'btn-info text-white',
                                    'selesai'  => 'btn-success',
                                    'ditolak'  => 'btn-danger',
                                    default    => 'btn-secondary',
                                };
                                $label = \App\Models\Report::STATUSES[$nextStatus] ?? $nextStatus;
                            @endphp

                            @if ($needsNote)
                                {{-- Tombol buka modal --}}
                                <button type="button" class="btn {{ $btnClass }} w-100 mb-2"
                                        data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
                                    Tandai {{ $label }}
                                </button>

                                {{-- Modal catatan resolusi --}}
                                <div class="modal fade" id="{{ $modalId }}" tabindex="-1"
                                     aria-labelledby="{{ $modalId }}-label" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('petugas.reports.status', $report) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $nextStatus }}">

                                                <div class="modal-header">
                                                    <h5 class="modal-title h6" id="{{ $modalId }}-label">
                                                        Tandai {{ $label }}
                                                    </h5>
                                                    <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="resolution_note_{{ $nextStatus }}" class="form-label fw-medium">
                                                            Catatan resolusi <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea name="resolution_note"
                                                                  id="resolution_note_{{ $nextStatus }}"
                                                                  class="form-control"
                                                                  rows="4" required
                                                                  minlength="5" maxlength="1000"
                                                                  placeholder="Jelaskan tindakan yang telah dilakukan (min. 5 karakter)"></textarea>
                                                        <div class="form-text">Catatan ini akan dapat dilihat oleh pelapor.</div>
                                                    </div>

                                                    {{-- Checkbox restore fasilitas --}}
                                                    @if ($report->facility->status === 'dalam_perbaikan')
                                                        <div class="form-check border-top pt-3">
                                                            <input class="form-check-input" type="checkbox"
                                                                   name="restore_facility" value="1"
                                                                   id="restore_{{ $nextStatus }}">
                                                            <label class="form-check-label small" for="restore_{{ $nextStatus }}">
                                                                Fasilitas sudah bisa dipakai kembali (set status ke aktif)
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-link text-decoration-none"
                                                            data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn {{ $btnClass }}">
                                                        Konfirmasi
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Aksi tanpa catatan (diproses): confirm() --}}
                                <form action="{{ route('petugas.reports.status', $report) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin mengubah status menjadi {{ $label }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                                    <button type="submit" class="btn {{ $btnClass }} w-100 mb-2">
                                        Tandai {{ $label }}
                                    </button>
                                </form>
                            @endif
                        @endforeach
                    </div>
                </aside>
            @endif

            {{-- Aksi Status Fasilitas (US 12) --}}
            <aside class="card" aria-labelledby="status-fasilitas-judul">
                <div class="card-header bg-white py-3">
                    <h2 id="status-fasilitas-judul" class="h6 mb-0">Status fasilitas</h2>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <strong class="small">{{ $report->facility->name }}</strong>
                        <x-status-badge :status="$report->facility->status" />
                    </div>

                    @if ($report->facility->status === 'aktif')
                        <form action="{{ route('petugas.reports.facility-status', $report) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin menandai fasilitas ini dalam perbaikan?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="repair">
                            <button type="submit" class="btn btn-warning w-100">
                                Tandai Dalam Perbaikan
                            </button>
                        </form>
                    @elseif ($report->facility->status === 'dalam_perbaikan')
                        <form action="{{ route('petugas.reports.facility-status', $report) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin mengembalikan fasilitas ke aktif?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="restore">
                            <button type="submit" class="btn btn-success w-100">
                                Kembalikan ke Aktif
                            </button>
                        </form>
                    @elseif ($report->facility->status === 'nonaktif')
                        <div class="alert alert-secondary mb-0 small">
                            Fasilitas berstatus <strong>nonaktif</strong>. Perubahan status hanya bisa dilakukan oleh Admin.
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
