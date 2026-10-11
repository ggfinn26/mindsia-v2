@extends('layouts.dashboard')

@section('title', 'Detail Aturan Jadwal Kerja - MINDSIA')
@section('header_title', 'Aturan Jadwal Kerja')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('work-schedule-rules.index') }}" class="dash-back inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-medium mb-3 transition-colors"
               style="border-color: var(--dash-card-border); color: var(--dash-text-muted); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali ke Aturan Jadwal Kerja
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Presensi</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">{{ $rule->setting_name }}</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Detail aturan jadwal kerja dan konfigurasi penugasan ke pegawai, posisi, atau role.
            </p>
        </div>
        @can('attendance.schedule_rule.manage')
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('work-schedule-rules.edit', $rule) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">edit</span> Edit
                </a>
                <button type="button"
                        @click="showConfirmModal = true; confirmTitle = 'Hapus Aturan Jadwal Kerja'; confirmMessage = 'Yakin ingin menghapus aturan {{ addslashes($rule->setting_name) }}? Aturan tidak bisa dihapus jika sudah memiliki penugasan aktif.'; confirmActionUrl = '{{ route('work-schedule-rules.destroy', $rule) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: #dc2626;">
                    <span class="material-symbols-outlined text-[16px]">delete</span> Hapus
                </button>
            </div>
        @endcan
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
            <div class="flex-1">
                <p class="font-bold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Detail Card --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center gap-2 border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">info</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Parameter Jadwal Kerja</h2>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Jam Mulai Kerja</p>
                <p class="text-base font-semibold tabular-nums" style="color: var(--dash-text-heading);">
                    {{ $rule->start_time ? \Carbon\Carbon::parse($rule->start_time)->format('H:i') : '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Jam Selesai Kerja</p>
                <p class="text-base font-semibold tabular-nums" style="color: var(--dash-text-heading);">
                    {{ $rule->end_time ? \Carbon\Carbon::parse($rule->end_time)->format('H:i') : '-' }}
                </p>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Jam Istirahat</p>
                <p class="text-base font-semibold tabular-nums" style="color: var(--dash-text-heading);">
                    @if($rule->break_start_time && $rule->break_end_time)
                        {{ \Carbon\Carbon::parse($rule->break_start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($rule->break_end_time)->format('H:i') }}
                    @else
                        <span class="text-sm font-normal" style="color: var(--dash-text-faint);">Tidak ditentukan</span>
                    @endif
                </p>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Toleransi Keterlambatan</p>
                <p class="text-base font-semibold tabular-nums" style="color: var(--dash-text-heading);">
                    {{ $rule->late_tolerance_minutes }} menit
                </p>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Toleransi Pulang Cepat</p>
                <p class="text-base font-semibold tabular-nums" style="color: var(--dash-text-heading);">
                    {{ $rule->early_leave_tolerance_minutes }} menit
                </p>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Kewajiban Presensi</p>
                <div>
                    @if ($rule->is_required)
                        <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-md border"
                              style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border); color: var(--dash-primary-text);">
                            Wajib
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-md border"
                              style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border); color: var(--dash-text-muted);">
                            Opsional
                        </span>
                    @endif
                </div>
            </div>

            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Status Aturan</p>
                <div>
                    @if ($rule->is_active)
                        <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-md border"
                              style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
                            Aktif
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded-md border"
                              style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border); color: var(--dash-text-muted);">
                            Nonaktif
                        </span>
                    @endif
                </div>
            </div>

            <div class="sm:col-span-2 md:col-span-2">
                <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--dash-text-muted);">Hari Kerja Operasional</p>
                @php
                    $dayNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                    $activeDayNums = $rule->workDays->pluck('day_of_week')->toArray();
                @endphp
                @if($activeDayNums)
                    <div class="flex flex-wrap gap-2">
                        @foreach($dayNames as $num => $label)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-semibold border"
                                  style="{{ in_array($num, $activeDayNums)
                                      ? 'background-color: var(--dash-nav-hover-bg); border-color: var(--dash-primary); color: var(--dash-primary-text);'
                                      : 'background-color: var(--dash-card-bg); border-color: var(--dash-card-border); color: var(--dash-text-faint); text-decoration: line-through;' }}">
                                {{ $label }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm font-medium" style="color: var(--dash-text-body);">Semua hari berlaku (setiap hari operasional)</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Penugasan Jadwal (Assignments) --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Penugasan Jadwal (Assignments)</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                    Aturan ini dapat ditugaskan secara hierarkis (Prioritas: Pegawai > Posisi > Role).
                </p>
            </div>
        </div>

        {{-- Add Assignment Form --}}
        @can('attendance.schedule_rule.manage')
            <div class="rounded-2xl border shadow-sm p-6"
                 style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);"
                 x-data="workScheduleAssign()">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b" style="border-color: var(--dash-card-border);">
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">person_add</span>
                    <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Tambah Penugasan Baru</h3>
                </div>

                <form action="{{ route('work-schedule-assignments.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="work_schedule_rule_id" value="{{ $rule->id }}">
                    <input type="hidden" name="assignable_type" :value="type">

                    {{-- Step 1: Pilih target --}}
                    <div class="relative max-w-xs" @click.outside="open = false">
                        <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                            Target Penugasan <span class="text-red-500">*</span>
                        </label>
                        <div @click="open = !open"
                             class="w-full px-3.5 py-2.5 rounded-lg text-sm border flex justify-between items-center cursor-pointer transition-all min-h-[42px]"
                             :style="open
                                 ? 'background-color: var(--dash-input-bg); border-color: var(--dash-primary); box-shadow: 0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent);'
                                 : 'background-color: var(--dash-input-bg); border-color: var(--dash-input-border);'">
                            <span class="truncate block pr-2"
                                  :style="type ? 'color: var(--dash-text-heading); font-weight: 500;' : 'color: var(--dash-text-muted);'"
                                  x-text="typeLabel || 'Pilih target...'"></span>
                            <span class="material-symbols-outlined text-[18px] transition-transform duration-200"
                                  style="color: var(--dash-text-muted);"
                                  :class="open ? 'rotate-180' : ''">expand_more</span>
                        </div>
                        <div x-show="open" x-transition
                             class="absolute z-50 w-full mt-1.5 rounded-xl shadow-xl border overflow-hidden"
                             style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
                            <ul class="py-1">
                                <template x-for="opt in options" :key="opt.value">
                                    <li @click="pick(opt.value, opt.label)"
                                        class="px-4 py-2.5 text-xs font-semibold cursor-pointer transition-colors"
                                        :style="type === opt.value
                                            ? 'background-color: var(--dash-nav-hover-bg); color: var(--dash-primary-text);'
                                            : 'color: var(--dash-text-body);'"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="if(type !== opt.value) this.style.backgroundColor='transparent'"
                                        x-text="opt.label"></li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    {{-- Step 2: Checklist options --}}
                    <template x-if="type === 'role'">
                        <div class="rounded-xl border overflow-hidden"
                             style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg);">
                            <div class="flex items-center justify-between px-4 py-2.5 border-b"
                                 style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">Daftar Role</span>
                                <button type="button"
                                        @click="$el.closest('[x-data]').querySelectorAll('input[name=\'assignable_id[]\']').forEach(i => i.checked = true)"
                                        class="text-xs font-semibold hover:underline"
                                        style="color: var(--dash-primary-text);">
                                    Pilih Semua
                                </button>
                            </div>
                            <div class="divide-y max-h-56 overflow-y-auto" style="border-color: var(--dash-card-border);">
                                @foreach($roles as $role)
                                    <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition-colors"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='transparent'">
                                        <input type="checkbox" name="assignable_id[]" value="{{ $role->id }}"
                                               class="w-4 h-4 rounded cursor-pointer"
                                               style="accent-color: var(--dash-primary);">
                                        <span class="text-sm font-medium" style="color: var(--dash-text-body);">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </template>

                    <template x-if="type === 'position'">
                        <div class="rounded-xl border overflow-hidden"
                             style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg);">
                            <div class="flex items-center justify-between px-4 py-2.5 border-b"
                                 style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">Daftar Posisi</span>
                                <button type="button"
                                        @click="$el.closest('[x-data]').querySelectorAll('input[name=\'assignable_id[]\']').forEach(i => i.checked = true)"
                                        class="text-xs font-semibold hover:underline"
                                        style="color: var(--dash-primary-text);">
                                    Pilih Semua
                                </button>
                            </div>
                            <div class="divide-y max-h-56 overflow-y-auto" style="border-color: var(--dash-card-border);">
                                @foreach($positions as $position)
                                    <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition-colors"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='transparent'">
                                        <input type="checkbox" name="assignable_id[]" value="{{ $position->id }}"
                                               class="w-4 h-4 rounded cursor-pointer"
                                               style="accent-color: var(--dash-primary);">
                                        <span class="text-sm font-medium" style="color: var(--dash-text-body);">{{ $position->position_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </template>

                    <template x-if="type === 'employee'">
                        <div class="rounded-xl border overflow-hidden"
                             style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg);">
                            <div class="flex items-center justify-between px-4 py-2.5 border-b"
                                 style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">Daftar Pegawai</span>
                                <button type="button"
                                        @click="$el.closest('[x-data]').querySelectorAll('input[name=\'assignable_id[]\']').forEach(i => i.checked = true)"
                                        class="text-xs font-semibold hover:underline"
                                        style="color: var(--dash-primary-text);">
                                    Pilih Semua
                                </button>
                            </div>
                            <div class="divide-y max-h-56 overflow-y-auto" style="border-color: var(--dash-card-border);">
                                @foreach($employees as $employee)
                                    <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition-colors"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='transparent'">
                                        <input type="checkbox" name="assignable_id[]" value="{{ $employee->id }}"
                                               class="w-4 h-4 rounded cursor-pointer"
                                               style="accent-color: var(--dash-primary);">
                                        <span class="text-sm font-medium" style="color: var(--dash-text-body);">{{ $employee->full_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </template>

                    {{-- Step 3: Tanggal Berlaku & Aktif --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                                Berlaku Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="effective_start_date" required value="{{ now()->toDateString() }}"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5" style="color: var(--dash-text-body);">
                                Berlaku Sampai <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(kosong = selamanya)</span>
                            </label>
                            <input type="date" name="effective_end_date"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>
                    </div>

                    <div class="pt-1">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" id="is_active" value="1" checked
                                   class="w-4 h-4 rounded cursor-pointer"
                                   style="accent-color: var(--dash-primary);">
                            <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">Aktifkan penugasan ini sekarang</span>
                        </label>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                            Simpan Penugasan
                        </button>
                    </div>
                </form>
            </div>
        @endcan

        {{-- Assignment List Card --}}
        <div class="rounded-2xl border shadow-sm overflow-hidden"
             style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
            <div class="px-6 py-4 flex items-center gap-2 border-b"
                 style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">assignment_ind</span>
                <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Daftar Penugasan Aktif & Riwayat</h3>
            </div>

            @if ($rule->assignments->isEmpty())
                <div class="px-6 py-10 text-center">
                    <span class="material-symbols-outlined text-[42px] block mx-auto mb-2"
                          style="color: var(--dash-text-faint);">person_off</span>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">Belum ada penugasan</p>
                    <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Aturan ini belum di-assign ke role, posisi, maupun pegawai manapun.</p>
                </div>
            @else
                @php
                    $typeLabels = [
                        \App\Models\Employee::class => 'Pegawai',
                        \App\Models\Position::class => 'Posisi',
                        \Spatie\Permission\Models\Role::class => 'Role',
                    ];
                @endphp
                <div class="divide-y" style="border-color: var(--dash-card-border);">
                    @foreach ($rule->assignments as $assignment)
                        @php
                            $typeLabel = $typeLabels[$assignment->assignable_type] ?? $assignment->assignable_type;
                            $entityName = match($assignment->assignable_type) {
                                \App\Models\Employee::class => $assignment->assignable?->full_name,
                                \App\Models\Position::class => $assignment->assignable?->position_name,
                                \Spatie\Permission\Models\Role::class => $assignment->assignable?->name,
                                default => $assignment->assignable_id,
                            };
                        @endphp
                        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                             onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                             onmouseout="this.style.backgroundColor='transparent'">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold border"
                                      style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border); color: var(--dash-primary-text);">
                                    {{ $typeLabel }}
                                </span>
                                <span class="text-sm font-bold" style="color: var(--dash-text-heading);">
                                    {{ $entityName ?? '-' }}
                                </span>
                                <span class="text-xs font-medium" style="color: var(--dash-text-muted);">
                                    {{ \Carbon\Carbon::parse($assignment->effective_start_date)->format('d M Y') }}
                                    @if($assignment->effective_end_date)
                                        - {{ \Carbon\Carbon::parse($assignment->effective_end_date)->format('d M Y') }}
                                    @else
                                        - selamanya
                                    @endif
                                </span>
                                @if(!$assignment->is_active)
                                    <span class="text-xs italic" style="color: var(--dash-text-faint);">(nonaktif)</span>
                                @endif
                            </div>

                            @can('attendance.schedule_rule.manage')
                                <div class="shrink-0">
                                    <button type="button"
                                            @click="showConfirmModal = true; confirmTitle = 'Hapus Penugasan Jadwal'; confirmMessage = 'Yakin ingin menghapus penugasan jadwal untuk {{ addslashes($entityName ?? '-') }}?'; confirmActionUrl = '{{ route('work-schedule-assignments.destroy', $assignment) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                            style="background-color: #dc2626;">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                        Hapus
                                    </button>
                                </div>
                            @endcan
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Custom Alpine Modal Dialog (TANPA overflow-hidden pada card modal) --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="showConfirmModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: #fee2e2; color: #dc2626;">
                            <span class="material-symbols-outlined text-[24px]">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showConfirmModal = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="button" @click="$refs.confirmForm.submit()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: #dc2626;"
                            x-text="confirmActionText">
                    </button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
