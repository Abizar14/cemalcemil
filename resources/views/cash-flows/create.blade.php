@php
    $title = 'Tambah Arus Kas';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Manajemen Arus Kas')
@section('panel-title', 'Tambah catatan arus kas')
@section('panel-description', 'Masukkan kas masuk atau keluar operasional agar pembukuan harian tetap lengkap.')

@section('panel-content')
    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('cash-flows.store') }}">
            @include('cash-flows.form', ['submitLabel' => 'Simpan Arus Kas'])
        </form>
    </section>
@endsection
