@extends('layouts.dashboard')

@section('title', 'Edit Kelas - ' . $class->class_name)
@section('header_title', 'Edit Kelas')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('classrooms.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Semua Kelas
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Akademik</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Kelas</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui data nama kelas atau alokasi tutor untuk kelas {{ $class->class_name }}.
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
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">edit_note</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Kelas</h2>
        </div>

        <form action="{{ route('classrooms.update', $class) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">
                {{-- Nama Kelas --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="class_name" required
                           value="{{ old('class_name', $class->class_name) }}"
                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('class_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tutor Dropdown --}}
                <div x-data="tutorDropdown({{ Illuminate\Support\Js::from($tutors) }}, {{ Illuminate\Support\Js::from(old('tutor_id', $class->tutor_id)) }})"
                     @click.away="open = false" @keydown.escape.window="open = false"
                     class="relative">
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Tutor <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div @click="open = !open"
                             class="w-full text-sm px-3.5 py-2.5 rounded-lg cursor-pointer flex justify-between items-center transition-all"
                             style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);">
                            <span :style="selectedId ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'" x-text="selectedLabel || 'Pilih Tutor'"></span>
                            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-text-muted);">expand_more</span>
                        </div>
                        <div x-show="open" x-transition x-cloak
                             class="absolute z-50 w-full mt-1 rounded-xl shadow-xl overflow-hidden"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                            <div class="p-2.5" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[16px]"
                                          style="color: var(--dash-text-muted);">search</span>
                                    <input type="text" x-model="search" placeholder="Cari tutor..."
                                           style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           class="w-full pr-3 py-1.5 rounded-lg text-sm focus:outline-none transition-all"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'"
                                           @click.stop>
                                </div>
                            </div>
                            <ul class="max-h-52 overflow-y-auto py-1 divide-y" style="border-color: var(--dash-card-border);">
                                <template x-if="filtered.length === 0">
                                    <li class="px-4 py-3 text-sm text-center" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                </template>
                                <template x-for="t in filtered" :key="t.id">
                                    <li @click="select(t)"
                                        class="px-4 py-2.5 text-sm cursor-pointer transition-colors"
                                        :style="selectedId == t.id ? 'background-color: var(--dash-nav-hover-bg); color: var(--dash-primary-text); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor=(selectedId == t.id ? 'var(--dash-nav-hover-bg)' : 'transparent')">
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium" x-text="t.name"></span>
                                            <span class="text-xs ml-2 px-2 py-0.5 rounded-full" style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);" x-text="t.class_count + ' kelas'"></span>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" name="tutor_id" :value="selectedId">
                    @error('tutor_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('classrooms.show', $class) }}"
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

@push('head-scripts')
    @vite('resources/js/classroom-edit.js')
@endpush
