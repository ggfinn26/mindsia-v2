@extends('layouts.dashboard')

@section('title', 'Buat Surat Keluar')
@section('header_title', 'Buat Surat Keluar')

@section('content')
@php
use Carbon\Carbon;
$formConfig = [
    'templates' => App\Models\LetterTemplate::where('is_active', true)
        ->whereIn('letter_category', ['generated','marketing','announcement'])
        ->orderBy('template_name')->get()
        ->map(fn($t) => ['id' => $t->id, 'name' => $t->template_name, 'category' => $t->letter_category])
        ->values()->all(),

    'branches' => App\Models\Branch::where('is_active', true)->orderBy('branch_name')->get()
        ->map(fn($b) => ['id' => $b->id, 'name' => $b->branch_name])
        ->values()->all(),

    'employees' => App\Models\Employee::where('is_active', true)
        ->with('currentStatus.position')
        ->orderBy('full_name')->get()
        ->map(fn($e) => [
            'id'                           => $e->id,
            'name'                         => $e->full_name,
            'jabatan_pegawai'              => $e->currentStatus?->position?->position_name ?? '',
            'divisi_pegawai'               => '',
            'agama_pegawai'                => '',
            'alamat_pegawai'               => '',
            'email_pegawai'                => $e->email ?? '',
            'wa_pegawai'                   => $e->whatsapp_number ?? '',
            'nomor_identitas_pegawai'      => $e->employee_code ?? '',
            'tempat_tanggal_lahir_pegawai' => $e->birthdate ? Carbon::parse($e->birthdate)->translatedFormat('d F Y') : '',
            'laki_laki_atau_perempuan'     => $e->gender === 'L' ? 'Laki-Laki' : 'Perempuan',
            'tanggal_mulai_kontrak'        => $e->currentStatus?->join_date
                ? Carbon::parse($e->currentStatus->join_date)->translatedFormat('d F Y') : '',
            'tanggal_berakhir_kontrak'     => $e->currentStatus?->contract_end_date
                ? Carbon::parse($e->currentStatus->contract_end_date)->translatedFormat('d F Y') : '',
        ])->values()->all(),

    'templateVarsUrl' => route('letter-templates.template-vars'),

    'old' => [
        'template_id' => old('letter_template_id'),
        'branch_id'   => old('branch_id'),
        'employee_id' => old('employee_id'),
        'signer_id'   => old('signer_employee_id'),
        'manual_vars' => old('manual_vars', []),
    ],
];
@endphp

<script>window.__letterForm = @json($formConfig);</script>

