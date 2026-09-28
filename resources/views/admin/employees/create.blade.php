@extends('layouts.dashboard')

@section('title', 'Tambah Pegawai')

@section('content')
<script>
window._empFormData = {
    allAreas:    @json($areas->map(fn($a) => ['id' => $a->id, 'name' => $a->name, 'region_id' => $a->region_id])),
    allBranches: @json($branches->map(fn($b) => ['id' => $b->id, 'name' => $b->branch_name, 'area_id' => $b->areas_id])),
};
</script>
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Pegawai Baru</h1>
        <a href="{{ route('employees.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium border border-gray-300">
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

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('employees.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="employeeForm()">
                <!-- Kode Pegawai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kode Pegawai <span class="text-red-500">*</span></label>
                    <input type="text" name="employee_code" value="{{ old('employee_code') }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                    <select name="gender" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih...</option>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email Utama</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- WhatsApp -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm font-bold text-gray-500">+62</span>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" placeholder="8xxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')" class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-gray-300 py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"  minlength="8" maxlength="15">
                    </div>
                </div>

                <!-- Region -->
                <div x-show="!isHq">
                    <label class="block text-sm font-medium text-gray-700">Region</label>
                    <select name="region_id" x-model="regionId" @change="onRegionChange()"
                            data-no-enhance
                            class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">— Tidak ada —</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Area -->
                <div x-show="!isHq">
                    <label class="block text-sm font-medium text-gray-700">Area</label>
                    <select name="area_id" x-model="areaId" @change="onAreaChange()"
                            data-no-enhance
                            class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">— Tidak ada —</option>
                        <template x-for="area in filteredAreas" :key="area.id">
                            <option :value="area.id" x-text="area.name"></option>
                        </template>
                    </select>
                </div>

                <!-- Penempatan Branch -->
                <div x-show="!isHq">
                    <label class="block text-sm font-medium text-gray-700">Penempatan Cabang</label>
                    <select name="branch_id" x-model="branchId" @change="onBranchChange()"
                            data-no-enhance
                            class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">— Tidak ditempatkan di Cabang —</option>
                        <template x-for="branch in filteredBranches" :key="branch.id">
                            <option :value="branch.id" x-text="branch.name"></option>
                        </template>
                    </select>
                </div>

                <!-- HQ -->
                <div class="flex items-center gap-3 pt-5">
                    <input type="hidden" name="is_hq" value="0">
                    <input type="checkbox" name="is_hq" id="is_hq" value="1" {{ old('is_hq') ? 'checked' : '' }}
                           @change="isHq = $event.target.checked"
                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="is_hq" class="text-sm font-medium text-gray-700">Pegawai Kantor Pusat (HQ)</label>
                </div>

                <!-- Status Aktif -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status Pegawai <span class="text-red-500">*</span></label>
                    <select name="is_active" required class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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
        allAreas:    window._empFormData.allAreas,
        allBranches: window._empFormData.allBranches,
        regionId:    '{{ old('region_id', '') }}',
        areaId:      '{{ old('area_id', '') }}',
        branchId:    '{{ old('branch_id', '') }}',

        get filteredAreas() {
            if (!this.regionId) return this.allAreas;
            return this.allAreas.filter(a => a.region_id == this.regionId);
        },
        get filteredBranches() {
            if (!this.areaId) return this.allBranches;
            return this.allBranches.filter(b => b.area_id == this.areaId);
        },
        onRegionChange() {
            this.areaId   = '';
            this.branchId = '';
        },
        onAreaChange() {
            const area = this.allAreas.find(a => a.id == this.areaId);
            if (area) this.regionId = area.region_id;
            this.branchId = '';
        },
        onBranchChange() {
            const branch = this.allBranches.find(b => b.id == this.branchId);
            if (branch) {
                this.areaId = branch.area_id;
                const area = this.allAreas.find(a => a.id == branch.area_id);
                if (area) this.regionId = area.region_id;
            }
        },
    }));
});
</script>
@endpush
