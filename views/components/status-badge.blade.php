@props(['status'])

@php
    // Peta status → warna Bootstrap + label Indonesia (README §7.4). Warna jangan diubah (DESIGN.md §2);
    // label memakai huruf kecil biasa (DESIGN.md §3).
    $peta = [
        'menunggu'        => ['warning',   'Menunggu'],
        'pending'         => ['warning',   'Menunggu verifikasi'],
        'dalam_perbaikan' => ['warning',   'Dalam perbaikan'],
        'disetujui'       => ['success',   'Disetujui'],
        'selesai'         => ['success',   'Selesai'],
        'aktif'           => ['success',   'Aktif'],
        'tersedia'        => ['success',   'Tersedia'],
        'ditolak'         => ['danger',    'Ditolak'],
        'baru'            => ['primary',   'Baru'],
        'diproses'        => ['info',      'Diproses'],
        'dibatalkan'      => ['secondary', 'Dibatalkan'],
        'nonaktif'        => ['secondary', 'Nonaktif'],
        'tidak_tersedia'  => ['secondary', 'Tidak tersedia'],
    ];

    [$warna, $label] = $peta[$status] ?? ['dark', ucfirst(str_replace('_', ' ', (string) $status))];
@endphp

<span {{ $attributes->merge(['class' => 'badge text-bg-'.$warna]) }}>{{ $label }}</span>
