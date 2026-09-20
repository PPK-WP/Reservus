@extends('layouts.app')

@section('title', 'Verifikasi Akun — '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="h3 mb-1">Verifikasi Akun</h1>
        <p class="text-muted mb-0">
            Akun hasil pendaftaran mandiri yang menunggu keputusan Anda. Yang paling lama menunggu berada di atas.
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jenis</th>
                        <th>NIM / NIP</th>
                        <th>Mendaftar</th>
                        <th>Status</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->user_type ? (\App\Models\User::USER_TYPES[$user->user_type] ?? $user->user_type) : '—' }}</td>
                            <td>{{ $user->identity_number ?: '—' }}</td>
                            <td>{{ $user->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                            <td><x-status-badge :status="$user->status" /></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <form method="POST" action="/admin/verifications/{{ $user->id }}/approve"
                                          onsubmit="return confirm('Setujui akun {{ $user->name }}?');">
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
                            <td colspan="7" class="text-center text-muted py-5">
                                Tidak ada akun menunggu verifikasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="card-footer bg-white">{{ $users->links() }}</div>
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
                    <h2 class="modal-title h5" id="judul-tolak-{{ $user->id }}">Tolak Akun {{ $user->name }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <label for="catatan-{{ $user->id }}" class="form-label">Catatan penolakan</label>
                    <textarea id="catatan-{{ $user->id }}" name="verification_note" rows="3"
                              class="form-control" required maxlength="500"
                              placeholder="Contoh: NIM tidak terdaftar pada data akademik."></textarea>
                    <div class="form-text">
                        Wajib diisi, maksimal 500 karakter. Catatan ini ditampilkan kepada pendaftar saat mencoba masuk.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Akun</button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection
