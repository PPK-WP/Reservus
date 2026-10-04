@props(['href', 'aktif'])

{{-- Tautan navbar dengan penanda halaman aktif (DESIGN.md §6.1). $aktif = pola request()->is(). --}}
@php($sedangDibuka = request()->is($aktif))

<li class="nav-item">
    <a href="{{ $href }}" @if ($sedangDibuka) aria-current="page" @endif
       {{ $attributes->class(['nav-link', 'active' => $sedangDibuka]) }}>{{ $slot }}</a>
</li>
