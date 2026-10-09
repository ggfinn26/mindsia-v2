@extends('layouts.dashboard')

@section('title', 'Edit Divisi - MINDSIA')
@section('header_title', 'Edit Divisi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-2xl mx-auto">

    {{-- Page header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('roles.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Edit Divisi</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Ubah nama divisi: <span class="font-semibold" style="color: var(--dash-text-body);">{{ $role->name }}</span>
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm mb-6"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="p-6 sm:p-8">
                <div>
                    <label class="block text-sm font-medium mb-1.5" for="name"
                           style="color: var(--dash-text-body);">
                        Nama Divisi <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $role->name) }}"
                           required
                           class="block w-full rounded-xl text-sm px-3 py-2.5 focus:outline-none focus:ring-2"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); --tw-ring-color: var(--dash-primary);">
                </div>
            </div>
            <div class="px-6 py-4 flex justify-end"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <button type="submit"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
