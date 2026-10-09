@extends('layouts.dashboard')

@section('title', 'Tambah Pegawai')

@section('content')
<script>
window._empFormData = {
    allRegions:  @json($regions->map(fn($r) => ['id' => $r->id, 'name' => $r->name])),
    allAreas:    @json($areas->map(fn($a) => ['id' => $a->id, 'name' => $a->name, 'region_id' => $a->region_id])),
    allBranches: @json($branches->map(fn($b) => ['id' => $b->id, 'name' => $b->branch_name, 'area_id' => $b->areas_id])),
};
</script>
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-[color:var(--dash-text-heading)]">Tambah Pegawai Baru</h1>
        <a href="{{ route('employees.index') }}" class="bg-[var(--dash-nav-hover-bg)] hover:bg-gray-200 text-[color:var(--dash-text-body)] px-4 py-2 rounded-lg font-medium border border-[color:var(--dash-input-border)]">
            Batal
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <strong>Ada kesalahan pada input Anda:</strong>
            <ul class="list-disc pl-5 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-[var(--dash-card-bg)] shadow sm:rounded-lg">
        <form action="{{ route('employees.store') }}" method="POST" class="p-6" novalidate>
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="employeeForm()">
                <!-- Kode Pegawai -->
                <div>
                    <label class="block text-sm font-medium text-[color:var(--dash-text-body)]">Kode Pegawai <span class="text-red-500">*</span></label>
                    <input type="text" name="employee_code" value="{{ old('employee_code') }}" required class="mt-1 block w-full border border-[color:var(--dash-input-border)] rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-sm font-medium text-[color:var(--dash-text-body)]">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required class="mt-1 block w-full border border-[color:var(--dash-input-border)] rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-sm font-medium text-[color:var(--dash-text-body)]">Jenis Kelamin</label>
                    <select name="gender" class="mt-1 block w-full bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih...</option>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-sm font-medium text-[color:var(--dash-text-body)]">Tanggal Lahir</label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" class="mt-1 block w-full border border-[color:var(--dash-input-border)] rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- Tanggal Mulai Kerja -->
                <div>
                    <label class="block text-sm font-medium text-[color:var(--dash-text-body)]">Tanggal Mulai Kerja <span class="text-red-500">*</span></label>
                    <input type="date" name="join_date" value="{{ old('join_date') }}" class="mt-1 block w-full border border-[color:var(--dash-input-border)] rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <p class="mt-1 text-xs text-[color:var(--dash-text-muted)]">Karyawan baru: isi sesuai hari pertama bekerja, dapat berupa tanggal yang akan datang. Karyawan lama: isi tanggal pertama kali bergabung untuk keperluan kalkulasi bonus masa kerja.</p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-[color:var(--dash-text-body)]">Email Utama <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border border-[color:var(--dash-input-border)] rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- WhatsApp -->
                <div>
                    <label class="block text-sm font-medium text-[color:var(--dash-text-body)]">Nomor WhatsApp <span class="text-red-500">*</span></label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-[color:var(--dash-input-border)] bg-[var(--dash-nav-hover-bg)] px-3 text-sm font-bold text-[color:var(--dash-text-muted)]">+62</span>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" placeholder="8xxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')" class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-[color:var(--dash-input-border)] py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"  minlength="8" maxlength="15">
                    </div>
                </div>

                <!-- Region -->
                <div x-show="!isHq">
                    <label class="block text-sm font-medium text-[color:var(--dash-text-body)]">Region</label>
                    <input type="hidden" name="region_id" :value="regionId">
                    <div class="relative mt-1" @click.away="regionOpen = false">
                        <div @click="regionOpen = !regionOpen" class="w-full px-3.5 py-2.5 bg-[var(--dash-card-bg)] border rounded-xl text-[13px] shadow-sm flex justify-between items-center cursor-pointer transition-all min-h-[44px]" :class="regionOpen ? 'border-[#5586DB] ring-2 ring-[#5586DB]/20' : 'border-[#E5E7EB]'">
                            <span class="truncate block pr-4" :class="regionId ? 'text-[#111827]' : 'text-[#9CA3AF]'" x-text="regionLabel || '— Tidak ada —'"></span>
                            <span class="material-symbols-outlined text-[16px] text-[color:var(--dash-text-faint)] transition-transform duration-200" :class="regionOpen ? 'rotate-180' : ''">expand_more</span>
                        </div>
                        <div x-show="regionOpen" x-cloak class="absolute z-50 w-full mt-2 bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl shadow-xl overflow-hidden">
                            <div class="p-2 bg-[var(--dash-nav-hover-bg)] border-b border-[color:var(--dash-card-border)]">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[color:var(--dash-text-faint)] text-[16px]">search</span>
                                    <input type="text" x-model="regionSearch" placeholder="Cari region..." @click.stop style="padding-left: 2.5rem !important" class="w-full pr-3 py-2 bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-lg text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB] transition-all">
                                </div>
                            </div>
                            <ul class="max-h-48 overflow-y-auto py-1">
                                <li @click="regionId = ''; regionOpen = false; regionSearch = ''; areaId = ''; branchId = '';" class="px-4 py-2 text-[12px] text-[color:var(--dash-text-body)] hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors" :class="!regionId ? 'bg-[#EFF6FF] font-semibold' : ''">— Tidak ada —</li>
                                <li x-show="filteredRegions.length === 0" class="px-4 py-3 text-[12px] text-[color:var(--dash-text-muted)] text-center">Tidak ditemukan</li>
                                <template x-for="r in filteredRegions" :key="r.id">
                                    <li @click="pickRegion(r)" class="px-4 py-2 text-[12px] text-[color:var(--dash-text-body)] hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors" :class="regionId == r.id ? 'bg-[#EFF6FF] font-semibold' : ''" x-text="r.name"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Area -->
                <div x-show="!isHq">
                    <label class="block text-sm font-medium text-[color:var(--dash-text-body)]">Area</label>
                    <input type="hidden" name="area_id" :value="areaId">
                    <div class="relative mt-1" @click.away="areaOpen = false">
                        <div @click="areaOpen = !areaOpen" class="w-full px-3.5 py-2.5 bg-[var(--dash-card-bg)] border rounded-xl text-[13px] shadow-sm flex justify-between items-center cursor-pointer transition-all min-h-[44px]" :class="areaOpen ? 'border-[#5586DB] ring-2 ring-[#5586DB]/20' : 'border-[#E5E7EB]'">
                            <span class="truncate block pr-4" :class="areaId ? 'text-[#111827]' : 'text-[#9CA3AF]'" x-text="areaLabel || '— Tidak ada —'"></span>
                            <span class="material-symbols-outlined text-[16px] text-[color:var(--dash-text-faint)] transition-transform duration-200" :class="areaOpen ? 'rotate-180' : ''">expand_more</span>
                        </div>
                        <div x-show="areaOpen" x-cloak class="absolute z-50 w-full mt-2 bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl shadow-xl overflow-hidden">
                            <div class="p-2 bg-[var(--dash-nav-hover-bg)] border-b border-[color:var(--dash-card-border)]">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[color:var(--dash-text-faint)] text-[16px]">search</span>
                                    <input type="text" x-model="areaSearch" placeholder="Cari area..." @click.stop style="padding-left: 2.5rem !important" class="w-full pr-3 py-2 bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-lg text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB] transition-all">
                                </div>
                            </div>
                            <ul class="max-h-48 overflow-y-auto py-1">
                                <li @click="areaId = ''; areaOpen = false; areaSearch = ''; branchId = '';" class="px-4 py-2 text-[12px] text-[color:var(--dash-text-body)] hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors" :class="!areaId ? 'bg-[#EFF6FF] font-semibold' : ''">— Tidak ada —</li>
                                <li x-show="filteredAreas.length === 0" class="px-4 py-3 text-[12px] text-[color:var(--dash-text-muted)] text-center">Tidak ditemukan</li>
                                <template x-for="a in filteredAreas" :key="a.id">
                                    <li @click="pickArea(a)" class="px-4 py-2 text-[12px] text-[color:var(--dash-text-body)] hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors" :class="areaId == a.id ? 'bg-[#EFF6FF] font-semibold' : ''" x-text="a.name"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Penempatan Branch -->
                <div x-show="!isHq">
                    <label class="block text-sm font-medium text-[color:var(--dash-text-body)]">Penempatan Cabang</label>
                    <input type="hidden" name="branch_id" :value="branchId">
                    <div class="relative mt-1" @click.away="branchOpen = false">
                        <div @click="branchOpen = !branchOpen" class="w-full px-3.5 py-2.5 bg-[var(--dash-card-bg)] border rounded-xl text-[13px] shadow-sm flex justify-between items-center cursor-pointer transition-all min-h-[44px]" :class="branchOpen ? 'border-[#5586DB] ring-2 ring-[#5586DB]/20' : 'border-[#E5E7EB]'">
                            <span class="truncate block pr-4" :class="branchId ? 'text-[#111827]' : 'text-[#9CA3AF]'" x-text="branchLabel || '— Tidak ditempatkan di Cabang —'"></span>
                            <span class="material-symbols-outlined text-[16px] text-[color:var(--dash-text-faint)] transition-transform duration-200" :class="branchOpen ? 'rotate-180' : ''">expand_more</span>
                        </div>
                        <div x-show="branchOpen" x-cloak class="absolute z-50 w-full mt-2 bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl shadow-xl overflow-hidden">
                            <div class="p-2 bg-[var(--dash-nav-hover-bg)] border-b border-[color:var(--dash-card-border)]">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[color:var(--dash-text-faint)] text-[16px]">search</span>
                                    <input type="text" x-model="branchSearch" placeholder="Cari cabang..." @click.stop style="padding-left: 2.5rem !important" class="w-full pr-3 py-2 bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-lg text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB] transition-all">
                                </div>
                            </div>
                            <ul class="max-h-48 overflow-y-auto py-1">
                                <li @click="branchId = ''; branchOpen = false; branchSearch = '';" class="px-4 py-2 text-[12px] text-[color:var(--dash-text-body)] hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors" :class="!branchId ? 'bg-[#EFF6FF] font-semibold' : ''">— Tidak ditempatkan di Cabang —</li>
                                <li x-show="filteredBranches.length === 0" class="px-4 py-3 text-[12px] text-[color:var(--dash-text-muted)] text-center">Tidak ditemukan</li>
                                <template x-for="b in filteredBranches" :key="b.id">
                                    <li @click="pickBranch(b)" class="px-4 py-2 text-[12px] text-[color:var(--dash-text-body)] hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors" :class="branchId == b.id ? 'bg-[#EFF6FF] font-semibold' : ''" x-text="b.name"></li>
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
                           class="h-4 w-4 rounded border-[color:var(--dash-input-border)] text-blue-600 focus:ring-blue-500">
                    <label for="is_hq" class="text-sm font-medium text-[color:var(--dash-text-body)]">Pegawai Kantor Pusat (HQ)</label>
                </div>

                <!-- Status Aktif -->
                <div>
                    <label class="block text-sm font-medium text-[color:var(--dash-text-body)]">Status Pegawai <span class="text-red-500">*</span></label>
                    <select name="is_active" required class="mt-1 block w-full bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
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
