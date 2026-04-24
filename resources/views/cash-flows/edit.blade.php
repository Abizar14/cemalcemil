@php
    $title = 'Edit Arus Kas';
@endphp

@extends('layouts.panel')

@section('panel-eyebrow', 'Manajemen Arus Kas')
@section('panel-title', 'Edit arus kas')
@section('panel-description', 'Perbarui catatan kas agar nominal, waktu, dan keterangannya tetap akurat.')

@section('panel-content')
    <section class="mesh-panel shadow-panel animate-rise rounded-[1.75rem] border border-white/70 p-5 sm:p-6 lg:p-8">
        <form method="POST" action="{{ route('cash-flows.update', $cashFlow) }}">
            @csrf
            @method('PUT')
            @include('cash-flows.form', ['submitLabel' => 'Update Arus Kas'])
        </form>
    </section>
@endsection
