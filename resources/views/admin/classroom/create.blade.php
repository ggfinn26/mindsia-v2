@extends('layouts.dashboard')

@section('title', 'Buat Kelas Baru - MINDSIA')
@section('header_title', 'Buat Kelas Baru')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('classrooms.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span> Kembali ke Semua Kelas
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Akademik</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Buat Kelas Baru</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Tambahkan kelas baru dengan konfigurasi program, cabang, tutor, dan jadwal belajar.
        </p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center gap-2"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">school</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Informasi Kelas Baru</h2>
        </div>

        <form action="{{ route('classrooms.store') }}" method="POST"
              x-data="classroomForm({{ Illuminate\Support\Js::from($tutors) }}, {{ Illuminate\Support\Js::from($branches) }}, {{ Illuminate\Support\Js::from(old('branch_id')) }}, {{ Illuminate\Support\Js::from(old('tutor_id')) }})">
            @csrf

            <div class="p-6 space-y-5">
                {{-- Program --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Program <span class="text-red-500">*</span>
                    </label>
                    <select name="program_id" required
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Program</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                    @error('program_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cabang --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Cabang <span class="text-red-500">*</span>
                    </label>
                    <select name="branch_id" required x-model="selectedBranchId" @change="onBranchChange()"
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Cabang</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Kelas --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Nama Kelas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="class_name" required
                           value="{{ old('class_name') }}" placeholder="Contoh: TOEFL Intensive Batch 5"
                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('class_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tutor Custom Dropdown --}}
                <div @click.away="tutorOpen = false" @keydown.escape.window="tutorOpen = false" class="relative">
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Tutor <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div @click="tutorOpen = !tutorOpen"
                             class="w-full text-sm px-3.5 py-2.5 rounded-lg cursor-pointer flex justify-between items-center transition-all"
                             style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);">
                            <span :style="selectedTutorId ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'" x-text="tutorLabel || 'Pilih Tutor'"></span>
                            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-text-muted);">expand_more</span>
                        </div>
                        <div x-show="tutorOpen" x-transition x-cloak
                             class="absolute z-50 w-full mt-1 rounded-xl shadow-xl overflow-hidden"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                            <div class="p-2.5" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-[16px]"
                                          style="color: var(--dash-text-muted);">search</span>
                                    <input type="text" x-model="tutorSearch" placeholder="Cari tutor..."
                                           style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           class="w-full pr-3 py-1.5 rounded-lg text-sm focus:outline-none transition-all"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'"
                                           @click.stop>
                                </div>
                            </div>
                            <template x-if="!selectedBranchId">
                                <p class="px-4 py-3 text-sm text-center" style="color: var(--dash-text-muted);">Pilih cabang terlebih dahulu</p>
                            </template>
                            <template x-if="selectedBranchId">
                                <ul class="max-h-56 overflow-y-auto py-1 divide-y" style="border-color: var(--dash-card-border);">
                                    <template x-if="filteredTutors.length === 0">
                                        <li class="px-4 py-3 text-sm text-center" style="color: var(--dash-text-muted);">Tidak ada tutor tersedia untuk cabang ini</li>
                                    </template>
                                    <template x-for="t in filteredTutors" :key="t.id">
                                        <li @click="selectTutor(t)"
                                            class="px-4 py-2.5 text-sm cursor-pointer transition-colors"
                                            :style="selectedTutorId == t.id ? 'background-color: var(--dash-nav-hover-bg); color: var(--dash-primary-text); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                            onmouseout="this.style.backgroundColor=(selectedTutorId == t.id ? 'var(--dash-nav-hover-bg)' : 'transparent')">
                                            <div class="flex justify-between items-center">
                                                <span class="font-medium" x-text="t.name"></span>
                                                <span class="text-xs ml-2 px-2 py-0.5 rounded-full" style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);" x-text="t.class_count + ' kelas aktif'"></span>
                                            </div>
                                            <div class="text-xs mt-0.5" style="color: var(--dash-text-muted);" x-text="t.area_name + ' · ' + t.branch_name"></div>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                        </div>
                    </div>
                    <input type="hidden" name="tutor_id" :value="selectedTutorId">
                    @error('tutor_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Hari Utama & Jumlah Minggu --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Hari Utama <span class="text-red-500">*</span>
                        </label>
                        <select name="day_of_week" id="day_of_week" required
                                class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'"
                                onchange="updateSecondaryDay(this.value)">
                            <option value="">Pilih Hari</option>
                            @foreach(\App\Models\ClassRoom::PRIMARY_DAYS as $day)
                                <option value="{{ $day }}" {{ old('day_of_week') === $day ? 'selected' : '' }}>{{ $day }}</option>
                            @endforeach
                        </select>
                        <p id="secondary_day_info" class="text-xs mt-1.5 font-medium hidden" style="color: var(--dash-primary-text);"></p>
                        @error('day_of_week')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Jumlah Minggu <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="week_count" required min="1" max="52"
                               value="{{ old('week_count', 12) }}"
                               class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        @error('week_count')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Mulai --}}
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="start_date" required
                           value="{{ old('start_date') }}" min="{{ now()->format('Y-m-d') }}"
                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    @error('start_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jam Pelajaran - Hari Utama --}}
                <div class="pt-5" style="border-top: 1px solid var(--dash-card-border);">
                    <h3 class="font-jakarta text-sm font-bold mb-4" style="color: var(--dash-text-heading);">
                        Jam Pelajaran - Hari Utama
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                                Jam Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="start_time_primary" required
                                   value="{{ old('start_time_primary') }}"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            @error('start_time_primary')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                                Jam Selesai <span class="text-red-500">*</span>
                            </label>
                            <input type="time" name="end_time_primary" required
                                   value="{{ old('end_time_primary') }}"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            @error('end_time_primary')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Jam Pelajaran - Hari Kedua (Opsional) --}}
                <div class="pt-5" style="border-top: 1px solid var(--dash-card-border);">
                    <h3 class="font-jakarta text-sm font-bold mb-1" style="color: var(--dash-text-heading);">
                        Jam Pelajaran - Hari Kedua (Opsional)
                    </h3>
                    <p class="text-xs mb-4" style="color: var(--dash-text-muted);">
                        Hari kedua otomatis dari pasangan hari utama. Jika jam tidak diisi, menggunakan jam hari utama.
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                                Jam Mulai
                            </label>
                            <input type="time" name="start_time_secondary"
                                   value="{{ old('start_time_secondary') }}"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            @error('start_time_secondary')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                                Jam Selesai
                            </label>
                            <input type="time" name="end_time_secondary"
                                   value="{{ old('end_time_secondary') }}"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                            @error('end_time_secondary')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="px-6 py-4 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('classrooms.index') }}"
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
                    Buat Kelas
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const dayPairs = {
    'Senin': 'Kamis', 'Kamis': 'Senin',
    'Selasa': 'Jumat', 'Jumat': 'Selasa',
    'Rabu': 'Sabtu', 'Sabtu': 'Rabu',
};
function updateSecondaryDay(day) {
    const info = document.getElementById('secondary_day_info');
    if (dayPairs[day]) {
        info.textContent = 'Hari kedua: ' + dayPairs[day];
        info.classList.remove('hidden');
    } else {
        info.classList.add('hidden');
    }
}
const initialDay = document.getElementById('day_of_week').value;
if (initialDay) updateSecondaryDay(initialDay);

