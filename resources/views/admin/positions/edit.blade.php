@extends('layouts.dashboard')

@section('title', 'Edit Posisi - MINDSIA')
@section('header_title', 'Edit Posisi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('positions.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Kelola Posisi
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Organisasi</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Posisi: {{ $position->position_name }}</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui data posisi, role sistem terkait, dan urutan hierarki.
        </p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center gap-2"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">edit</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Posisi</h2>
        </div>

        <form action="{{ route('positions.update', $position->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">
                {{-- Nama Posisi --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Nama Posisi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="position_name" value="{{ old('position_name', $position->position_name) }}" required
                           placeholder="Contoh: Manager Operasional"
                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('position_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role Sistem Terkait --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Role Sistem Terkait <span class="text-red-500">*</span>
                    </label>
                    <select name="role_id" required
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $position->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Hierarchy Order --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Urutan Hierarki <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="hierarchy_order" value="{{ old('hierarchy_order', $position->hierarchy_order) }}" required
                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                        Gunakan angka. Angka lebih kecil menandakan hierarki lebih tinggi (contoh: 1 untuk Direktur).
                    </p>
                    @error('hierarchy_order')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('positions.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
