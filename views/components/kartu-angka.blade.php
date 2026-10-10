@props(['label', 'angka', 'ikon' => null, 'href' => null, 'keterangan' => null, 'sorot' => false])

{{-- Kartu ringkasan angka. Bila ada $href, seluruh kartu bisa diklik (card-interaktif, DESIGN.md §6.3). --}}
<div @class(['card h-100 kartu-angka', 'card-interaktif' => $href, 'bg-kobalt-muda border-primary' => $sorot])>
    <div class="card-body d-flex align-items-start gap-3">
        @if ($ikon)
            <span class="ikon-kotak d-none d-sm-inline-flex"><x-ikon :nama="$ikon" /></span>
        @endif
        <div class="min-w-0">
            <div class="angka-ringkasan">{{ $angka }}</div>
            <div class="fw-semibold lh-sm label-angka">
                @if ($href)
                    <a href="{{ $href }}" class="stretched-link text-reset text-decoration-none">{{ $label }}</a>
                @else
                    {{ $label }}
                @endif
            </div>
            @if ($keterangan)
                <div class="small text-secondary d-none d-sm-block">{{ $keterangan }}</div>
            @endif
            @if ($href)
                {{-- Penanda yang selalu terlihat, juga di layar sentuh --}}
                <div class="tanda-buka mt-1" aria-hidden="true">Lihat <x-ikon nama="arrow-right" ukuran="0.9em" class="ikon-panah" /></div>
            @endif
        </div>
    </div>
</div>