function classroomForm(tutors, branches, initialBranchId, initialTutorId) {
    return {
        tutors,
        branchAreaMap: Object.fromEntries(branches.map(b => [String(b.id), b.areas_id])),
        selectedBranchId: initialBranchId ? String(initialBranchId) : '',
        selectedTutorId: initialTutorId ? String(initialTutorId) : '',
        tutorSearch: '',
        tutorOpen: false,
        get selectedAreaId() {
            return this.branchAreaMap[this.selectedBranchId] ?? null;
        },
        get filteredTutors() {
            const areaId = this.selectedAreaId;
            const branchId = this.selectedBranchId;
            let list = this.tutors.filter(t =>
                String(t.branch_id) === branchId || String(t.area_id) === String(areaId)
            );
            if (this.tutorSearch) {
                const q = this.tutorSearch.toLowerCase();
                list = list.filter(t => t.name.toLowerCase().includes(q));
            }
            return list;
        },
        get tutorLabel() {
            const t = this.tutors.find(t => String(t.id) === this.selectedTutorId);
            return t ? t.name + ' (' + t.class_count + ' kelas aktif)' : '';
        },
        onBranchChange() {
            const stillAvailable = this.filteredTutors.some(t => String(t.id) === this.selectedTutorId);
            if (!stillAvailable) this.selectedTutorId = '';
        },
        selectTutor(t) {
            this.selectedTutorId = String(t.id);
            this.tutorOpen = false;
            this.tutorSearch = '';
        },
    };
}
</script>
@endsection
