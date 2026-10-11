@extends('layouts.dashboard')

@section('title', 'Edit Kelas — ' . $class->class_name)

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Kelas</h1>
        <a href="{{ route('classrooms.show', $class) }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('classrooms.update', $class) }}" method="POST" class="bg-white shadow sm:rounded-lg">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
                <input type="text" name="class_name" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       value="{{ old('class_name', $class->class_name) }}" />
                @error('class_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="tutorDropdown({{ Illuminate\Support\Js::from($tutors) }}, {{ Illuminate\Support\Js::from(old('tutor_id', $class->tutor_id)) }})" @click.away="open = false" @keydown.escape.window="open = false">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tutor <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div @click="open = !open"
                         class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white cursor-pointer flex justify-between items-center focus:ring-2 focus:ring-blue-500">
                        <span :class="selectedId ? 'text-gray-900' : 'text-gray-400'" x-text="selectedLabel || 'Pilih Tutor'"></span>
                        <span class="material-symbols-outlined text-[18px] text-gray-400">expand_more</span>
                    </div>
                    <div x-show="open" x-transition x-cloak
                         class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                        <div class="p-2 bg-gray-50 border-b border-gray-100">
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-2 text-gray-400 text-[16px]">search</span>
                                <input type="text" x-model="search" placeholder="Cari tutor..."
                                       style="padding-left: 2.5rem !important" class="w-full pr-3 py-1.5 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                       @click.stop>
                            </div>
                        </div>
                        <ul class="max-h-52 overflow-y-auto py-1">
                            <template x-if="filtered.length === 0">
                                <li class="px-4 py-2 text-sm text-gray-500 text-center">Tidak ditemukan</li>
                            </template>
                            <template x-for="t in filtered" :key="t.id">
                                <li @click="select(t)"
                                    class="px-4 py-2 text-sm cursor-pointer hover:bg-blue-500 hover:text-white flex justify-between items-center"
                                    :class="selectedId == t.id ? 'bg-blue-50 font-semibold text-blue-700' : 'text-gray-700'">
                                    <span x-text="t.name"></span>
                                    <span class="text-xs opacity-60 ml-2" x-text="t.class_count + ' kelas'"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
                <input type="hidden" name="tutor_id" :value="selectedId">
                @error('tutor_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3 justify-end">
            <a href="{{ route('classrooms.show', $class) }}" class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        </div>
    </form>
</div>

@endsection

@push('head-scripts')
    @vite('resources/js/classroom-edit.js')
@endpush
