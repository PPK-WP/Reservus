@extends('layouts.app')

@section('title', 'Kelola User — '.config('app.name'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1">Kelola User</h1>
            <p class="text-muted mb-0">Daftar seluruh akun beserta peran dan statusnya.</p>
        </div>
        <a href="/admin/users/create" class="btn btn-primary">+ Buat Akun</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="/admin/users" class="row g-2 align-items-end">
                <div class="col-sm-4">
                    <label for="role" class="form-label small text-muted mb-1">Peran</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">Semua peran</option>
                        @foreach (\App\Models\User::ROLES as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filterRole === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <label for="status" class="form-label small text-muted mb-1">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua status</option>
                        @foreach (\App\Models\User::STATUSES as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($filterStatus === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">Saring</button>
                    @if ($filterRole || $filterStatus)
                        <a href="/admin/users" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Peran</th>
                        <th>Jenis</th>
                        <th>NIM / NIP</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ \App\Models\User::ROLES[$user->role] ?? $user->role }}</td>
                            <td>{{ $user->user_type ? (\App\Models\User::USER_TYPES[$user->user_type] ?? $user->user_type) : '—' }}</td>
                            <td>{{ $user->identity_number ?: '—' }}</td>
                            <td><x-status-badge :status="$user->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Tidak ada akun yang cocok dengan saringan ini.
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
