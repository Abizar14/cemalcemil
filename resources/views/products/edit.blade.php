@php
    $title = 'Edit Produk';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Manajemen Produk')
@section('panel-title', 'Edit produk')
@section('panel-description', 'Perbarui harga, kategori, atau status produk supaya data kasir tetap akurat.')

@section('panel-content')
    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('products.form', ['submitLabel' => 'Update Produk'])
        </form>
    </section>
@endsection
