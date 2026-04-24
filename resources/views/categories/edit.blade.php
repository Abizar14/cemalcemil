@php
    $title = 'Edit Kategori';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Manajemen Kategori')
@section('panel-title', 'Edit kategori')
@section('panel-description', 'Perbarui nama atau deskripsi kategori agar sesuai dengan menu booth saat ini.')

@section('panel-content')
    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('categories.update', $category) }}">
            @csrf
            @method('PUT')
            @include('categories.form', ['submitLabel' => 'Update Kategori'])
        </form>
    </section>
@endsection
