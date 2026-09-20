@props(['status'])

@php
    // Peta status → warna Bootstrap + label Indonesia (README §7.4).
    $peta = [
        'menunggu'        => ['warning',   'Menunggu'],
        'pending'         => ['warning',   'Menunggu Verifikasi'],
        'dalam_perbaikan' => ['warning',   'Dalam Perbaikan'],
        'disetujui'       => ['success',   'Disetujui'],
        'selesai'         => ['success',   'Selesai'],
        'aktif'           => ['success',   'Aktif'],
        'tersedia'        => ['success',   'Tersedia'],
        'ditolak'         => ['danger',    'Ditolak'],
        'baru'            => ['primary',   'Baru'],
        'diproses'        => ['info',      'Diproses'],
        'dibatalkan'      => ['secondary', 'Dibatalkan'],
        'nonaktif'        => ['secondary', 'Nonaktif'],
        'tidak_tersedia'  => ['secondary', 'Tidak Tersedia'],
    ];

    [$warna, $label] = $peta[$status] ?? ['dark', ucfirst(str_replace('_', ' ', (string) $status))];
@endphp

<span {{ $attributes->merge(['class' => 'badge text-bg-'.$warna]) }}>{{ $label }}</span>