<div class="max-w-3xl mx-auto space-y-6 sm:space-y-8 pb-10" x-data="letterGeneratorForm(window.__letterForm)">
    <div>
        <a href="{{ route('out-letters-generate.index') }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-slate-500 hover:text-slate-800 transition-colors mb-3">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali ke daftar surat
        </a>
        <h2 class="font-jakarta text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Buat Surat Keluar</h2>
        <p class="mt-2 text-[14px] text-slate-600">Pilih template lalu lengkapi isian. Dokumen dibuat sebagai <strong>Draft</strong>.</p>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 p-4 rounded-lg">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px] text-red-600 mt-0.5">error</span>
                <div>
                    <p class="text-[13px] font-bold text-red-800">Terdapat kesalahan</p>
                    <ul class="mt-1 list-disc pl-4 text-[13px] text-red-700 space-y-1">
                        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('out-letters-generate.store') }}"
          class="bg-white border border-slate-200 shadow-sm rounded-xl"
          @submit="isSubmitting = true">
        @csrf

        <div class="p-5 sm:p-7 space-y-7">

            {{-- Template ──────────────────────────────────────────────────── --}}
            <div class="space-y-4">
                <h3 class="font-jakarta text-[15px] font-bold text-slate-800 pb-2 border-b border-slate-100">Template Surat</h3>
                <div>
                    <label class="mb-1.5 block text-[13px] font-bold text-slate-700">Template <span class="text-red-500">*</span></label>
                    <div class="relative" @click.away="showTemplateDD = false">
                        <button type="button" @click="showTemplateDD = !showTemplateDD; showBranchDD = false; showEmployeeDD = false; showSignerDD = false"
                                class="w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] text-left flex items-center justify-between gap-2 focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                            <span x-text="selectedTemplateName" :class="selectedTemplateId ? 'text-slate-900' : 'text-slate-400'" class="truncate"></span>
                            <span class="material-symbols-outlined text-[18px] text-slate-400 flex-shrink-0">expand_more</span>
                        </button>
                        <input type="hidden" name="letter_template_id" :value="selectedTemplateId">
                        <div x-show="showTemplateDD" x-transition x-cloak
                             class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden">
                            <div class="p-2 bg-slate-50 border-b border-slate-100">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-2 text-slate-400 text-[16px]">search</span>
                                    <input type="text" x-model="searchTemplate" placeholder="Cari template..."
                                           style="padding-left: 2.5rem !important" class="w-full pr-3 py-1.5 bg-white border border-slate-200 rounded-md text-[13px] focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                                           @click.stop>
                                </div>
                            </div>
                            <ul class="max-h-52 overflow-y-auto py-1">
                                <li x-show="filteredTemplates.length === 0" class="px-4 py-2 text-[13px] text-slate-500 text-center">Tidak ditemukan</li>
                                <template x-for="item in filteredTemplates" :key="item.id">
                                    <li @click="selectTemplate(item.id)"
                                        class="px-4 py-2 text-[13px] text-slate-700 hover:bg-[#215aac] hover:text-white cursor-pointer"
                                        :class="selectedTemplateId == item.id ? 'bg-[#EEF4FF] font-semibold' : ''"
                                        x-text="item.name"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dynamic sections ──────────────────────────────────────────── --}}
            <div x-show="templateVars.length > 0" x-cloak class="space-y-7">

                {{-- Auto vars info ──────────────────────────────────────── --}}
                <div x-show="autoVars.length > 0">
                    <p class="text-[12px] font-semibold uppercase tracking-wide text-slate-400 mb-2">Diisi Otomatis Sistem</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="v in autoVars" :key="v.key">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-[12px] text-slate-500">
                                <span class="material-symbols-outlined text-[14px]">auto_awesome</span>
                                <span x-text="v.label"></span>
                                <span x-show="v.note" class="text-slate-400" x-text="v.note ? '— ' + v.note : ''"></span>
                            </span>
                        </template>
                    </div>
                </div>

                {{-- Cabang ──────────────────────────────────────────────── --}}
                <div x-show="showBranch" x-cloak class="space-y-4">
                    <h3 class="font-jakarta text-[15px] font-bold text-slate-800 pb-2 border-b border-slate-100">Cabang</h3>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-[13px] font-bold text-slate-700">Cabang <span class="text-red-500">*</span></label>
                            <div class="relative" @click.away="showBranchDD = false">
                                <button type="button" @click="showBranchDD = !showBranchDD; showTemplateDD = false; showEmployeeDD = false; showSignerDD = false"
                                        class="w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] text-left flex items-center justify-between gap-2 focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                                    <span x-text="selectedBranchName" :class="selectedBranchId ? 'text-slate-900' : 'text-slate-400'" class="truncate"></span>
                                    <span class="material-symbols-outlined text-[18px] text-slate-400 flex-shrink-0">expand_more</span>
                                </button>
                                <input type="hidden" name="branch_id" :value="selectedBranchId">
                                <div x-show="showBranchDD" x-transition x-cloak
                                     class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden">
                                    <div class="p-2 bg-slate-50 border-b border-slate-100">
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-3 top-2 text-slate-400 text-[16px]">search</span>
                                            <input type="text" x-model="searchBranch" placeholder="Cari cabang..."
                                                   style="padding-left: 2.5rem !important" class="w-full pr-3 py-1.5 bg-white border border-slate-200 rounded-md text-[13px] focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                                                   @click.stop>
                                        </div>
                                    </div>
                                    <ul class="max-h-52 overflow-y-auto py-1">
                                        <li x-show="filteredBranches.length === 0" class="px-4 py-2 text-[13px] text-slate-500 text-center">Tidak ditemukan</li>
                                        <template x-for="item in filteredBranches" :key="item.id">
                                            <li @click="selectBranch(item.id)"
                                                class="px-4 py-2 text-[13px] text-slate-700 hover:bg-[#215aac] hover:text-white cursor-pointer"
                                                :class="selectedBranchId == item.id ? 'bg-[#EEF4FF] font-semibold' : ''"
                                                x-text="item.name"></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div x-show="templateVars.some(v => v.key === 'kota_cabang')">
                            <label class="mb-1.5 block text-[13px] font-bold text-slate-700">Teks di Dokumen <span class="text-slate-400 font-normal text-[12px]">(bisa diubah)</span></label>
                            <input type="text" name="manual_vars[kota_cabang]" x-model="values['kota_cabang']"
                                   class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] focus:bg-white focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                        </div>
                    </div>
                </div>

                {{-- Pegawai ───────────────────────────────────────────────── --}}
                <div x-show="showEmployee" x-cloak class="space-y-4">
                    <h3 class="font-jakarta text-[15px] font-bold text-slate-800 pb-2 border-b border-slate-100">Pegawai yang Dituju</h3>
                    <div>
                        <label class="mb-1.5 block text-[13px] font-bold text-slate-700">Nama Pegawai</label>
                        <div class="relative" @click.away="showEmployeeDD = false">
                            <button type="button" @click="showEmployeeDD = !showEmployeeDD; showTemplateDD = false; showBranchDD = false; showSignerDD = false"
                                    class="w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] text-left flex items-center justify-between gap-2 focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                                <span x-text="selectedEmployeeName" :class="selectedEmployeeId ? 'text-slate-900' : 'text-slate-400'" class="truncate"></span>
                                <span class="material-symbols-outlined text-[18px] text-slate-400 flex-shrink-0">expand_more</span>
                            </button>
                            <input type="hidden" name="employee_id" :value="selectedEmployeeId">
                            <div x-show="showEmployeeDD" x-transition x-cloak
                                 class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden">
                                <div class="p-2 bg-slate-50 border-b border-slate-100">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-2 text-slate-400 text-[16px]">search</span>
                                        <input type="text" x-model="searchEmployee" placeholder="Cari pegawai..."
                                               style="padding-left: 2.5rem !important" class="w-full pr-3 py-1.5 bg-white border border-slate-200 rounded-md text-[13px] focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                                               @click.stop>
                                    </div>
                                </div>
                                <ul class="max-h-52 overflow-y-auto py-1">
                                    <li x-show="filteredEmployees.length === 0" class="px-4 py-2 text-[13px] text-slate-500 text-center">Tidak ditemukan</li>
                                    <template x-for="item in filteredEmployees" :key="item.id">
                                        <li @click="selectEmployee(item.id)"
                                            class="px-4 py-2 text-[13px] text-slate-700 hover:bg-[#215aac] hover:text-white cursor-pointer"
                                            :class="selectedEmployeeId == item.id ? 'bg-[#EEF4FF] font-semibold' : ''"
                                            x-text="item.name"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div x-show="varsByType('employee_derived').length > 0" class="grid gap-4 sm:grid-cols-2">
                        <template x-for="v in varsByType('employee_derived')" :key="v.key">
                            <div>
                                <label class="mb-1 block text-[13px] font-medium text-slate-600" x-text="v.label"></label>
                                <input type="text" :name="'manual_vars[' + v.key + ']'" x-model="values[v.key]"
                                       class="block w-full min-h-[40px] px-3 py-2 rounded-lg border border-slate-200 bg-[#F8FAFC] text-[13px] focus:bg-white focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Penandatangan ────────────────────────────────────────── --}}
                <div x-show="showSigner" x-cloak class="space-y-4">
                    <h3 class="font-jakarta text-[15px] font-bold text-slate-800 pb-2 border-b border-slate-100">Penandatangan</h3>
                    <div>
                        <label class="mb-1.5 block text-[13px] font-bold text-slate-700">Pilih Penandatangan</label>
                        <div class="relative" @click.away="showSignerDD = false">
                            <button type="button" @click="showSignerDD = !showSignerDD; showTemplateDD = false; showBranchDD = false; showEmployeeDD = false"
                                    class="w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] text-left flex items-center justify-between gap-2 focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                                <span x-text="selectedSignerName" :class="selectedSignerId ? 'text-slate-900' : 'text-slate-400'" class="truncate"></span>
                                <span class="material-symbols-outlined text-[18px] text-slate-400 flex-shrink-0">expand_more</span>
                            </button>
                            <input type="hidden" name="signer_employee_id" :value="selectedSignerId">
                            <div x-show="showSignerDD" x-transition x-cloak
                                 class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg overflow-hidden">
                                <div class="p-2 bg-slate-50 border-b border-slate-100">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-2 text-slate-400 text-[16px]">search</span>
                                        <input type="text" x-model="searchSigner" placeholder="Cari penandatangan..."
                                               style="padding-left: 2.5rem !important" class="w-full pr-3 py-1.5 bg-white border border-slate-200 rounded-md text-[13px] focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                                               @click.stop>
                                    </div>
                                </div>
                                <ul class="max-h-52 overflow-y-auto py-1">
                                    <li x-show="filteredSigners.length === 0" class="px-4 py-2 text-[13px] text-slate-500 text-center">Tidak ditemukan</li>
                                    <template x-for="item in filteredSigners" :key="item.id">
                                        <li @click="selectSigner(item.id)"
                                            class="px-4 py-2 text-[13px] text-slate-700 hover:bg-[#215aac] hover:text-white cursor-pointer"
                                            :class="selectedSignerId == item.id ? 'bg-[#EEF4FF] font-semibold' : ''"
                                            x-text="item.name"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div x-show="varsByType('signer_derived').length > 0" class="grid gap-4 sm:grid-cols-2">
                        <template x-for="v in varsByType('signer_derived')" :key="v.key">
                            <div>
                                <label class="mb-1 block text-[13px] font-medium text-slate-600" x-text="v.label"></label>
                                <input type="text" :name="'manual_vars[' + v.key + ']'" x-model="values[v.key]"
                                       class="block w-full min-h-[40px] px-3 py-2 rounded-lg border border-slate-200 bg-[#F8FAFC] text-[13px] focus:bg-white focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Date + Text vars ─────────────────────────────────────── --}}
                <div x-show="dateVars.length > 0 || textVars.length > 0" x-cloak class="space-y-4">
                    <h3 class="font-jakarta text-[15px] font-bold text-slate-800 pb-2 border-b border-slate-100">Isian Dokumen</h3>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <template x-for="v in dateVars" :key="v.key">
                            <div>
                                <label class="mb-1.5 block text-[13px] font-bold text-slate-700" x-text="v.label"></label>
                                <input type="date" :name="'manual_vars[' + v.key + ']'" x-model="values[v.key]"
                                       class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] focus:bg-white focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                            </div>
                        </template>
                        <template x-for="v in textVars" :key="v.key">
                            <div>
                                <label class="mb-1.5 block text-[13px] font-bold text-slate-700" x-text="v.label"></label>
                                <input type="text" :name="'manual_vars[' + v.key + ']'" x-model="values[v.key]"
                                       class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] focus:bg-white focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Repeatable vars ──────────────────────────────────────── --}}
                <div x-show="repeatableVars.length > 0" x-cloak class="space-y-5">
                    <h3 class="font-jakarta text-[15px] font-bold text-slate-800 pb-2 border-b border-slate-100">Daftar Isian (bisa ditambah)</h3>
                    <template x-for="v in repeatableVars" :key="v.key">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-[13px] font-bold text-slate-700" x-text="v.label"></label>
                                <button type="button" @click="addRow(v.key)"
                                        class="inline-flex items-center gap-1 text-[12px] font-semibold text-[#215aac] hover:text-[#194a91]">
                                    <span class="material-symbols-outlined text-[16px]">add</span> Tambah baris
                                </button>
                            </div>
                            <template x-for="(row, i) in getRows(v.key)" :key="i">
                                <div class="flex gap-2">
                                    <input type="text"
                                           :name="'manual_vars[' + v.key + '][' + i + ']'"
                                           :value="row"
                                           @input="updateRow(v.key, i, $event.target.value)"
                                           class="block flex-1 min-h-[40px] px-3 py-2 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[13px] focus:bg-white focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                                    <button type="button" @click="removeRow(v.key, i)"
                                            x-show="getRows(v.key).length > 1"
                                            class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">remove_circle</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Metadata ────────────────────────────────────────────── --}}
                <div class="space-y-4">
                    <h3 class="font-jakarta text-[15px] font-bold text-slate-800 pb-2 border-b border-slate-100">Metadata Surat</h3>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-[13px] font-bold text-slate-700">Penerima <span class="text-slate-400 font-normal text-[12px]">(ditampilkan di daftar)</span></label>
                            <input type="text" name="recipient" value="{{ old('recipient') }}" maxlength="255"
                                   class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] focus:bg-white focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[13px] font-bold text-slate-700">Catatan Internal <span class="text-slate-400 font-normal text-[12px]">(tidak tercetak)</span></label>
                            <input type="text" name="notes" value="{{ old('notes') }}"
                                   class="block w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 bg-[#F8FAFC] text-[14px] focus:bg-white focus:outline-none focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac] transition-colors">
                        </div>
                    </div>
                </div>

            </div>{{-- end dynamic --}}

        </div>

        <div class="border-t border-slate-100 bg-slate-50 p-5 sm:px-7 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 rounded-b-xl">
            <a href="{{ route('out-letters-generate.index') }}"
               class="w-full sm:w-auto inline-flex justify-center min-h-[44px] items-center border border-slate-300 bg-white px-5 rounded-lg text-[14px] font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit" :disabled="isSubmitting || !selectedTemplateId"
                    class="w-full sm:w-auto inline-flex justify-center min-h-[44px] items-center gap-2 bg-[#215aac] px-6 rounded-lg text-[14px] font-bold text-white hover:bg-[#194a91] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!isSubmitting" class="material-symbols-outlined text-[18px]">draft</span>
                <span x-show="!isSubmitting">Buat Surat (Draft)</span>
                <span x-show="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]" x-cloak>progress_activity</span>
                <span x-show="isSubmitting" x-cloak>Memproses...</span>
            </button>
        </div>
    </form>
</div>
@endsection
