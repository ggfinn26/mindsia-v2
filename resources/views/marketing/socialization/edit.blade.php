@extends('layouts.dashboard')

@section('title', 'Edit Sosialisasi')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('socializations.show', $socialization) }}" class="text-gray-500 hover:text-gray-700">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit Sosialisasi</h1>
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

    <form action="{{ route('socializations.update', $socialization) }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-5">
        @csrf
        @method('PATCH')

        <div>
            <label for="location_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lokasi</label>
            <input type="text" name="location_name" id="location_name"
                value="{{ old('location_name', $socialization->location_name) }}"
                class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('location_name') border-red-300 @enderror">
            @error('location_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="border-t border-gray-100 pt-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Fee Partner</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="partner_fee_amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Fee (Rp)</label>
                    <input type="number" name="partner_fee_amount" id="partner_fee_amount"
                        value="{{ old('partner_fee_amount', $socialization->partner_fee_amount) }}"
                        min="0" step="1000" placeholder="0"
                        class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="partner_fee_due_date" class="block text-sm font-medium text-gray-700 mb-1">Tenggat Fee</label>
                    <input type="date" name="partner_fee_due_date" id="partner_fee_due_date"
                        value="{{ old('partner_fee_due_date', $socialization->partner_fee_due_date?->format('Y-m-d')) }}"
                        class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('socializations.show', $socialization) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
