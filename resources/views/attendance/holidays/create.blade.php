@extends('layouts.dashboard')

@section('title', 'Tambah Hari Libur')

@section('content')
<div class="max-w-lg mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('work-schedule-rules.index') }}" class="text-gray-400 hover:text-gray-600">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Hari Libur</h1>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('holidays.store') }}" class="bg-white shadow rounded-lg divide-y divide-gray-100">
        @csrf

        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Nama Hari Libur <span class="text-red-500">*</span></label>
                <input type="text" name="holiday_name" value="{{ old('holiday_name') }}"
                       class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Contoh: Hari Raya Idul Fitri">
                @error('holiday_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="holiday_start_date" value="{{ old('holiday_start_date') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('holiday_start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="holiday_end_date" value="{{ old('holiday_end_date') }}"
                           class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('holiday_end_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="px-6 py-4 flex justify-end gap-3">
            <a href="{{ route('work-schedule-rules.index') }}"
               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
