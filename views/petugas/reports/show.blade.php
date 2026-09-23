@extends('layouts.app')

@section('title', 'Detail Laporan #'.$report->id.' — '.config('app.name'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="mb-4">
                <a href="{{ route('petugas.reports.index') }}" class="text-decoration-none">← Kembali ke Antrian</a>
            </div>

            <div class="row g-4">
                {{-- Kolom kiri: Detail laporan --}}
                <div class="col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Laporan #{{ $report->id }}</h5>
                            <x-status-badge :status="$report->status" />
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-sm-4 text-muted">Pelapor</div>
                                <div class="col-sm-8">{{ $report->user->name }}</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4 text-muted">Fasilitas</div>
                                <div class="col-sm-8">
                                    {{ $report->facility->name }}
                                    <span class="text-muted small">({{ \App\Models\Facility::TYPES[$report->facility->type] ?? $report->facility->type }})</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4 text-muted">Lokasi</div>
                                <div class="col-sm-8">{{ $report->facility->location }}</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4 text-muted">Status Fasilitas</div>
                                <div class="col-sm-8"><x-status-badge :status="$report->facility->status" /></div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4 text-muted">Kategori</div>
                                <div class="col-sm-8">
                                    <span class="badge text-bg-light text-dark">
                                        {{ \App\Models\Report::CATEGORIES[$report->category] ?? $report->category }}
                                    </span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-4 text-muted">Tanggal Lapor</div>
                                <div class="col-sm-8">{{ $report->created_at->format('d/m/Y H:i') }} WIB</div>
                            </div>

                            <hr>

                            <h6 class="text-muted">Deskripsi</h6>
                            <div class="mb-3">{!! nl2br(e($report->description)) !!}</div>

                            @if ($report->photoUrl())
                                <h6 class="text-muted">Foto</h6>
                                <a href="{{ $report->photoUrl() }}" target="_blank">
                                    <img src="{{ $report->photoUrl() }}" alt="Foto laporan"
                                         class="img-fluid rounded" style="max-height: 400px;">
                                </a>
                            @endif

                            @if ($report->processor)
                                <hr>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Diproses Oleh</div>
                                    <div class="col-sm-8">{{ $report->processor->name }}</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-4 text-muted">Waktu Proses</div>
                                    <div class="col-sm-8">{{ $report->processed_at->format('d/m/Y H:i') }} WIB</div>
                                </div>
                            @endif

                            @if ($report->resolution_note)
                                <h6 class="text-muted">Catatan Resolusi</h6>
                                <div class="alert alert-light border mb-0">
                                    {!! nl2br(e($report->resolution_note)) !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kolom kanan: Panel aksi --}}
                <div class="col-lg-5">
                    {{-- Aksi Status Laporan --}}
                    @if (!empty($allowedStatuses))
                        <div class="card shadow-sm mb-3">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">Ubah Status Laporan</h6>
                            </div>
                            <div class="card-body">
                                @foreach ($allowedStatuses as $nextStatus)
                                    @php
                                        $needsNote = in_array($nextStatus, ['selesai', 'ditolak']);
                                        $modalId = 'modal-' . $nextStatus;
                                        $btnClass = match($nextStatus) {
                                            'diproses' => 'btn-info',
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
                                                            <h5 class="modal-title" id="{{ $modalId }}-label">
                                                                Tandai {{ $label }}
                                                            </h5>
                                                            <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="resolution_note_{{ $nextStatus }}" class="form-label">
                                                                    Catatan Resolusi <span class="text-danger">*</span>
                                                                </label>
                                                                <textarea name="resolution_note"
                                                                          id="resolution_note_{{ $nextStatus }}"
                                                                          class="form-control"
                                                                          rows="4" required
                                                                          minlength="5" maxlength="1000"
                                                                          placeholder="Jelaskan tindakan yang telah dilakukan (min. 5 karakter)"></textarea>
                                                            </div>

                                                            {{-- Checkbox restore fasilitas --}}
                                                            @if ($report->facility->status === 'dalam_perbaikan')
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                           name="restore_facility" value="1"
                                                                           id="restore_{{ $nextStatus }}">
                                                                    <label class="form-check-label" for="restore_{{ $nextStatus }}">
                                                                        Fasilitas sudah bisa dipakai kembali (set aktif)
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary"
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
                        </div>
                    @endif

                    {{-- Aksi Status Fasilitas (US 12) --}}
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">Status Fasilitas</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">
                                <strong>{{ $report->facility->name }}</strong>:
                                <x-status-badge :status="$report->facility->status" />
                            </p>

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
                                <div class="alert alert-secondary mb-0">
                                    Fasilitas berstatus <strong>nonaktif</strong>. Perubahan status hanya bisa dilakukan oleh Admin.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
