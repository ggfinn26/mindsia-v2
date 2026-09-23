@extends('layouts.dashboard')

@section('title', 'Buat Sosialisasi')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('socializations.index') }}" class="text-gray-500 hover:text-gray-700">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Buat Sosialisasi Baru</h1>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('socializations.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-5">
        @csrf

        {{-- Cabang: auto-set jika branch-level employee, pilih jika area-level/HQ --}}
        @if ($employee->branch_id)
            {{-- Branch-level: auto, tampilkan sebagai info saja --}}
            <input type="hidden" name="branch_id" value="{{ $employee->branch_id }}">
            <div class="bg-gray-50 border border-gray-200 rounded-md px-4 py-3 text-sm text-gray-700">
                <span class="font-medium">Cabang:</span>
                {{ $branches->first()?->branch_name ?? 'Cabang kamu' }}
                <span class="text-gray-400 text-xs ml-1">(otomatis dari profil)</span>
            </div>
        @else
            {{-- Area/HQ-level: pilih cabang yang ada di scope mereka --}}
            <div>
                <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Cabang Penyelenggara <span class="text-red-500">*</span>
                    @if ($employee->area_id)
                        <span class="text-xs text-gray-400 font-normal ml-1">(cabang di area kamu)</span>
                    @endif
                </label>
                <select name="branch_id" id="branch_id" required
                    class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('branch_id') border-red-300 @enderror">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>
                @error('branch_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        <div>
            <label for="location_name" class="block text-sm font-medium text-gray-700 mb-1">
                Nama Lokasi <span class="text-red-500">*</span>
            </label>
            <input type="text" name="location_name" id="location_name" value="{{ old('location_name') }}" required
                placeholder="Contoh: SMAN 1 Makassar — Aula Utama"
                class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('location_name') border-red-300 @enderror">
            @error('location_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="institution_id" class="block text-sm font-medium text-gray-700 mb-1">
                Institusi / Sekolah
                <span class="text-gray-400 text-xs font-normal">(opsional — kosongkan jika lokasi bebas)</span>
            </label>
            <select name="institution_id" id="institution_id"
                class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">-- Tidak Ada --</option>
                @foreach ($institutions as $institution)
                    <option value="{{ $institution->id }}" {{ old('institution_id') == $institution->id ? 'selected' : '' }}>
                        {{ $institution->institution_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="border-t border-gray-100 pt-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Fee Partner <span class="text-gray-400 font-normal">(opsional)</span></h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="partner_fee_amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Fee (Rp)</label>
                    <input type="number" name="partner_fee_amount" id="partner_fee_amount" value="{{ old('partner_fee_amount') }}"
                        min="0" step="1000" placeholder="0"
                        class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="partner_fee_due_date" class="block text-sm font-medium text-gray-700 mb-1">Tenggat Fee</label>
                    <input type="date" name="partner_fee_due_date" id="partner_fee_due_date" value="{{ old('partner_fee_due_date') }}"
                        class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-4">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="schedule_now" value="1" {{ old('schedule_now') ? 'checked' : '' }}
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span class="text-sm text-gray-700">Langsung tandai sebagai <strong>Terjadwal</strong></span>
            </label>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('socializations.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
