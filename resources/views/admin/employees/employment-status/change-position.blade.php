@extends('layouts.app')

@section('title', 'Ubah Posisi — ' . $status->employee->full_name)

@section('content')
    <div class="container py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Ubah Posisi Karyawan</h1>
            <a href="{{ route('employees.show', $status->employee) }}"
               class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="p-8">
                <div class="grid grid-cols-2 gap-6 mb-6 pb-6 border-b">
                    <div>
                        <p class="text-sm text-gray-600">Nama Karyawan</p>
                        <p class="text-lg font-semibold">{{ $status->employee->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Posisi Saat Ini</p>
                        <p class="text-lg font-semibold">{{ $status->position->position_name }}</p>
                    </div>
                </div>

                <form action="{{ route('employment-statuses.change-position.store', $status) }}"
                      method="POST"
                      class="space-y-6">
                    @csrf

                    <div>
                        <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Posisi Baru
                        </label>
                        <select id="position_id"
                                name="position_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('position_id') border-red-500 @enderror">
                            <option value="">-- Pilih Posisi --</option>
                            @foreach ($positions as $position)
                                @if ($position->id != $status->position_id)
                                    <option value="{{ $position->id }}" @selected(old('position_id') == $position->id)>
                                        {{ $position->position_name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('position_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Efektif
                        </label>
                        <input type="date"
                               id="effective_date"
                               name="effective_date"
                               value="{{ old('effective_date') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('effective_date') border-red-500 @enderror">
                        @error('effective_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan (Opsional)
                        </label>
                        <textarea id="notes"
                                  name="notes"
                                  rows="4"
                                  placeholder="Alasan perubahan posisi atau keterangan lainnya"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-6 border-t">
                        <button type="submit"
                                class="px-6 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600">
                            Ubah Posisi
                        </button>
                        <a href="{{ route('employees.show', $status->employee) }}"
                           class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
