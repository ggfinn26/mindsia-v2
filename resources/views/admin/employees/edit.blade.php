@extends('layouts.dashboard')

@section('title', 'Edit Pegawai')
@section('header_title', 'Edit Pegawai')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('employees.show', $employee->id) }}"
               class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                {{ $employee->full_name }}
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Data Pegawai</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Ada kesalahan:</p>
            <ul class="mt-1 list-disc pl-5">
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
        <div class="overflow-hidden border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
            <div class="border-b border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] px-5 py-4">
                <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Informasi Dasar</h3>
            </div>
            <div class="grid gap-5 px-5 py-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name', $employee->full_name) }}"
                           required maxlength="255"
                           class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Jenis Kelamin <span class="text-rose-500">*</span></label>
                    <select name="gender" required
                            class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                        <option value="">Pilih...</option>
                        <option value="L" @selected(old('gender', $employee->gender) === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('gender', $employee->gender) === 'P')>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Tanggal Lahir</label>
                    <input type="date" name="birthdate"
                           value="{{ old('birthdate', $employee->birthdate?->format('Y-m-d')) }}"
                           class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Email</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                           class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">No. WhatsApp</label>
                    <div class="flex">
                        <span class="inline-flex items-center border border-r-0 border-[color:var(--dash-input-border)] bg-[var(--dash-nav-hover-bg)] px-3 text-sm font-bold text-[color:var(--dash-text-muted)]">+62</span>
                        <input type="text" name="whatsapp_number"
                               value="{{ old('whatsapp_number', $employee->whatsapp_number) }}"
                               placeholder="8xxxxxxxxxx" minlength="8" maxlength="15"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                               class="block w-full min-w-0 flex-1 border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
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
        <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]"
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
            <div class="border-b border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] px-5 py-4">
                <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Penempatan</h3>
            </div>
            <div class="px-5 py-5 space-y-5">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Tingkat Penempatan</label>
                    <select x-model="placement"
                            class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
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
                        <p class="text-sm text-[color:var(--dash-text-muted)]">Pegawai ditempatkan di kantor pusat.</p>
                    </div>
                </template>

                {{-- Branch --}}
                <template x-if="placement === 'branch'">
                    <div>
                        <input type="hidden" name="is_hq" value="0">
                        <input type="hidden" name="area_id" :value="derivedAreaId">
                        <input type="hidden" name="region_id" :value="derivedRegionId">
                        <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Cabang <span class="text-rose-500">*</span></label>
                        <select name="branch_id" x-model="selectedBranch"
                                class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
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
                        <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Area <span class="text-rose-500">*</span></label>
                        <select name="area_id" x-model="selectedArea"
                                class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
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
                        <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Region <span class="text-rose-500">*</span></label>
                        <select name="region_id"
                                class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
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

        {{-- Status --}}
        <div class="overflow-hidden border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
            <div class="border-b border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] px-5 py-4">
                <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Status Pegawai</h3>
            </div>
            <div class="px-5 py-5">
                <label class="inline-flex cursor-pointer items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $employee->is_active))
                           class="h-4 w-4 border-[color:var(--dash-input-border)] text-[color:var(--dash-primary-text,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                    <span class="text-sm font-medium text-[color:var(--dash-text-body)]">Pegawai aktif</span>
                </label>
                <p class="mt-1 text-xs text-[color:var(--dash-text-muted)]">Nonaktifkan untuk menyembunyikan pegawai dari daftar dan proses payroll.</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('employees.show', $employee->id) }}"
               class="inline-flex min-h-10 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection
