@props(['facility', 'ratio' => '16x9'])

{{-- Foto fasilitas tanpa kolom database (DESIGN.md §5.3). Dicari berdasarkan nama berkas di
     public/images/facilities/: foto spesifik (slug nama) lalu cadangan per tipe. --}}
@php
    $slug = \Illuminate\Support\Str::slug($facility->name);
    $kandidat = [
        "images/facilities/{$slug}.webp",
        "images/facilities/{$slug}.jpg",
        "images/facilities/tipe-{$facility->type}.webp",
        "images/facilities/tipe-{$facility->type}.jpg",
    ];
    $foto = collect($kandidat)->first(fn ($p) => file_exists(public_path($p)));
@endphp

<div {{ $attributes->merge(['class' => "ratio ratio-{$ratio} bg-body-secondary overflow-hidden"]) }}>
    @if ($foto)
        <img src="{{ asset($foto) }}"
             alt="Foto {{ $facility->name }} di {{ $facility->location }}"
             class="w-100 h-100 object-fit-cover" loading="lazy">
    @else
        <div class="d-flex align-items-center justify-content-center small text-secondary">
            Foto belum tersedia
        </div>
    @endif
</div>
