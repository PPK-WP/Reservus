@props(['href', 'judul', 'ikon', 'deskripsi'])

{{-- Kartu pintasan menu; seluruh permukaan kartu bisa diklik. Isi $slot opsional (mis. badge). --}}
<div class="card h-100 card-interaktif">
    <div class="card-body d-flex align-items-start gap-3">
        <span class="ikon-kotak"><x-ikon :nama="$ikon" /></span>
        <div class="flex-grow-1">
            <h2 class="h6 mb-1">
                <a href="{{ $href }}" class="stretched-link text-reset text-decoration-none">{{ $judul }}</a>
            </h2>
            <p class="small text-secondary mb-0">{{ $deskripsi }}</p>
            {{ $slot }}
        </div>
        <x-ikon nama="arrow-right" class="ikon-panah text-primary mt-1" />
    </div>
</div>
