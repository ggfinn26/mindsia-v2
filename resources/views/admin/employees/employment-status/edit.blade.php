@extends('layouts.app')

@section('title', 'Edit Kontrak — ' . $status->employee->full_name)

@section('content')
    <div class="container py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Kontrak</h1>
                <p class="text-gray-600 mt-2">{{ $status->employee->full_name }}</p>
            </div>
            <a href="{{ route('employees.show', $status->employee) }}"
               class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                Kembali
            </a>
        </div>

        @if ($status->setup_incomplete)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-yellow-800">
                    <strong>⚠️ Setup Belum Lengkap:</strong> Kontrak ini masih dalam mode cepat. Lengkapi detail kontrak untuk menyelesaikan setup.
                </p>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow">
            <div class="p-8">
                <form action="{{ route('employment-statuses.update', $status) }}"
                      method="POST"
                      class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Type Employment -->
                        <div>
                            <label for="type_employment" class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Ketenagakerjaan
                            </label>
                            <input type="text"
                                   id="type_employment"
                                   name="type_employment"
                                   value="{{ old('type_employment', $status->type_employment) }}"
                                   placeholder="Tetap, Kontrak, Magang"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type_employment') border-red-500 @enderror">
                            @error('type_employment')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Join Date -->
                        <div>
                            <label for="join_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Mulai Kerja
                            </label>
                            <input type="date"
                                   id="join_date"
                                   name="join_date"
                                   value="{{ old('join_date', $status->join_date?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('join_date') border-red-500 @enderror">
                            @error('join_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contract Start Date -->
                        <div>
                            <label for="contract_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Mulai Kontrak
                            </label>
                            <input type="date"
                                   id="contract_start_date"
                                   name="contract_start_date"
                                   value="{{ old('contract_start_date', $status->contract_start_date?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contract_start_date') border-red-500 @enderror">
                            @error('contract_start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contract End Date -->
                        <div>
                            <label for="contract_end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Akhir Kontrak (opsional)
                            </label>
                            <input type="date"
                                   id="contract_end_date"
                                   name="contract_end_date"
                                   value="{{ old('contract_end_date', $status->contract_end_date?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contract_end_date') border-red-500 @enderror">
                            @error('contract_end_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="position_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Posisi
                        </label>
                        <select id="position_id"
                                name="position_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('position_id') border-red-500 @enderror">
                            <option value="">-- Pilih Posisi --</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}" @selected(old('position_id', $status->position_id) == $position->id)>
                                    {{ $position->position_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($status->contract_file_path)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm font-medium text-gray-700 mb-2">File Kontrak Saat Ini</p>
                            <a href="{{ Storage::url($status->contract_file_path) }}"
                               target="_blank"
                               class="text-blue-500 hover:text-blue-700 text-sm">
                                📄 Lihat File
                            </a>
                        </div>
                    @endif

                    <!-- Submit -->
                    <div class="flex gap-3 pt-6 border-t">
                        <button type="submit"
                                class="px-6 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600">
                            Simpan Perubahan
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
