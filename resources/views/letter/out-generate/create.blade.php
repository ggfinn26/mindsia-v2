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
        'template_id' => old('letter_template_id') ?? $preselectedTemplateId,
        'branch_id'   => old('branch_id'),
        'employee_id' => old('employee_id'),
        'signer_id'   => old('signer_employee_id'),
        'manual_vars' => old('manual_vars', []),
    ],
];
@endphp

<script>window.__letterForm = @json($formConfig);</script>

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6 pb-10" x-data="letterGeneratorForm(window.__letterForm)">
    <div>
        <a href="{{ route('out-letters-generate.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Surat Keluar
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Surat</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Buat Surat Keluar</h1>
        <p class="mt-2 text-sm" style="color: var(--dash-text-muted);">Pilih template lalu lengkapi isian. Dokumen dibuat sebagai <strong>Draft</strong>.</p>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent);">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-[20px] mt-0.5" style="color: var(--dash-danger);">error</span>
                <div>
                    <p class="text-sm font-bold" style="color: var(--dash-danger);">Terdapat kesalahan</p>
                    <ul class="mt-1 list-disc pl-4 text-sm space-y-1" style="color: var(--dash-danger);">
                        @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('out-letters-generate.store') }}"
          class="rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
          @submit="isSubmitting = true">
        @csrf

        <div class="p-5 sm:p-7 space-y-7">

            {{-- Template --}}
            <div class="space-y-4">
                <h2 class="font-jakarta text-base font-bold pb-2" style="color: var(--dash-text-heading); border-bottom: 1px solid var(--dash-card-border);">Template Surat</h2>
                <div>
                    <label class="mb-1.5 block text-sm font-medium" style="color: var(--dash-text-body);">Template <span style="color: var(--dash-danger);">*</span></label>
                    <div class="relative" @click.away="showTemplateDD = false">
                        <button type="button" @click="showTemplateDD = !showTemplateDD; showBranchDD = false; showEmployeeDD = false; showSignerDD = false"
                                class="w-full px-3 py-2 rounded-lg text-sm text-left flex items-center justify-between gap-2 focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <span x-text="selectedTemplateName" :style="selectedTemplateId ? '' : 'color: var(--dash-text-faint)'" class="truncate"></span>
                            <span class="material-symbols-outlined text-[18px] flex-shrink-0" style="color: var(--dash-text-faint);">expand_more</span>
                        </button>
                        <input type="hidden" name="letter_template_id" :value="selectedTemplateId">
                        <div x-show="showTemplateDD" x-transition x-cloak
                             class="absolute z-50 w-full mt-1 rounded-lg shadow-lg"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                            <div class="p-2 rounded-t-lg" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none" style="color: var(--dash-text-muted);">search</span>
                                    <input type="text" x-model="searchTemplate" placeholder="Cari template..."
                                           class="w-full pr-3 py-1.5 rounded-lg text-sm focus:outline-none transition-all"
                                           style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                                           @click.stop>
                                </div>
                            </div>
                            <ul class="max-h-52 overflow-y-auto py-1">
                                <li x-show="filteredTemplates.length === 0" class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                <template x-for="item in filteredTemplates" :key="item.id">
                                    <li @click="selectTemplate(item.id)"
                                        class="px-4 py-2 text-sm cursor-pointer"
                                        :style="selectedTemplateId == item.id ? 'background-color: var(--dash-nav-hover-bg); font-weight: 600; color: var(--dash-text-heading)' : 'color: var(--dash-text-body)'"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor=''"
                                        x-text="item.name"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dynamic sections --}}
            <div x-show="templateVars.length > 0" x-cloak class="space-y-7">

                {{-- Auto vars info --}}
                <div x-show="autoVars.length > 0">
                    <p class="text-[11px] font-semibold uppercase tracking-wide mb-2" style="color: var(--dash-text-faint);">Diisi Otomatis Sistem</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="v in autoVars" :key="v.key">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs"
                                  style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                                <span class="material-symbols-outlined text-[14px]">auto_awesome</span>
                                <span x-text="v.label"></span>
                                <span x-show="v.note" style="color: var(--dash-text-faint);" x-text="v.note ? '- ' + v.note : ''"></span>
                            </span>
                        </template>
                    </div>
                </div>

                {{-- Cabang --}}
                <div x-show="showBranch" x-cloak class="space-y-4">
                    <h2 class="font-jakarta text-base font-bold pb-2" style="color: var(--dash-text-heading); border-bottom: 1px solid var(--dash-card-border);">Cabang</h2>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color: var(--dash-text-body);">Cabang <span style="color: var(--dash-danger);">*</span></label>
                            <div class="relative" @click.away="showBranchDD = false">
                                <button type="button" @click="showBranchDD = !showBranchDD; showTemplateDD = false; showEmployeeDD = false; showSignerDD = false"
                                        class="w-full px-3 py-2 rounded-lg text-sm text-left flex items-center justify-between gap-2 focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <span x-text="selectedBranchName" :style="selectedBranchId ? '' : 'color: var(--dash-text-faint)'" class="truncate"></span>
                                    <span class="material-symbols-outlined text-[18px] flex-shrink-0" style="color: var(--dash-text-faint);">expand_more</span>
                                </button>
                                <input type="hidden" name="branch_id" :value="selectedBranchId">
                                <div x-show="showBranchDD" x-transition x-cloak
                                     class="absolute z-50 w-full mt-1 rounded-lg shadow-lg"
                                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                    <div class="p-2 rounded-t-lg" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none" style="color: var(--dash-text-muted);">search</span>
                                            <input type="text" x-model="searchBranch" placeholder="Cari cabang..."
                                                   class="w-full pr-3 py-1.5 rounded-lg text-sm focus:outline-none transition-all"
                                                   style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                                                   @click.stop>
                                        </div>
                                    </div>
                                    <ul class="max-h-52 overflow-y-auto py-1">
                                        <li x-show="filteredBranches.length === 0" class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                        <template x-for="item in filteredBranches" :key="item.id">
                                            <li @click="selectBranch(item.id)"
                                                class="px-4 py-2 text-sm cursor-pointer"
                                                :style="selectedBranchId == item.id ? 'background-color: var(--dash-nav-hover-bg); font-weight: 600; color: var(--dash-text-heading)' : 'color: var(--dash-text-body)'"
                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                onmouseout="this.style.backgroundColor=''"
                                                x-text="item.name"></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div x-show="templateVars.some(v => v.key === 'kota_cabang')">
                            <label class="mb-1.5 block text-sm font-medium" style="color: var(--dash-text-body);">Teks di Dokumen <span class="text-xs font-normal" style="color: var(--dash-text-faint);">(bisa diubah)</span></label>
                            <input type="text" name="manual_vars[kota_cabang]" x-model="values['kota_cabang']"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>
                    </div>
                </div>

                {{-- Pegawai --}}
                <div x-show="showEmployee" x-cloak class="space-y-4">
                    <h2 class="font-jakarta text-base font-bold pb-2" style="color: var(--dash-text-heading); border-bottom: 1px solid var(--dash-card-border);">Pegawai yang Dituju</h2>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color: var(--dash-text-body);">Nama Pegawai</label>
                        <div class="relative" @click.away="showEmployeeDD = false">
                            <button type="button" @click="showEmployeeDD = !showEmployeeDD; showTemplateDD = false; showBranchDD = false; showSignerDD = false"
                                    class="w-full px-3 py-2 rounded-lg text-sm text-left flex items-center justify-between gap-2 focus:outline-none transition-all"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                <span x-text="selectedEmployeeName" :style="selectedEmployeeId ? '' : 'color: var(--dash-text-faint)'" class="truncate"></span>
                                <span class="material-symbols-outlined text-[18px] flex-shrink-0" style="color: var(--dash-text-faint);">expand_more</span>
                            </button>
                            <input type="hidden" name="employee_id" :value="selectedEmployeeId">
                            <div x-show="showEmployeeDD" x-transition x-cloak
                                 class="absolute z-50 w-full mt-1 rounded-lg shadow-lg"
                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                <div class="p-2 rounded-t-lg" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none" style="color: var(--dash-text-muted);">search</span>
                                        <input type="text" x-model="searchEmployee" placeholder="Cari pegawai..."
                                               class="w-full pr-3 py-1.5 rounded-lg text-sm focus:outline-none transition-all"
                                               style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                                               @click.stop>
                                    </div>
                                </div>
                                <ul class="max-h-52 overflow-y-auto py-1">
                                    <li x-show="filteredEmployees.length === 0" class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                    <template x-for="item in filteredEmployees" :key="item.id">
                                        <li @click="selectEmployee(item.id)"
                                            class="px-4 py-2 text-sm cursor-pointer"
                                            :style="selectedEmployeeId == item.id ? 'background-color: var(--dash-nav-hover-bg); font-weight: 600; color: var(--dash-text-heading)' : 'color: var(--dash-text-body)'"
                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                            onmouseout="this.style.backgroundColor=''"
                                            x-text="item.name"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div x-show="varsByType('employee_derived').length > 0" class="grid gap-4 sm:grid-cols-2">
                        <template x-for="v in varsByType('employee_derived')" :key="v.key">
                            <div>
                                <label class="mb-1 block text-sm font-medium" style="color: var(--dash-text-body);" x-text="v.label"></label>
                                <input type="text" :name="'manual_vars[' + v.key + ']'" x-model="values[v.key]"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Penandatangan --}}
                <div x-show="showSigner" x-cloak class="space-y-4">
                    <h2 class="font-jakarta text-base font-bold pb-2" style="color: var(--dash-text-heading); border-bottom: 1px solid var(--dash-card-border);">Penandatangan</h2>
                    <div>
                        <label class="mb-1.5 block text-sm font-medium" style="color: var(--dash-text-body);">Pilih Penandatangan</label>
                        <div class="relative" @click.away="showSignerDD = false">
                            <button type="button" @click="showSignerDD = !showSignerDD; showTemplateDD = false; showBranchDD = false; showEmployeeDD = false"
                                    class="w-full px-3 py-2 rounded-lg text-sm text-left flex items-center justify-between gap-2 focus:outline-none transition-all"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                <span x-text="selectedSignerName" :style="selectedSignerId ? '' : 'color: var(--dash-text-faint)'" class="truncate"></span>
                                <span class="material-symbols-outlined text-[18px] flex-shrink-0" style="color: var(--dash-text-faint);">expand_more</span>
                            </button>
                            <input type="hidden" name="signer_employee_id" :value="selectedSignerId">
                            <div x-show="showSignerDD" x-transition x-cloak
                                 class="absolute z-50 w-full mt-1 rounded-lg shadow-lg"
                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                <div class="p-2 rounded-t-lg" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none" style="color: var(--dash-text-muted);">search</span>
                                        <input type="text" x-model="searchSigner" placeholder="Cari penandatangan..."
                                               class="w-full pr-3 py-1.5 rounded-lg text-sm focus:outline-none transition-all"
                                               style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                                               @click.stop>
                                    </div>
                                </div>
                                <ul class="max-h-52 overflow-y-auto py-1">
                                    <li x-show="filteredSigners.length === 0" class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                    <template x-for="item in filteredSigners" :key="item.id">
                                        <li @click="selectSigner(item.id)"
                                            class="px-4 py-2 text-sm cursor-pointer"
                                            :style="selectedSignerId == item.id ? 'background-color: var(--dash-nav-hover-bg); font-weight: 600; color: var(--dash-text-heading)' : 'color: var(--dash-text-body)'"
                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                            onmouseout="this.style.backgroundColor=''"
                                            x-text="item.name"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div x-show="varsByType('signer_derived').length > 0" class="grid gap-4 sm:grid-cols-2">
                        <template x-for="v in varsByType('signer_derived')" :key="v.key">
                            <div>
                                <label class="mb-1 block text-sm font-medium" style="color: var(--dash-text-body);" x-text="v.label"></label>
                                <input type="text" :name="'manual_vars[' + v.key + ']'" x-model="values[v.key]"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Date + Text vars --}}
                <div x-show="dateVars.length > 0 || textVars.length > 0" x-cloak class="space-y-4">
                    <h2 class="font-jakarta text-base font-bold pb-2" style="color: var(--dash-text-heading); border-bottom: 1px solid var(--dash-card-border);">Isian Dokumen</h2>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <template x-for="v in dateVars" :key="v.key">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium" style="color: var(--dash-text-body);" x-text="v.label"></label>
                                <input type="date" :name="'manual_vars[' + v.key + ']'" x-model="values[v.key]"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            </div>
                        </template>
                        <template x-for="v in textVars" :key="v.key">
                            <div>
                                <label class="mb-1.5 block text-sm font-medium" style="color: var(--dash-text-body);" x-text="v.label"></label>
                                <input type="text" :name="'manual_vars[' + v.key + ']'" x-model="values[v.key]"
                                       class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Repeatable vars --}}
                <div x-show="repeatableVars.length > 0" x-cloak class="space-y-5">
                    <h2 class="font-jakarta text-base font-bold pb-2" style="color: var(--dash-text-heading); border-bottom: 1px solid var(--dash-card-border);">Daftar Isian (bisa ditambah)</h2>
                    <template x-for="v in repeatableVars" :key="v.key">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium" style="color: var(--dash-text-body);" x-text="v.label"></label>
                                <button type="button" @click="addRow(v.key)"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors"
                                        style="color: var(--dash-primary-text);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='transparent'">
                                    <span class="material-symbols-outlined text-[16px]">add</span> Tambah baris
                                </button>
                            </div>
                            <template x-for="(row, i) in getRows(v.key)" :key="i">
                                <div class="flex gap-2">
                                    <input type="text"
                                           :name="'manual_vars[' + v.key + '][' + i + ']'"
                                           :value="row"
                                           @input="updateRow(v.key, i, $event.target.value)"
                                           class="flex-1 px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                                    <button type="button" @click="removeRow(v.key, i)"
                                            x-show="getRows(v.key).length > 1"
                                            class="inline-flex items-center justify-center px-2 rounded-lg text-white transition-opacity hover:opacity-90"
                                            style="background-color: var(--dash-danger);">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- Metadata --}}
                <div class="space-y-4">
                    <h2 class="font-jakarta text-base font-bold pb-2" style="color: var(--dash-text-heading); border-bottom: 1px solid var(--dash-card-border);">Metadata Surat</h2>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color: var(--dash-text-body);">Penerima <span class="text-xs font-normal" style="color: var(--dash-text-faint);">(ditampilkan di daftar)</span></label>
                            <input type="text" name="recipient" value="{{ old('recipient') }}" maxlength="255"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium" style="color: var(--dash-text-body);">Catatan Internal <span class="text-xs font-normal" style="color: var(--dash-text-faint);">(tidak tercetak)</span></label>
                            <input type="text" name="notes" value="{{ old('notes') }}"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>
                    </div>
                </div>

            </div>{{-- end dynamic --}}

        </div>

        <div class="p-5 sm:px-7 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 rounded-b-2xl"
             style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
            <a href="{{ route('out-letters-generate.index') }}"
               class="w-full sm:w-auto inline-flex justify-center items-center gap-1.5 px-5 py-2 rounded-lg text-sm font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[18px]">close</span> Batal
            </a>
            <button type="submit" :disabled="isSubmitting || !selectedTemplateId"
                    class="w-full sm:w-auto inline-flex justify-center items-center gap-1.5 px-6 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background-color: var(--dash-primary);">
                <span x-show="!isSubmitting" class="material-symbols-outlined text-[18px]">draft</span>
                <span x-show="!isSubmitting">Buat Surat (Draft)</span>
                <span x-show="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]" x-cloak>progress_activity</span>
                <span x-show="isSubmitting" x-cloak>Memproses...</span>
            </button>
        </div>
    </form>
</div>
@endsection
