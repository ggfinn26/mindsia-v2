@extends('layouts.dashboard')

@section('title', 'Tambah Aturan Jadwal Kerja')

@section('content')
<div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('work-schedule-rules.index') }}" class="text-gray-400 hover:text-gray-600">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Aturan Jadwal Kerja</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('work-schedule-rules.store') }}" class="bg-white shadow rounded-lg divide-y divide-gray-100">
        @csrf

        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nama Aturan <span class="text-red-500">*</span></label>
                <input type="text" name="setting_name" value="{{ old('setting_name') }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Contoh: Jadwal Standar, Jadwal Tutor">
                @error('setting_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('start_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('end_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Jam Istirahat Mulai</label>
                    <input type="time" name="break_start_time" value="{{ old('break_start_time') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('break_start_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Jam Istirahat Selesai</label>
                    <input type="time" name="break_end_time" value="{{ old('break_end_time') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('break_end_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Toleransi Telat (menit) <span class="text-red-500">*</span></label>
                    <input type="number" name="late_tolerance_minutes" value="{{ old('late_tolerance_minutes', 0) }}"
                           min="0" max="120"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('late_tolerance_minutes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Toleransi Pulang Cepat (menit) <span class="text-red-500">*</span></label>
                    <input type="number" name="early_leave_tolerance_minutes" value="{{ old('early_leave_tolerance_minutes', 0) }}"
                           min="0" max="120"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('early_leave_tolerance_minutes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-6 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_required" value="0">
                    <input type="checkbox" name="is_required" value="1" {{ old('is_required', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Absensi Wajib</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    <span class="text-sm font-medium text-gray-700">Aktif</span>
                </label>
            </div>
        </div>

        <div class="px-6 py-4 flex justify-end gap-3">
            <a href="{{ route('work-schedule-rules.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                Simpan Aturan
            </button>
        </div>
    </form>
</div>
@endsection
