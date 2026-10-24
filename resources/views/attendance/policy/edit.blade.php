@extends('layouts.dashboard')

@section('title', 'Edit Kebijakan Absensi - MINDSIA')
@section('header_title', 'Kebijakan Absensi')

@section('content')

<div class="max-w-3xl mx-auto pt-8 sm:pt-16 space-y-4">
    <div>
        <a href="{{ route('attendance-policies.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Kebijakan
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Kebijakan</h1>
    </div>

    @if($errors->any())
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] leading-none">error</span>
            {{ $errors->first() }}
        </div>
    @endif

    @include('attendance.policy._form')
</div>
@endsection
