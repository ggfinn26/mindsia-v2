@extends('layouts.dashboard')

@section('title', 'Edit Kelas — ' . $class->class_name)
@section('header_title', 'Edit Kelas')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('classrooms.show', $class) }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Edit Kelas</h1>
            <p class="mt-0.5 text-sm" style="color: var(--dash-text-muted);">{{ $class->class_name }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('classrooms.update', $class) }}" method="POST">
            @csrf @method('PUT')
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Nama Kelas <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" name="class_name" required
                           value="{{ old('class_name', $class->class_name) }}"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    @error('class_name')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>

                <div x-data="tutorDropdown({{ Illuminate\Support\Js::from($tutors) }}, {{ Illuminate\Support\Js::from(old('tutor_id', $class->tutor_id)) }})"
                     @click.away="open = false" @keydown.escape.window="open = false">
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Tutor <span style="color:#dc2626;">*</span>
                    </label>
                    <div class="relative">
                        <div @click="open = !open"
                             class="w-full rounded-lg px-3 py-2 text-sm flex justify-between items-center cursor-pointer"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                            <span :class="selectedId ? '' : 'opacity-50'" x-text="selectedLabel || 'Pilih Tutor'"></span>
                            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-text-muted);">expand_more</span>
                        </div>
                        <div x-show="open" x-transition x-cloak
                             class="absolute z-50 w-full mt-1 rounded-2xl shadow-xl overflow-hidden"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                            <div class="p-2" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-2 text-[16px]" style="color: var(--dash-text-muted);">search</span>
                                    <input type="text" x-model="search" placeholder="Cari tutor..."
                                           style="padding-left: 2.5rem !important; background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"
                                           class="w-full pr-3 py-1.5 rounded-lg text-sm focus:outline-none"
                                           @click.stop>
                                </div>
                            </div>
                            <ul class="max-h-52 overflow-y-auto py-1">
                                <template x-if="filtered.length === 0">
                                    <li class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                </template>
                                <template x-for="t in filtered" :key="t.id">
                                    <li @click="select(t)"
                                        class="px-4 py-2 text-sm cursor-pointer flex justify-between items-center hover:bg-blue-500 hover:text-white transition-colors"
                                        :class="selectedId == t.id ? 'font-semibold' : ''"
                                        :style="selectedId == t.id ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary-text);' : 'color: var(--dash-text-body);'">
                                        <span x-text="t.name"></span>
                                        <span class="text-xs opacity-60 ml-2" x-text="t.class_count + ' kelas'"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" name="tutor_id" :value="selectedId">
                    @error('tutor_id')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('classrooms.show', $class) }}"
                   class="px-5 py-2 rounded-lg text-sm font-medium transition-colors"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</a>
                <button type="submit"
                        class="px-5 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">Simpan</button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('head-scripts')
    @vite('resources/js/classroom-edit.js')
@endpush
