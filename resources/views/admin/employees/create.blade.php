@extends('layouts.dashboard')

@section('title', 'Tambah Pegawai - MINDSIA')
@section('header_title', 'Tambah Pegawai')

@section('content')
<script>
window._empFormData = {
    allRegions:  @json($regions->map(fn($r) => ['id' => $r->id, 'name' => $r->name])),
    allAreas:    @json($areas->map(fn($a) => ['id' => $a->id, 'name' => $a->name, 'region_id' => $a->region_id])),
    allBranches: @json($branches->map(fn($b) => ['id' => $b->id, 'name' => $b->branch_name, 'area_id' => $b->areas_id])),
};
</script>

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">
    {{-- Header Pola B3 --}}
    <div>
        <a href="{{ route('employees.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Data Pegawai
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">SDM & Karyawan</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Tambah Pegawai Baru</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Lengkapi data diri, penempatan kerja, dan status pegawai baru.
        </p>
    </div>

    {{-- Error Flash / Validation --}}
    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <div class="flex items-center gap-2 font-semibold mb-1">
            <span class="material-symbols-outlined text-[18px]">error</span>
            Ada kesalahan pada input Anda:
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm font-jakarta" style="color: var(--dash-text-heading);">Formulir Data Pegawai</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Masukkan informasi pegawai secara lengkap dan valid.</p>
        </div>

        <form action="{{ route('employees.store') }}" method="POST" class="p-6" novalidate>
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="employeeForm()">
                <!-- Kode Pegawai -->
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Kode Pegawai <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="employee_code" value="{{ old('employee_code') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all font-mono"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Jenis Kelamin</label>
                    <select name="gender"
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih...</option>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Tanggal Lahir</label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>

                <!-- Tanggal Mulai Kerja -->
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Tanggal Mulai Kerja <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="join_date" value="{{ old('join_date') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    <p class="mt-1 text-xs" style="color: var(--dash-text-muted);">
                        Karyawan baru: isi sesuai hari pertama bekerja, dapat berupa tanggal yang akan datang. Karyawan lama: isi tanggal pertama kali bergabung untuk keperluan kalkulasi bonus masa kerja.
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Email Utama <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>

                <!-- WhatsApp -->
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Nomor WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <div class="flex rounded-lg overflow-hidden transition-all" style="border: 1px solid var(--dash-input-border);">
                        <span class="inline-flex items-center px-3.5 text-sm font-bold border-r"
                              style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-input-border); color: var(--dash-text-muted);">+62</span>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" placeholder="8xxxxxxxxxx"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                               class="w-full px-4 py-2.5 text-sm focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); color: var(--dash-text-heading);"
                               minlength="8" maxlength="15"
                               onfocus="this.parentElement.style.borderColor='var(--dash-primary)'; this.parentElement.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.parentElement.style.borderColor='var(--dash-input-border)'; this.parentElement.style.boxShadow='none'" />
                    </div>
                </div>

                <!-- Region -->
                <div x-show="!isHq">
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Region</label>
                    <input type="hidden" name="region_id" :value="regionId">
                    <div class="relative mt-1" @click.away="regionOpen = false">
                        <div @click="regionOpen = !regionOpen"
                             class="w-full px-4 py-2.5 rounded-lg text-sm shadow-sm flex justify-between items-center cursor-pointer transition-all min-h-[44px]"
                             :style="regionOpen ? 'border: 1px solid var(--dash-primary); box-shadow: 0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent); background-color: var(--dash-input-bg);' : 'border: 1px solid var(--dash-input-border); background-color: var(--dash-input-bg);'">
                            <span class="truncate block pr-4"
                                  :style="regionId ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-faint);'"
                                  x-text="regionLabel || 'Tidak ada'"></span>
                            <span class="material-symbols-outlined text-[18px] transition-transform duration-200"
                                  style="color: var(--dash-text-muted);"
                                  :class="regionOpen ? 'rotate-180' : ''">expand_more</span>
                        </div>
                        <div x-show="regionOpen" x-cloak
                             class="absolute z-50 w-full mt-2 rounded-xl shadow-xl overflow-hidden"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                            <div class="p-2" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                                          style="color: var(--dash-text-muted);">search</span>
                                    <input type="text" x-model="regionSearch" placeholder="Cari region..." @click.stop
                                           class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                                </div>
                            </div>
                            <ul class="max-h-48 overflow-y-auto py-1 text-sm">
                                <li @click="regionId = ''; regionOpen = false; regionSearch = ''; areaId = ''; branchId = '';"
                                    class="px-4 py-2 cursor-pointer transition-colors"
                                    :style="!regionId ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="if(!regionId) this.style.backgroundColor='var(--dash-primary-bg)'; else this.style.backgroundColor='transparent'">
                                    Tidak ada
                                </li>
                                <li x-show="filteredRegions.length === 0" class="px-4 py-3 text-center text-xs" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                <template x-for="r in filteredRegions" :key="r.id">
                                    <li @click="pickRegion(r)"
                                        class="px-4 py-2 cursor-pointer transition-colors"
                                        :style="regionId == r.id ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="if(regionId == r.id) this.style.backgroundColor='var(--dash-primary-bg)'; else this.style.backgroundColor='transparent'"
                                        x-text="r.name"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Area -->
                <div x-show="!isHq">
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Area</label>
                    <input type="hidden" name="area_id" :value="areaId">
                    <div class="relative mt-1" @click.away="areaOpen = false">
                        <div @click="areaOpen = !areaOpen"
                             class="w-full px-4 py-2.5 rounded-lg text-sm shadow-sm flex justify-between items-center cursor-pointer transition-all min-h-[44px]"
                             :style="areaOpen ? 'border: 1px solid var(--dash-primary); box-shadow: 0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent); background-color: var(--dash-input-bg);' : 'border: 1px solid var(--dash-input-border); background-color: var(--dash-input-bg);'">
                            <span class="truncate block pr-4"
                                  :style="areaId ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-faint);'"
                                  x-text="areaLabel || 'Tidak ada'"></span>
                            <span class="material-symbols-outlined text-[18px] transition-transform duration-200"
                                  style="color: var(--dash-text-muted);"
                                  :class="areaOpen ? 'rotate-180' : ''">expand_more</span>
                        </div>
                        <div x-show="areaOpen" x-cloak
                             class="absolute z-50 w-full mt-2 rounded-xl shadow-xl overflow-hidden"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                            <div class="p-2" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                                          style="color: var(--dash-text-muted);">search</span>
                                    <input type="text" x-model="areaSearch" placeholder="Cari area..." @click.stop
                                           class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                                </div>
                            </div>
                            <ul class="max-h-48 overflow-y-auto py-1 text-sm">
                                <li @click="areaId = ''; areaOpen = false; areaSearch = ''; branchId = '';"
                                    class="px-4 py-2 cursor-pointer transition-colors"
                                    :style="!areaId ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="if(!areaId) this.style.backgroundColor='var(--dash-primary-bg)'; else this.style.backgroundColor='transparent'">
                                    Tidak ada
                                </li>
                                <li x-show="filteredAreas.length === 0" class="px-4 py-3 text-center text-xs" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                <template x-for="a in filteredAreas" :key="a.id">
                                    <li @click="pickArea(a)"
                                        class="px-4 py-2 cursor-pointer transition-colors"
                                        :style="areaId == a.id ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="if(areaId == a.id) this.style.backgroundColor='var(--dash-primary-bg)'; else this.style.backgroundColor='transparent'"
                                        x-text="a.name"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Penempatan Branch -->
                <div x-show="!isHq">
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Penempatan Cabang</label>
                    <input type="hidden" name="branch_id" :value="branchId">
                    <div class="relative mt-1" @click.away="branchOpen = false">
                        <div @click="branchOpen = !branchOpen"
                             class="w-full px-4 py-2.5 rounded-lg text-sm shadow-sm flex justify-between items-center cursor-pointer transition-all min-h-[44px]"
                             :style="branchOpen ? 'border: 1px solid var(--dash-primary); box-shadow: 0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent); background-color: var(--dash-input-bg);' : 'border: 1px solid var(--dash-input-border); background-color: var(--dash-input-bg);'">
                            <span class="truncate block pr-4"
                                  :style="branchId ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-faint);'"
                                  x-text="branchLabel || 'Tidak ditempatkan di Cabang'"></span>
                            <span class="material-symbols-outlined text-[18px] transition-transform duration-200"
                                  style="color: var(--dash-text-muted);"
                                  :class="branchOpen ? 'rotate-180' : ''">expand_more</span>
                        </div>
                        <div x-show="branchOpen" x-cloak
                             class="absolute z-50 w-full mt-2 rounded-xl shadow-xl overflow-hidden"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                            <div class="p-2" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                                          style="color: var(--dash-text-muted);">search</span>
                                    <input type="text" x-model="branchSearch" placeholder="Cari cabang..." @click.stop
                                           class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                           style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                                </div>
                            </div>
                            <ul class="max-h-48 overflow-y-auto py-1 text-sm">
                                <li @click="branchId = ''; branchOpen = false; branchSearch = '';"
                                    class="px-4 py-2 cursor-pointer transition-colors"
                                    :style="!branchId ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="if(!branchId) this.style.backgroundColor='var(--dash-primary-bg)'; else this.style.backgroundColor='transparent'">
                                    Tidak ditempatkan di Cabang
                                </li>
                                <li x-show="filteredBranches.length === 0" class="px-4 py-3 text-center text-xs" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                <template x-for="b in filteredBranches" :key="b.id">
                                    <li @click="pickBranch(b)"
                                        class="px-4 py-2 cursor-pointer transition-colors"
                                        :style="branchId == b.id ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="if(branchId == b.id) this.style.backgroundColor='var(--dash-primary-bg)'; else this.style.backgroundColor='transparent'"
                                        x-text="b.name"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- HQ -->
                <div class="flex items-center gap-3 pt-5">
                    <input type="hidden" name="is_hq" value="0">
                    <input type="checkbox" name="is_hq" id="is_hq" value="1" {{ old('is_hq') ? 'checked' : '' }}
                           @change="isHq = $event.target.checked"
                           class="h-4 w-4 rounded cursor-pointer"
                           style="accent-color: var(--dash-primary);">
                    <label for="is_hq" class="text-sm font-medium cursor-pointer" style="color: var(--dash-text-body);">Pegawai Kantor Pusat (HQ)</label>
                </div>

                <!-- Status Aktif -->
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Status Pegawai <span class="text-red-500">*</span>
                    </label>
                    <select name="is_active" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-6" style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('employees.index') }}"
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
                    Simpan Data Pegawai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('employeeForm', () => ({
        isHq:        {{ old('is_hq') ? 'true' : 'false' }},
        allRegions:  window._empFormData.allRegions,
        allAreas:    window._empFormData.allAreas,
        allBranches: window._empFormData.allBranches,
        regionId:    '{{ old('region_id', '') }}',
        areaId:      '{{ old('area_id', '') }}',
        branchId:    '{{ old('branch_id', '') }}',

        regionOpen: false, regionSearch: '',
        areaOpen: false, areaSearch: '',
        branchOpen: false, branchSearch: '',

        get regionLabel() {
            const r = this.allRegions.find(r => r.id == this.regionId);
            return r ? r.name : '';
        },
        get areaLabel() {
            const a = this.allAreas.find(a => a.id == this.areaId);
            return a ? a.name : '';
        },
        get branchLabel() {
            const b = this.allBranches.find(b => b.id == this.branchId);
            return b ? b.name : '';
        },
        get filteredRegions() {
            if (!this.regionSearch) return this.allRegions;
            const s = this.regionSearch.toLowerCase();
            return this.allRegions.filter(r => r.name.toLowerCase().includes(s));
        },
        get filteredAreas() {
            const areas = this.regionId ? this.allAreas.filter(a => a.region_id == this.regionId) : this.allAreas;
            if (!this.areaSearch) return areas;
            const s = this.areaSearch.toLowerCase();
            return areas.filter(a => a.name.toLowerCase().includes(s));
        },
        get filteredBranches() {
            const branches = this.areaId ? this.allBranches.filter(b => b.area_id == this.areaId) : this.allBranches;
            if (!this.branchSearch) return branches;
            const s = this.branchSearch.toLowerCase();
            return branches.filter(b => b.name.toLowerCase().includes(s));
        },
        pickRegion(r) {
            this.regionId = r.id;
            this.regionOpen = false;
            this.regionSearch = '';
            this.areaId = '';
            this.branchId = '';
        },
        pickArea(a) {
            this.areaId = a.id;
            this.regionId = a.region_id;
            this.areaOpen = false;
            this.areaSearch = '';
            this.branchId = '';
        },
        pickBranch(b) {
            this.branchId = b.id;
            this.areaId = b.area_id;
            const area = this.allAreas.find(a => a.id == b.area_id);
            if (area) this.regionId = area.region_id;
            this.branchOpen = false;
            this.branchSearch = '';
        },
    }));
});
</script>
@endpush
