@extends('layouts.dashboard')

@section('title', 'Buat Sosialisasi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6"
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

    <div class="flex items-center gap-4">
        <a href="{{ route('socializations.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Buat Sosialisasi Baru</h1>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Atur jadwal, pilih institusi, dan tugaskan pegawai.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #fecaca; background-color: #fef2f2; color: #991b1b;">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('socializations.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Area info --}}
        <div class="bg-[var(--dash-page-bg)] border border-[var(--dash-card-border)] rounded-xl px-4 py-3 text-sm text-[var(--dash-text-body)]">
            <span class="font-medium text-[var(--dash-text-body)]">Area:</span>
            {{ $area?->name ?? 'Semua Area' }}
            @if ($area?->region)
                <span class="text-[var(--dash-text-faint)] mx-1">·</span>
                <span class="text-[var(--dash-text-faint)]">{{ $area->region->name }}</span>
            @endif
        </div>

        {{-- SECTION: Lokasi --}}
        <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[var(--dash-card-border)] p-6 space-y-4">
            <h2 class="text-sm font-semibold text-[var(--dash-text-body)]">Lokasi Sosialisasi</h2>

            {{-- Institution dropdown --}}
            <div x-show="!noInstitusi">
                <label for="institution_id" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                    Institusi / Sekolah <span class="text-red-500">*</span>
                </label>
                <select name="institution_id" id="institution_id"
                    class="block w-full border-[var(--dash-input-border)] rounded-lg shadow-sm text-sm focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('institution_id') border-red-300 @enderror">
                    <option value="">Pilih institusi...</option>
                    @foreach ($institutions as $inst)
                        <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>
                            {{ $inst->institution_name }}
                        </option>
                    @endforeach
                </select>
                @error('institution_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Toggle: bukan institusi --}}
            <label class="flex items-center gap-3 cursor-pointer select-none">
                <input type="checkbox" x-model="noInstitusi"
                    class="w-4 h-4 text-[var(--dash-primary)] border-[var(--dash-input-border)] rounded focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]">
                <span class="text-sm text-[var(--dash-text-body)]">Institusi tidak ditemukan / bukan sekolah</span>
            </label>

            {{-- location_name muncul saat toggle --}}
            <div x-show="noInstitusi" x-cloak>
                <label for="location_name" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                    Nama Lokasi <span class="text-red-500">*</span>
                </label>
                <input type="text" name="location_name" id="location_name"
                    value="{{ old('location_name') }}"
                    placeholder="Contoh: Gedung Serbaguna RT 05 Kelurahan Antang"
                    class="block w-full border-[var(--dash-input-border)] rounded-lg shadow-sm text-sm focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('location_name') border-red-300 @enderror">
                @error('location_name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- SECTION: Jadwal --}}
        <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[var(--dash-card-border)] p-6 space-y-4">
            <h2 class="text-sm font-semibold text-[var(--dash-text-body)]">Jadwal</h2>

            <div>
                <label for="schedule_date" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                    Tanggal <span class="text-red-500">*</span>
                </label>
                <input type="date" name="schedule_date" id="schedule_date"
                    value="{{ old('schedule_date') }}" required
                    class="block w-full border-[var(--dash-input-border)] rounded-lg shadow-sm text-sm focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('schedule_date') border-red-300 @enderror">
                @error('schedule_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                        Jam Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="start_time" id="start_time"
                        value="{{ old('start_time') }}" required
                        class="block w-full border-[var(--dash-input-border)] rounded-lg shadow-sm text-sm focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('start_time') border-red-300 @enderror">
                    @error('start_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">
                        Jam Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="end_time" id="end_time"
                        value="{{ old('end_time') }}" required
                        class="block w-full border-[var(--dash-input-border)] rounded-lg shadow-sm text-sm focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)] focus:border-[var(--dash-primary)] @error('end_time') border-red-300 @enderror">
                    @error('end_time') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- SECTION: Pegawai yang ditugaskan --}}
        <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[var(--dash-card-border)] p-6 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-[var(--dash-text-body)]">
                    Pegawai yang Ditugaskan <span class="text-red-500">*</span>
                </h2>
                <button type="button" @click="toggleAll()"
                    class="text-xs font-medium text-[var(--dash-primary)] hover:opacity-80 focus:outline-none"
                    x-text="allSelected ? 'Batal Semua' : 'Pilih Semua'">
                </button>
            </div>

            @error('employee_ids') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

            @if ($eligibleEmployees->isEmpty())
                <p class="text-sm text-[var(--dash-text-faint)]">Tidak ada pegawai di area ini.</p>
            @else
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[var(--dash-text-faint)] text-[16px]">search</span>
                    <input type="text" x-model="search" placeholder="Cari nama pegawai..."
                        style="padding-left: 2rem !important"
                        class="w-full pr-3 py-2 bg-[var(--dash-page-bg)] border border-[var(--dash-card-border)] rounded-lg text-sm focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 transition-colors">
                </div>

                <div class="border border-[var(--dash-card-border)] rounded-xl max-h-52 overflow-y-auto divide-y divide-gray-50">
                    <template x-for="emp in filtered" :key="emp.id">
                        <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-[var(--dash-page-bg)] cursor-pointer">
                            <input type="checkbox"
                                :value="emp.id"
                                x-model="selectedEmployees"
                                class="w-4 h-4 text-[var(--dash-primary)] border-[var(--dash-input-border)] rounded focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]">
                            <span class="text-sm text-[var(--dash-text-body)]" x-text="emp.name"></span>
                        </label>
                    </template>
                    <p x-show="filtered.length === 0" class="px-4 py-3 text-sm text-[var(--dash-text-faint)] text-center">
                        Tidak ditemukan.
                    </p>
                </div>

                <p class="text-xs text-[var(--dash-text-faint)]" x-show="selectedEmployees.length > 0">
                    <span x-text="selectedEmployees.length"></span> pegawai dipilih
                </p>

                {{-- Hidden inputs untuk yang dipilih --}}
                <template x-for="id in selectedEmployees" :key="id">
                    <input type="hidden" name="employee_ids[]" :value="id">
                </template>
            @endif
        </div>

        {{-- SECTION: Fee Partner --}}
        <div class="bg-[var(--dash-card-bg)] rounded-2xl border border-[var(--dash-card-border)] p-6 space-y-4">
            <h2 class="text-sm font-semibold text-[var(--dash-text-body)]">Fee Partner <span class="text-[var(--dash-text-faint)] font-normal">(opsional)</span></h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="partner_fee_amount" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Jumlah Fee (Rp)</label>
                    <input type="number" name="partner_fee_amount" id="partner_fee_amount"
                        value="{{ old('partner_fee_amount') }}" min="0" step="1000" placeholder="0"
                        class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]">
                </div>
                <div>
                    <label for="partner_fee_due_date" class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Tenggat Fee</label>
                    <input type="date" name="partner_fee_due_date" id="partner_fee_due_date"
                        value="{{ old('partner_fee_due_date') }}"
                        class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]">
                </div>
            </div>
        </div>

        {{-- Actions: dua tombol sesuai flow.md --}}
        <div class="flex justify-end gap-3 pb-4">
            <a href="{{ route('socializations.index') }}"
                class="px-4 py-2 text-sm font-medium text-[var(--dash-text-body)] bg-[var(--dash-card-bg)] border border-[var(--dash-input-border)] rounded-lg hover:bg-[var(--dash-page-bg)] inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">close</span>
                Batal
            </a>
            <button type="submit" name="schedule_now" value="0"
                class="px-4 py-2 text-sm font-medium text-[var(--dash-text-body)] bg-[var(--dash-card-bg)] border border-[var(--dash-input-border)] rounded-lg hover:bg-[var(--dash-page-bg)] inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Simpan sebagai Draft
            </button>
            <button type="submit" name="schedule_now" value="1"
                class="px-4 py-2 text-sm font-medium text-white bg-[var(--dash-primary)] rounded-lg hover:opacity-90">
                Jadwalkan
            </button>
        </div>
    </form>
</div>
@endsection
