@php
    $title = 'Tambah Produk';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Manajemen Produk')
@section('panel-title', 'Tambah produk baru')
@section('panel-description', 'Masukkan menu baru lengkap dengan kategori dan harga jualnya.')

@section('panel-content')
    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @include('products.form', ['submitLabel' => 'Simpan Produk'])
        </form>
    </section>
@endsection
