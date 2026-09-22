@extends('layouts.app')

@section('title', 'Edit Fasilitas — '.config('app.name'))

@section('content')
<div class="container">
    <div class="mb-4">
        <h1 class="h3 mb-1">Edit Fasilitas</h1>
        <p class="text-muted mb-0">Ubah data master berikut. Status fasilitas diubah lewat tombol di halaman daftar.</p>
    </div>

    @include('admin.facilities._form', ['facility' => $facility])
</div>
@endsection