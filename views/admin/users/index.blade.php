@extends('layouts.app')

@section('title', 'Kelola User | '.config('app.name'))

@push('styles')
<style>
    .tabel-akun { min-width: 780px; }
</style>
@endpush

@section('content')
<div class="container">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Kelola User</h1>
            <p class="text-secondary mb-0">Lihat akun, peran, dan status akses pengguna Reservus.</p>
        </div>
        <a href="/admin/users/create" class="btn btn-primary flex-shrink-0">
            <span class="d-inline-flex align-items-center gap-2">
                <x-ikon nama="people" />
                <span>Buat akun</span>
            </span>
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-0">Saring daftar akun</h2>
        </div>
        <div class="card-body">
            <form method="GET" action="/admin/users" class="row g-3 align-items-end">
                <div class="col-sm-6 col-lg-4">
                    <label for="role" class="form-label fw-medium">Peran</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">Semua peran</option>
                        @foreach (\App\Models\User::ROLES as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filterRole === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <label for="status" class="form-label fw-medium">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        @foreach (\App\Models\User::STATUSES as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filterStatus === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-4 d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-outline-primary">Terapkan saringan</button>
                    @if ($filterRole || $filterStatus)
                        <a href="/admin/users" class="btn btn-link text-decoration-none">Hapus saringan</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
            <h2 class="h6 mb-0">Daftar akun</h2>
            <span class="small text-secondary">{{ $users->total() }} akun</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 tabel-akun">
                <thead class="table-light small fw-semibold">
                    <tr>
                        <th scope="col">Nama</th>
                        <th scope="col">Email</th>
                        <th scope="col">Peran</th>
                        <th scope="col">Jenis</th>
                        <th scope="col">NIM / NIP</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr class="{{ $user->status === 'pending' ? 'table-row-aktif' : '' }}">
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ \App\Models\User::ROLES[$user->role] ?? $user->role }}</td>
                            <td>{{ $user->user_type ? (\App\Models\User::USER_TYPES[$user->user_type] ?? $user->user_type) : '-' }}</td>
                            <td>{{ $user->identity_number ?: '-' }}</td>
                            <td><x-status-badge :status="$user->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 px-3">
                                <span class="ikon-kotak mb-3" aria-hidden="true">
                                    <x-ikon nama="search" ukuran="1.3rem" />
                                </span>
                                <h3 class="h6 mb-2">Tidak ada akun yang cocok</h3>
                                <p class="text-secondary mb-3">Ubah pilihan peran atau status untuk melihat akun lain.</p>
                                @if ($filterRole || $filterStatus)
                                    <a href="/admin/users" class="btn btn-outline-primary btn-sm">Hapus saringan</a>
                                @endif
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
@endsection
