@extends('layouts.dashboard')

@section('title', 'Buat Kelas Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('classrooms.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Buat Kelas Baru</h1>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Atur jadwal, tutor, dan program untuk kelas yang baru.</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #fecaca; background-color: #fef2f2; color: #991b1b;">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('classrooms.store') }}" method="POST" class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-2xl overflow-hidden"
          x-data="classroomForm({{ Illuminate\Support\Js::from($tutors) }}, {{ Illuminate\Support\Js::from($branches) }}, {{ Illuminate\Support\Js::from(old('branch_id')) }}, {{ Illuminate\Support\Js::from(old('tutor_id')) }})">
        @csrf
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Program <span class="text-red-500">*</span></label>
                <select name="program_id" required
                        class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]">
                    <option value="">Pilih program...</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>{{ $program->program_name }}</option>
                    @endforeach
                </select>
                @error('program_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Cabang <span class="text-red-500">*</span></label>
                <select name="branch_id" required x-model="selectedBranchId" @change="onBranchChange()"
                        class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]">
                    <option value="">Pilih cabang...</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
                @error('branch_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Nama Kelas <span class="text-red-500">*</span></label>
                <input type="text" name="class_name" required
                       class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                       value="{{ old('class_name') }}" placeholder="Contoh: TOEFL Intensive Batch 5" />
                @error('class_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tutor dropdown --}}
            <div @click.away="tutorOpen = false" @keydown.escape.window="tutorOpen = false">
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Tutor <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div @click="tutorOpen = !tutorOpen"
                         class="w-full px-4 py-2 border border-[var(--dash-input-border)] rounded-lg bg-[var(--dash-card-bg)] cursor-pointer flex justify-between items-center">
                        <span :class="selectedTutorId ? 'text-[var(--dash-text-heading)]' : 'text-[color:var(--dash-text-faint)]'" x-text="tutorLabel || 'Pilih tutor...'"></span>
                        <span class="material-symbols-outlined text-[18px] text-[color:var(--dash-text-faint)]">expand_more</span>
                    </div>
                    <div x-show="tutorOpen" x-transition x-cloak
                         class="absolute z-50 w-full mt-1 bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-lg shadow-lg overflow-hidden">
                        <div class="p-2 bg-[var(--dash-page-bg)] border-b border-[color:var(--dash-card-border)]">
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-2 text-[color:var(--dash-text-faint)] text-[16px]">search</span>
                                <input type="text" x-model="tutorSearch" placeholder="Cari tutor..."
                                       style="padding-left: 2.5rem !important" class="w-full pr-3 py-1.5 bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-md text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                                       @click.stop>
                            </div>
                        </div>
                        <template x-if="!selectedBranchId">
                            <p class="px-4 py-3 text-sm text-[color:var(--dash-text-faint)] text-center">Pilih cabang terlebih dahulu</p>
                        </template>
                        <template x-if="selectedBranchId">
                            <ul class="max-h-56 overflow-y-auto py-1">
                                <template x-if="filteredTutors.length === 0">
                                    <li class="px-4 py-2 text-sm text-[var(--dash-text-muted)] text-center">Tidak ada tutor tersedia untuk cabang ini</li>
                                </template>
                                <template x-for="t in filteredTutors" :key="t.id">
                                    <li @click="selectTutor(t)"
                                        class="px-4 py-2.5 text-sm cursor-pointer hover:bg-blue-500 hover:text-white"
                                        :class="selectedTutorId == t.id ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-[var(--dash-text-body)]'">
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium" x-text="t.name"></span>
                                            <span class="text-xs opacity-60 ml-2" x-text="t.class_count + ' kelas aktif'"></span>
                                        </div>
                                        <div class="text-xs opacity-60 mt-0.5" x-text="t.area_name + ' · ' + t.branch_name"></div>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>
                </div>
                <input type="hidden" name="tutor_id" :value="selectedTutorId">
                @error('tutor_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Hari Utama <span class="text-red-500">*</span></label>
                    <select name="day_of_week" id="day_of_week" required
                            class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                            onchange="updateSecondaryDay(this.value)">
                        <option value="">Pilih hari...</option>
                        @foreach(\App\Models\ClassRoom::PRIMARY_DAYS as $day)
                            <option value="{{ $day }}" {{ old('day_of_week') === $day ? 'selected' : '' }}>{{ $day }}</option>
                        @endforeach
                    </select>
                    <p id="secondary_day_info" class="text-sm text-[var(--dash-primary)] mt-1 hidden"></p>
                    @error('day_of_week')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Jumlah Minggu <span class="text-red-500">*</span></label>
                    <input type="number" name="week_count" required min="1" max="52"
                           class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                           value="{{ old('week_count', 12) }}" />
                    @error('week_count')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                <input type="date" name="start_date" required
                       class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                       value="{{ old('start_date') }}" min="{{ now()->format('Y-m-d') }}" />
                @error('start_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="border-t border-[var(--dash-card-border)] pt-6">
                <h3 class="text-md font-semibold text-[var(--dash-text-heading)] mb-4">Jam Pelajaran — Hari Utama</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="time" name="start_time_primary" required
                               class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                               value="{{ old('start_time_primary') }}" />
                        @error('start_time_primary')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="time" name="end_time_primary" required
                               class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                               value="{{ old('end_time_primary') }}" />
                        @error('end_time_primary')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-[var(--dash-card-border)] pt-6">
                <h3 class="text-md font-semibold text-[var(--dash-text-heading)] mb-4">Jam Pelajaran — Hari Kedua (Opsional)</h3>
                <p class="text-sm text-[var(--dash-text-muted)] mb-4">Hari kedua otomatis dari pasangan hari utama. Jika jam tidak diisi, menggunakan jam hari utama.</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Jam Mulai</label>
                        <input type="time" name="start_time_secondary"
                               class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                               value="{{ old('start_time_secondary') }}" />
                        @error('start_time_secondary')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--dash-text-body)] mb-1">Jam Selesai</label>
                        <input type="time" name="end_time_secondary"
                               class="w-full px-4 py-2 bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] border border-[var(--dash-input-border)] rounded-lg focus:ring-2 focus:ring-[var(--dash-primary)]/30 focus:border-[var(--dash-primary)]"
                               value="{{ old('end_time_secondary') }}" />
                        @error('end_time_secondary')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-[var(--dash-page-bg)] border-t border-[var(--dash-card-border)] flex gap-3 justify-end">
            <a href="{{ route('classrooms.index') }}" class="px-6 py-2 text-[var(--dash-text-body)] border border-[var(--dash-input-border)] rounded-lg hover:bg-[var(--dash-page-bg)]">Batal</a>
            <button type="submit" class="px-6 py-2 bg-[var(--dash-primary)] text-white rounded-lg hover:opacity-90">Buat Kelas</button>
        </div>
    </form>
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
        // branch_id → areas_id map
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
            // reset tutor jika tidak lagi tersedia di cabang/area baru
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
