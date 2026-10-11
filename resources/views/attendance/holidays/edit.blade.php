@extends('layouts.dashboard')

@section('title', 'Edit Hari Libur - MINDSIA')
@section('header_title', 'Hari Libur')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('holidays.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Kelola Hari Libur
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Absensi & Jadwal</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Hari Libur: {{ $holiday->holiday_name }}</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui informasi hari libur dan rentang tanggal pelaksanaan.
        </p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: var(--dash-danger);">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="px-6 py-4 flex items-center gap-2 rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">edit_calendar</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Hari Libur</h2>
        </div>

        <form action="{{ route('holidays.update', $holiday) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">
                {{-- Nama Hari Libur --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Nama Hari Libur <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="holiday_name" value="{{ old('holiday_name', $holiday->holiday_name) }}" required
                           placeholder="Contoh: Hari Raya Idul Fitri"
                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('holiday_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Mulai & Tanggal Selesai --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="holiday_start_date" value="{{ old('holiday_start_date', $holiday->holiday_start_date->format('Y-m-d')) }}" required
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('holiday_start_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="holiday_end_date" value="{{ old('holiday_end_date', $holiday->holiday_end_date->format('Y-m-d')) }}" required
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('holiday_end_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 rounded-b-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <div>
                    @can('attendance.holiday.manage')
                        <button type="button"
                                @click="showConfirmModal = true; confirmTitle = 'Hapus Hari Libur'; confirmMessage = 'Yakin ingin menghapus hari libur {{ addslashes($holiday->holiday_name) }}? Tindakan tidak bisa dibatalkan.'; confirmActionUrl = '{{ route('holidays.destroy', $holiday) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-danger);">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                            Hapus Hari Libur
                        </button>
                    @endcan
                </div>
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('holidays.index') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                       style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="showConfirmModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: #fee2e2; color: var(--dash-danger);">
                            <span class="material-symbols-outlined text-[24px]">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showConfirmModal = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="button" @click="$refs.confirmForm.submit()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-danger);"
                            x-text="confirmActionText">
                    </button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
