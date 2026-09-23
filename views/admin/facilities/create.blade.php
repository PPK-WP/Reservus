@extends('layouts.app')

@section('title', 'Tambah Fasilitas — '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="h3 mb-1">Tambah Fasilitas</h1>
        <p class="text-muted mb-0">Fasilitas baru langsung berstatus aktif dan dapat direservasi.</p>
    </div>

    @include('admin.facilities._form', ['facility' => null])
</div>
@endsection