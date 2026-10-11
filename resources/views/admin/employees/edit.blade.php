@extends('layouts.dashboard')

@section('title', 'Edit Pegawai - MINDSIA')
@section('header_title', 'Edit Pegawai')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">
    {{-- Header Pola B3 --}}
    <div>
        <a href="{{ route('employees.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Data Pegawai
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">SDM & Karyawan</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Data Pegawai</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Perbarui data diri, penempatan kerja, dan status aktif {{ $employee->full_name }}.
        </p>
    </div>

    {{-- Error Flash / Validation --}}
    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <div class="flex items-center gap-2 font-semibold mb-1">
            <span class="material-symbols-outlined text-[18px]">error</span>
            Ada kesalahan:
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Informasi Dasar --}}
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="font-semibold text-sm font-jakarta" style="color: var(--dash-text-heading);">Informasi Dasar</h2>
            </div>
            <div class="grid gap-6 p-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name', $employee->full_name) }}"
                           required maxlength="255"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select name="gender" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih...</option>
                        <option value="L" @selected(old('gender', $employee->gender) === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('gender', $employee->gender) === 'P')>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Tanggal Lahir</label>
                    <input type="date" name="birthdate"
                           value="{{ old('birthdate', $employee->birthdate?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Email</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">No. WhatsApp</label>
                    <div class="flex rounded-lg overflow-hidden transition-all" style="border: 1px solid var(--dash-input-border);">
                        <span class="inline-flex items-center px-3.5 text-sm font-bold border-r"
                              style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-input-border); color: var(--dash-text-muted);">+62</span>
                        <input type="text" name="whatsapp_number"
                               value="{{ old('whatsapp_number', $employee->whatsapp_number) }}"
                               placeholder="8xxxxxxxxxx" minlength="8" maxlength="15"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                               class="w-full px-4 py-2.5 text-sm focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); color: var(--dash-text-heading);"
                               onfocus="this.parentElement.style.borderColor='var(--dash-primary)'; this.parentElement.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.parentElement.style.borderColor='var(--dash-input-border)'; this.parentElement.style.boxShadow='none'" />
                    </div>
                </div>
            </div>
        </div>

        @php
            $branchMap = $branches->mapWithKeys(fn($b) => [
                $b->id => ['area_id' => $b->areas_id, 'region_id' => $b->area?->region_id]
            ]);
            $areaMap = $areas->mapWithKeys(fn($a) => [$a->id => $a->region_id]);
        @endphp

        {{-- Penempatan --}}
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             x-data="{
                 placement: '{{ old('is_hq', $employee->is_hq) ? 'hq' : (old('branch_id', $employee->branch_id) ? 'branch' : (old('area_id', $employee->area_id) ? 'area' : (old('region_id', $employee->region_id) ? 'region' : 'branch'))) }}',
                 branchMap: @js($branchMap),
                 areaMap: @js($areaMap),
                 selectedBranch: '{{ old('branch_id', $employee->branch_id) }}',
                 selectedArea: '{{ old('area_id', $employee->area_id) }}',
                 get derivedRegionId() {
                     if (this.placement === 'branch' && this.selectedBranch) return this.branchMap[this.selectedBranch]?.region_id ?? '';
                     if (this.placement === 'area' && this.selectedArea) return this.areaMap[this.selectedArea] ?? '';
                     return '';
                 },
                 get derivedAreaId() {
                     if (this.placement === 'branch' && this.selectedBranch) return this.branchMap[this.selectedBranch]?.area_id ?? '';
                     return '';
                 }
             }">
            <div class="px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="font-semibold text-sm font-jakarta" style="color: var(--dash-text-heading);">Penempatan</h2>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Tingkat Penempatan</label>
                    <select x-model="placement"
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="hq">Headquarters (Pusat)</option>
                        <option value="branch">Cabang</option>
                        <option value="area">Area</option>
                        <option value="region">Region</option>
                    </select>
                </div>

                {{-- HQ hidden inputs --}}
                <template x-if="placement === 'hq'">
                    <div>
                        <input type="hidden" name="is_hq" value="1">
                        <input type="hidden" name="branch_id" value="">
                        <input type="hidden" name="area_id" value="">
                        <input type="hidden" name="region_id" value="">
                        <p class="text-sm" style="color: var(--dash-text-muted);">Pegawai ditempatkan di kantor pusat.</p>
                    </div>
                </template>

                {{-- Branch --}}
                <template x-if="placement === 'branch'">
                    <div>
                        <input type="hidden" name="is_hq" value="0">
                        <input type="hidden" name="area_id" :value="derivedAreaId">
                        <input type="hidden" name="region_id" :value="derivedRegionId">
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Cabang <span class="text-red-500">*</span>
                        </label>
                        <select name="branch_id" x-model="selectedBranch"
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih cabang...</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </template>

                {{-- Area --}}
                <template x-if="placement === 'area'">
                    <div>
                        <input type="hidden" name="is_hq" value="0">
                        <input type="hidden" name="branch_id" value="">
                        <input type="hidden" name="region_id" :value="derivedRegionId">
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Area <span class="text-red-500">*</span>
                        </label>
                        <select name="area_id" x-model="selectedArea"
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih area...</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </template>

                {{-- Region --}}
                <template x-if="placement === 'region'">
                    <div>
                        <input type="hidden" name="is_hq" value="0">
                        <input type="hidden" name="branch_id" value="">
                        <input type="hidden" name="area_id" value="">
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Region <span class="text-red-500">*</span>
                        </label>
                        <select name="region_id"
                                class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="">Pilih region...</option>
                            @foreach ($regions as $region)
                                <option value="{{ $region->id }}"
                                        @selected(old('region_id', $employee->region_id) == $region->id)>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </template>
            </div>
        </div>

        {{-- Status Pegawai --}}
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="font-semibold text-sm font-jakarta" style="color: var(--dash-text-heading);">Status Pegawai</h2>
            </div>
            <div class="p-6">
                <label class="inline-flex cursor-pointer items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $employee->is_active))
                           class="h-4 w-4 rounded cursor-pointer"
                           style="accent-color: var(--dash-primary);">
                    <span class="text-sm font-medium" style="color: var(--dash-text-body);">Pegawai aktif</span>
                </label>
                <p class="mt-1 text-xs" style="color: var(--dash-text-muted);">Nonaktifkan untuk menyembunyikan pegawai dari daftar dan proses payroll.</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('employees.show', $employee->id) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
