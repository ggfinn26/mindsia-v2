@extends('layouts.app')

@section('title', 'Perpanjang Kontrak — ' . $status->employee->full_name)

@section('content')
    <div class="container py-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Perpanjang Kontrak</h1>
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
                    <div>
                        <p class="text-sm text-gray-600">Tanggal Mulai</p>
                        <p class="text-lg font-semibold">{{ $status->contract_start_date?->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tanggal Akhir Saat Ini</p>
                        <p class="text-lg font-semibold">{{ $status->contract_end_date?->format('d/m/Y') ?? 'Tidak ada batas' }}</p>
                    </div>
                </div>

                <form action="{{ route('employment-statuses.extend.store', $status) }}"
                      method="POST"
                      class="space-y-6">
                    @csrf

                    <div>
                        <label for="proposed_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Akhir Baru
                        </label>
                        <input type="date"
                               id="proposed_end_date"
                               name="proposed_end_date"
                               value="{{ old('proposed_end_date') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('proposed_end_date') border-red-500 @enderror">
                        @error('proposed_end_date')
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
                                  placeholder="Alasan perpanjangan atau keterangan lainnya"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-6 border-t">
                        <button type="submit"
                                class="px-6 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600">
                            Buat Penawaran
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
