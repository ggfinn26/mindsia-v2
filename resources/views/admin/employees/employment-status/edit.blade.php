@extends('layouts.dashboard')

@section('title', 'Edit Kontrak — ' . $status->employee->full_name)

@section('content')
    <div class="container py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-[var(--dash-text-heading)]">Edit Kontrak</h1>
                <p class="text-[color:var(--dash-text-body)] mt-2">{{ $status->employee->full_name }}</p>
            </div>
            <a href="{{ route('employees.show', $status->employee) }}"
               class="px-4 py-2 text-[color:var(--dash-text-body)] border border-[var(--dash-input-border)] rounded-lg hover:bg-[var(--dash-page-bg)] inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
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

        <div class="bg-[var(--dash-card-bg)] rounded-lg shadow">
            <div class="p-8">
                <form action="{{ route('employment-statuses.update', $status) }}"
                      method="POST"
                      class="space-y-6">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="tab" value="lengkap">

                    <div class="grid grid-cols-2 gap-6">
                        <!-- Type Employment -->
                        <div>
                            <label for="type_employment" class="block text-sm font-medium text-[var(--dash-text-body)] mb-2">
                                Jenis Ketenagakerjaan
                            </label>
                            <input type="text"
                                   id="type_employment"
                                   name="type_employment"
                                   value="{{ old('type_employment', $status->type_employment) }}"
                                   placeholder="Tetap, Kontrak, Magang"
                                   class="w-full px-4 py-2 border border-[var(--dash-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--dash-primary)] @error('type_employment') border-red-500 @enderror">
                            @error('type_employment')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contract Start Date -->
                        <div>
                            <label for="contract_start_date" class="block text-sm font-medium text-[var(--dash-text-body)] mb-2">
                                Tanggal Mulai Kontrak
                            </label>
                            <input type="date"
                                   id="contract_start_date"
                                   name="contract_start_date"
                                   value="{{ old('contract_start_date', $status->contract_start_date?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-2 border border-[var(--dash-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--dash-primary)] @error('contract_start_date') border-red-500 @enderror">
                            @error('contract_start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contract End Date -->
                        <div>
                            <label for="contract_end_date" class="block text-sm font-medium text-[var(--dash-text-body)] mb-2">
                                Tanggal Akhir Kontrak (opsional)
                            </label>
                            <input type="date"
                                   id="contract_end_date"
                                   name="contract_end_date"
                                   value="{{ old('contract_end_date', $status->contract_end_date?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-2 border border-[var(--dash-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--dash-primary)] @error('contract_end_date') border-red-500 @enderror">
                            @error('contract_end_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Position -->
                    <div>
                        <label for="position_id" class="block text-sm font-medium text-[var(--dash-text-body)] mb-2">
                            Posisi
                        </label>
                        <select id="position_id"
                                name="position_id"
                                class="w-full px-4 py-2 border border-[var(--dash-input-border)] rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--dash-primary)] @error('position_id') border-red-500 @enderror">
                            <option value="">Pilih Posisi</option>
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
                        <div class="bg-[var(--dash-page-bg)] p-4 rounded-lg">
                            <p class="text-sm font-medium text-[var(--dash-text-body)] mb-2">File Kontrak Saat Ini</p>
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
                                class="px-6 py-2 bg-[var(--dash-primary)] text-white font-medium rounded-lg hover:opacity-90 inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('employees.show', $status->employee) }}"
                           class="px-6 py-2 text-[color:var(--dash-text-body)] border border-[var(--dash-input-border)] rounded-lg hover:bg-[var(--dash-page-bg)] inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
