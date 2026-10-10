@extends('layouts.app')

@section('title', 'Verifikasi Akun | '.config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 d-flex align-items-center gap-2">
                Verifikasi akun
                @if ($users->total() > 0)
                    <span class="badge text-bg-warning fs-6">{{ $users->total() }} menunggu</span>
                @endif
            </h1>
            <p class="text-secondary mb-0 teks-ringkas">
                Akun hasil pendaftaran mandiri yang menunggu keputusan Anda. Yang paling lama menunggu berada di atas.
            </p>
        </div>
        <a href="/admin/users" class="btn btn-outline-secondary btn-sm">Lihat semua akun</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="fw-semibold">Pendaftar</th>
                        <th class="fw-semibold d-none d-md-table-cell">Jenis</th>
                        <th class="fw-semibold d-none d-lg-table-cell">NIM / NIP</th>
                        <th class="fw-semibold d-none d-md-table-cell">Mendaftar</th>
                        <th class="fw-semibold d-none d-xl-table-cell">Status</th>
                        <th class="fw-semibold text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $user->name }}</div>
                                <div class="small text-secondary">{{ $user->email }}</div>
                                {{-- Di layar kecil kolom pelengkap disembunyikan; ringkasannya pindah ke sini --}}
                                <div class="small text-secondary d-md-none">
                                    {{ $user->user_type ? (\App\Models\User::USER_TYPES[$user->user_type] ?? $user->user_type) : 'Jenis belum diisi' }}
                                    · {{ $user->identity_number ?: 'NIM/NIP tidak diisi' }}
                                    · {{ $user->created_at?->timezone('Asia/Jakarta')->diffForHumans() }}
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">{{ $user->user_type ? (\App\Models\User::USER_TYPES[$user->user_type] ?? $user->user_type) : '—' }}</td>
                            <td class="d-none d-lg-table-cell">
                                @if ($user->identity_number)
                                    {{ $user->identity_number }}
                                @else
                                    <span class="text-secondary">Tidak diisi</span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">
                                <div>{{ $user->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</div>
                                <div class="small text-secondary">{{ $user->created_at?->timezone('Asia/Jakarta')->diffForHumans() }}</div>
                            </td>
                            <td class="d-none d-xl-table-cell"><x-status-badge :status="$user->status" /></td>
                            <td class="text-end">
                                <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                                    {{-- Teks konfirmasi lewat data-* agar nama pendaftar tidak pernah dieksekusi sebagai JavaScript --}}
                                    <form method="POST" action="/admin/verifications/{{ $user->id }}/approve"
                                          data-konfirmasi="Setujui akun {{ $user->name }}?"
                                          onsubmit="return confirm(this.dataset.konfirmasi);">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">Setujui</button>
                                    </form>

                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal" data-bs-target="#modal-tolak-{{ $user->id }}">
                                        Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <span class="ikon-kotak mb-2"><x-ikon nama="check-circle" /></span>
                                <p class="fw-semibold mb-1">Tidak ada akun menunggu verifikasi.</p>
                                <p class="small text-secondary mb-3">Pendaftaran baru akan muncul di sini. Akun yang sudah diproses ada di Kelola User.</p>
                                <a href="/admin/users" class="btn btn-outline-primary btn-sm">Buka Kelola User</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="card-footer bg-white">{{ $users->links('pagination::bootstrap-5') }}</div>
        @endif
    </div>
</div>

{{-- Modal catatan penolakan, satu per akun --}}
@foreach ($users as $user)
    <div class="modal fade" id="modal-tolak-{{ $user->id }}" tabindex="-1"
         aria-labelledby="judul-tolak-{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="/admin/verifications/{{ $user->id }}/reject" class="modal-content">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <h2 class="modal-title h5" id="judul-tolak-{{ $user->id }}">Tolak akun {{ $user->name }}?</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <p class="small text-secondary">{{ $user->email }} · {{ $user->user_type ? (\App\Models\User::USER_TYPES[$user->user_type] ?? $user->user_type) : 'Jenis belum diisi' }}</p>
                    <label for="catatan-{{ $user->id }}" class="form-label">Catatan penolakan</label>
                    <textarea id="catatan-{{ $user->id }}" name="verification_note" rows="3"
                              class="form-control" required maxlength="500"
                              placeholder="Contoh: NIM tidak terdaftar pada data akademik."></textarea>
                    <div class="form-text">
                        Wajib diisi, maksimal 500 karakter. Catatan ini ditampilkan kepada pendaftar saat mencoba masuk.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak akun</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection
