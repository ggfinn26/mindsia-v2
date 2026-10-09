@extends('layouts.dashboard')

@section('title', 'Buat Kontrak — ' . $employee->full_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-8">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-end">
        <div>
            <p class="text-sm font-semibold text-indigo-600 tracking-wide uppercase mb-1">Manajemen Kontrak</p>
            <h1 class="text-2xl md:text-3xl text-[var(--dash-text-heading)] font-bold tracking-tight">Buat Kontrak Kerja</h1>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Pegawai: <span class="font-medium text-[var(--dash-text-body)]">{{ $employee->full_name }}</span></p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('employees.show', $employee->id) }}" class="inline-flex items-center justify-center px-4 py-2 border border-[var(--dash-input-border)] shadow-sm text-sm font-medium rounded-md text-[var(--dash-text-body)] bg-[var(--dash-card-bg)] hover:bg-[var(--dash-page-bg)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--dash-primary)] transition-colors">
                Kembali ke Profil
            </a>
        </div>
    </div>

    @if (session('error'))
    <div class="rounded-md bg-rose-50 border border-rose-200 p-4">
        <div class="flex items-start gap-3">
            <svg class="h-5 w-5 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-rose-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div x-data="{ activeTab: 'lengkap' }" class="bg-[var(--dash-card-bg)] shadow-sm border border-[var(--dash-card-border)] rounded-lg">

        <!-- Tab Navigation -->
        <div class="border-b border-[var(--dash-card-border)]">
            <nav class="flex -mb-px" aria-label="Tabs">
                <button @click="activeTab = 'lengkap'"
                        :class="activeTab === 'lengkap' ? 'border-indigo-500 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-[var(--dash-text-muted)] hover:text-[var(--dash-text-body)] hover:border-[var(--dash-input-border)]'"
                        class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors flex flex-col items-center justify-center gap-1">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5" :class="activeTab === 'lengkap' ? 'text-indigo-600' : 'text-[var(--dash-text-faint)]'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-base font-bold">Form Lengkap</span>
                    </div>
                    <span class="text-xs font-normal" :class="activeTab === 'lengkap' ? 'text-indigo-500' : 'text-[var(--dash-text-faint)]'">Isi seluruh detail secara manual</span>
                </button>
                <button @click="activeTab = 'cepat'"
                        :class="activeTab === 'cepat' ? 'border-indigo-500 text-indigo-600 bg-indigo-50/50' : 'border-transparent text-[var(--dash-text-muted)] hover:text-[var(--dash-text-body)] hover:border-[var(--dash-input-border)]'"
                        class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors flex flex-col items-center justify-center gap-1">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5" :class="activeTab === 'cepat' ? 'text-indigo-600' : 'text-[var(--dash-text-faint)]'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="text-base font-bold">Mode Cepat</span>
                    </div>
                    <span class="text-xs font-normal" :class="activeTab === 'cepat' ? 'text-indigo-500' : 'text-[var(--dash-text-faint)]'">Upload draft file kontrak</span>
                </button>
            </nav>
        </div>

        <!-- Tab Panels -->
        <div class="p-6 sm:p-8">

            <!-- Tab: Lengkap (Full Form) -->
            <form x-show="activeTab === 'lengkap'"
                  action="{{ route('employment-statuses.store', $employee->id) }}"
                  method="POST"
                  class="space-y-6">
                @csrf
                <input type="hidden" name="tab" value="lengkap">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Type Employment -->
                    <div>
                        <label for="type_employment" class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1.5">Jenis Ketenagakerjaan <span class="text-rose-500">*</span></label>
                        <select id="type_employment" name="type_employment" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] rounded-md shadow-sm text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] @error('type_employment') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                            <option value="">Pilih Jenis</option>
                            @foreach (['Tetap', 'Kontrak', 'Magang'] as $tipe)
                                <option value="{{ $tipe }}" @selected(old('type_employment') === $tipe)>{{ $tipe }}</option>
                            @endforeach
                        </select>
                        @error('type_employment')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contract Start Date -->
                    <div>
                        <label for="contract_start_date" class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1.5">Tanggal Mulai Kontrak <span class="text-rose-500">*</span></label>
                        <input type="date" id="contract_start_date" name="contract_start_date" value="{{ old('contract_start_date') }}" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] rounded-md shadow-sm text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] @error('contract_start_date') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                        @error('contract_start_date')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contract End Date -->
                    <div>
                        <label for="contract_end_date" class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1.5">Tanggal Akhir Kontrak <span class="text-[var(--dash-text-faint)] font-normal">(opsional)</span></label>
                        <input type="date" id="contract_end_date" name="contract_end_date" value="{{ old('contract_end_date') }}" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] rounded-md shadow-sm text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] @error('contract_end_date') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                        @error('contract_end_date')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Position -->
                <div class="pt-2">
                    <label for="position_id" class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1.5">Posisi / Jabatan <span class="text-rose-500">*</span></label>
                    <select id="position_id" name="position_id" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] rounded-md shadow-sm text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] @error('position_id') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                        <option value="">Pilih Posisi</option>
                        @foreach ($positions as $position)
                            <option value="{{ $position->id }}" @selected(old('position_id') == $position->id)>
                                {{ $position->position_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('position_id')
                        <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 mt-8">
                    <button type="submit" class="inline-flex justify-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--dash-primary)]">
                        Simpan Kontrak Lengkap
                    </button>
                    <a href="{{ route('employees.show', $employee->id) }}" class="inline-flex justify-center px-4 py-2 border border-[var(--dash-input-border)] shadow-sm text-sm font-medium rounded-md text-[var(--dash-text-body)] bg-[var(--dash-card-bg)] hover:bg-[var(--dash-page-bg)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--dash-primary)]">
                        Batal
                    </a>
                </div>
            </form>

            <!-- Tab: Cepat (Quick Upload) -->
            <form x-show="activeTab === 'cepat'" x-cloak
                  action="{{ route('employment-statuses.store', $employee->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf
                <input type="hidden" name="tab" value="cepat">

                <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 mb-6 flex gap-3">
                    <svg class="h-5 w-5 text-indigo-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-indigo-800">
                        <strong>Mode Cepat:</strong> Upload file kontrak terlebih dahulu. Tanggal mulai kontrak dapat dilengkapi di kemudian hari via menu Edit.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Type Employment -->
                    <div>
                        <label for="cepat_type_employment" class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1.5">Jenis Ketenagakerjaan <span class="text-rose-500">*</span></label>
                        <select id="cepat_type_employment" name="type_employment" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] rounded-md shadow-sm text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] @error('type_employment') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                            <option value="">Pilih Jenis</option>
                            @foreach (['Tetap', 'Kontrak', 'Magang'] as $tipe)
                                <option value="{{ $tipe }}" @selected(old('type_employment') === $tipe)>{{ $tipe }}</option>
                            @endforeach
                        </select>
                        @error('type_employment')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contract End Date -->
                    <div>
                        <label for="cepat_contract_end_date" class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1.5">Tanggal Akhir Kontrak <span class="text-[var(--dash-text-faint)] font-normal">(opsional)</span></label>
                        <input type="date" id="cepat_contract_end_date" name="contract_end_date" value="{{ old('contract_end_date') }}" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] rounded-md shadow-sm text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] @error('contract_end_date') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                        @error('contract_end_date')
                            <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Position -->
                <div class="pt-2">
                    <label for="cepat_position_id" class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1.5">Posisi / Jabatan <span class="text-rose-500">*</span></label>
                    <select id="cepat_position_id" name="position_id" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] rounded-md shadow-sm text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] @error('position_id') border-rose-500 focus:border-rose-500 focus:ring-rose-500 @enderror">
                        <option value="">Pilih Posisi</option>
                        @foreach ($positions as $position)
                            <option value="{{ $position->id }}" @selected(old('position_id') == $position->id)>
                                {{ $position->position_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('position_id')
                        <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Upload -->
                <div class="pt-2" x-data="{ fileName: null }">
                    <label class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1.5">File Kontrak <span class="text-rose-500">*</span> <span class="text-[var(--dash-text-faint)] font-normal">(PDF)</span></label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-[var(--dash-input-border)] border-dashed rounded-md hover:bg-[var(--dash-page-bg)] transition-colors relative">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-[var(--dash-text-faint)]" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="contract_file_path" class="relative cursor-pointer bg-[var(--dash-card-bg)] rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload sebuah file</span>
                                    <input id="contract_file_path" name="contract_file_path" type="file" class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] sr-only" accept=".pdf" @change="fileName = $event.target.files[0]?.name">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-[var(--dash-text-muted)]">PDF hingga 10MB</p>
                        </div>
                    </div>
                    <div x-show="fileName" x-cloak class="mt-3 flex items-center gap-2 text-sm text-emerald-600 bg-emerald-50 p-2 rounded-md border border-emerald-100">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        File terpilih: <span class="font-semibold" x-text="fileName"></span>
                    </div>
                    @error('contract_file_path')
                        <p class="text-rose-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 mt-8">
                    <button type="submit" class="inline-flex justify-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--dash-primary)]">
                        Upload & Buat Kontrak
                    </button>
                    <a href="{{ route('employees.show', $employee->id) }}" class="inline-flex justify-center px-4 py-2 border border-[var(--dash-input-border)] shadow-sm text-sm font-medium rounded-md text-[var(--dash-text-body)] bg-[var(--dash-card-bg)] hover:bg-[var(--dash-page-bg)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[var(--dash-primary)]">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
