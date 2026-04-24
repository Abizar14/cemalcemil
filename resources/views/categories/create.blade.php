@php
    $title = 'Tambah Kategori';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Manajemen Kategori')
@section('panel-title', 'Tambah kategori baru')
@section('panel-description', 'Buat kelompok menu baru agar produk lebih terstruktur di aplikasi kasir.')

@section('panel-content')
    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('categories.store') }}">
            @include('categories.form', ['submitLabel' => 'Simpan Kategori'])
        </form>
    </section>
@endsection
