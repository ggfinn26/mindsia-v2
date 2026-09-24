@extends('layouts.dashboard')

@section('title', 'Buat Sosialisasi')

@section('content')
<div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8"
    x-data="{
        noInstitusi: {{ old('institution_id') ? 'false' : (old('location_name') ? 'true' : 'false') }},
        selectedEmployees: {{ json_encode(old('employee_ids', [])) }},
        search: '',
        employees: {{ $eligibleEmployees->map(fn($e) => ['id' => $e->id, 'name' => $e->full_name])->values()->toJson() }},
        get filtered() {
            if (!this.search) return this.employees;
            const s = this.search.toLowerCase();
            return this.employees.filter(e => e.name.toLowerCase().includes(s));
        },
        get allSelected() {
            return this.filtered.length > 0 && this.filtered.every(e => this.selectedEmployees.includes(e.id));
        },
        toggleAll() {
            const ids = this.filtered.map(e => e.id);
            const allChecked = ids.every(id => this.selectedEmployees.includes(id));
            if (allChecked) {
                this.selectedEmployees = this.selectedEmployees.filter(id => !ids.includes(id));
            } else {
                this.selectedEmployees = [...new Set([...this.selectedEmployees, ...ids])];
            }
        }
    }">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('socializations.index') }}" class="text-gray-500 hover:text-gray-700">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <h1 class="text-xl font-bold text-gray-900">Buat Sosialisasi Baru</h1>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('socializations.store') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Area info --}}
        <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-600">
            <span class="font-medium text-gray-700">Area:</span>
            {{ $area?->name ?? 'Semua Area' }}
            @if ($area?->region)
                <span class="text-gray-400 mx-1">·</span>
                <span class="text-gray-400">{{ $area->region->name }}</span>
            @endif
        </div>

        {{-- SECTION: Lokasi --}}
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">Lokasi Sosialisasi</h2>

            {{-- Institution dropdown --}}
            <div x-show="!noInstitusi">
                <label for="institution_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Institusi / Sekolah <span class="text-red-500">*</span>
                </label>
                <select name="institution_id" id="institution_id"
                    class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('institution_id') border-red-300 @enderror">
                    <option value="">-- Pilih Institusi --</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                            {{ $inst->institution_name }}
                        </option>
                    @endforeach
                </select>
                @error('institution_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Toggle: bukan institusi --}}
            <label class="flex items-center gap-3 cursor-pointer select-none">
                <input type="checkbox" x-model="noInstitusi"
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <span class="text-sm text-gray-700">Institusi tidak ditemukan / bukan sekolah</span>
            </label>

            {{-- location_name muncul saat toggle --}}
            <div x-show="noInstitusi" x-cloak>
                <label for="location_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Lokasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="location_name" id="location_name"
                    value="{{ old('location_name') }}"
                    placeholder="Contoh: Gedung Serbaguna RT 05 Kelurahan Antang"
                    class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('location_name') border-red-300 @enderror">
                @error('location_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- SECTION: Jadwal --}}
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">Jadwal</h2>

            <div>
                <label for="schedule_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date" name="schedule_date" id="schedule_date"
                    value="{{ old('schedule_date') }}" required
                    class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('schedule_date') border-red-300 @enderror">
                @error('schedule_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="start_time" id="start_time"
                        value="{{ old('start_time') }}" required
                        class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('start_time') border-red-300 @enderror">
                    @error('start_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">
                        Jam Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="end_time" id="end_time"
                        value="{{ old('end_time') }}" required
                        class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('end_time') border-red-300 @enderror">
                    @error('end_time') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- SECTION: Pegawai yang ditugaskan --}}
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700">
                    Pegawai yang Ditugaskan <span class="text-red-500">*</span>
                </h2>
                <button type="button" @click="toggleAll()"
                    class="text-xs font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none"
                    x-text="allSelected ? 'Batal Semua' : 'Pilih Semua'">
                </button>
            </div>

            @error('employee_ids') <p class="text-xs text-red-600">{{ $message }}</p> @enderror

            @if ($eligibleEmployees->isEmpty())
                <p class="text-sm text-gray-400">Tidak ada pegawai di area ini.</p>
            @else
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[16px]">search</span>
                    <input type="text" x-model="search" placeholder="Cari nama pegawai..."
                        style="padding-left: 2rem !important"
                        class="w-full pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 transition-colors">
                </div>

                <div class="border border-gray-200 rounded-xl max-h-52 overflow-y-auto divide-y divide-gray-50">
                    <template x-for="emp in filtered" :key="emp.id">
                        <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox"
                                :value="emp.id"
                                x-model="selectedEmployees"
                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="text-sm text-gray-700" x-text="emp.name"></span>
                        </label>
                    </template>
                    <p x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-400 text-center">
                        Tidak ditemukan.
                    </p>
                </div>

                <p class="text-xs text-gray-400" x-show="selectedEmployees.length > 0">
                    <span x-text="selectedEmployees.length"></span> pegawai dipilih
                </p>

                {{-- Hidden inputs untuk yang dipilih --}}
                <template x-for="id in selectedEmployees" :key="id">
                    <input type="hidden" name="employee_ids[]" :value="id">
                </template>
            @endif
        </div>

        {{-- SECTION: Fee Partner --}}
        <div class="bg-white shadow-sm rounded-xl border border-gray-100 p-6 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">Fee Partner <span class="text-gray-400 font-normal">(opsional)</span></h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="partner_fee_amount" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Fee (Rp)</label>
                    <input type="number" name="partner_fee_amount" id="partner_fee_amount"
                        value="{{ old('partner_fee_amount') }}" min="0" step="1000" placeholder="0"
                        class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="partner_fee_due_date" class="block text-sm font-medium text-gray-700 mb-1">Tenggat Fee</label>
                    <input type="date" name="partner_fee_due_date" id="partner_fee_due_date"
                        value="{{ old('partner_fee_due_date') }}"
                        class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
        </div>

        {{-- Actions: dua tombol sesuai flow.md --}}
        <div class="flex justify-end gap-3 pb-4">
            <a href="{{ route('socializations.index') }}"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" name="schedule_now" value="0"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Simpan sebagai Draft
            </button>
            <button type="submit" name="schedule_now" value="1"
                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                Jadwalkan
            </button>
        </div>
    </form>
</div>
@endsection
