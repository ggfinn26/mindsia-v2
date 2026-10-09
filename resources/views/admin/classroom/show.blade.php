@extends('layouts.dashboard')

@section('title', $class->class_name)
@section('header_title', $class->class_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{ showDeletePlanModal: false }">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('classrooms.index') }}"
               class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                        style="color: var(--dash-text-heading);">{{ $class->class_name }}</h1>
                    @php
                        $statusStyle = match($class->status) {
                            'planned'   => 'background-color:#fef3c7;color:#d97706;',
                            'active'    => 'background-color:#f0fdf4;color:#059669;',
                            'completed' => 'background-color:#eff6ff;color:#2563eb;',
                            'cancelled' => 'background-color:#fee2e2;color:#dc2626;',
                            default     => 'background-color:var(--dash-nav-hover-bg);color:var(--dash-text-muted);',
                        };
                        $statusLabel = match($class->status) {
                            'planned'   => 'Direncanakan',
                            'active'    => 'Aktif',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                            default     => $class->status,
                        };
                    @endphp
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                          style="{{ $statusStyle }}">{{ $statusLabel }}</span>
                </div>
                <p class="mt-0.5 text-sm" style="color: var(--dash-text-muted);">
                    {{ $class->program?->program_name }} &middot; {{ $class->branch?->branch_name }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0 flex-wrap justify-end">
            @can('class.manage')
            @if($class->status === 'planned')
                <a href="{{ route('classrooms.edit', $class) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[13px]">edit</span>Edit
                </a>
                <button type="button"
                        onclick="openConfirmModal('Aktifkan Kelas', 'Aktifkan kelas ini? Status akan berubah menjadi Aktif.', () => document.getElementById('activate-form').submit())"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: #059669;">
                    <span class="material-symbols-outlined text-[13px]">play_arrow</span>Aktifkan
                </button>
                <form id="activate-form" action="{{ route('classrooms.activate', $class) }}" method="POST" class="hidden">
                    @csrf @method('PATCH')
                </form>
            @endif
            @if(in_array($class->status, ['planned', 'active']))
                <button type="button"
                        onclick="openConfirmModal('Batalkan Kelas', 'Batalkan kelas ini? Status akan berubah menjadi Dibatalkan dan tidak bisa diubah lagi.', () => document.getElementById('cancel-form').submit())"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold"
                        style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                        onmouseover="this.style.backgroundColor='#fecaca'"
                        onmouseout="this.style.backgroundColor='#fee2e2'">
                    <span class="material-symbols-outlined text-[13px]">cancel</span>Batalkan Kelas
                </button>
                <form id="cancel-form" action="{{ route('classrooms.cancel', $class) }}" method="POST" class="hidden">
                    @csrf @method('PATCH')
                </form>
            @endif
            @endcan
            @can('class.graduate')
            @if($class->status === 'active')
                <button type="button"
                        onclick="openConfirmModal('Luluskan Semua Member', 'Luluskan semua member aktif di kelas ini? Tindakan ini tidak bisa dibatalkan.', () => document.getElementById('graduate-form').submit())"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: #7c3aed;">
                    <span class="material-symbols-outlined text-[13px]">school</span>Luluskan Semua
                </button>
                <form id="graduate-form" action="{{ route('classrooms.graduate', $class) }}" method="POST" class="hidden">
                    @csrf
                </form>
            @endif
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Info card --}}
    <div class="rounded-2xl p-5"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-5 text-sm">
            @foreach([
                ['Tutor', $class->tutor?->full_name ?? '-'],
                ['Jadwal', $class->day_of_week . ($class->day_of_week2 ? ' & ' . $class->day_of_week2 : '')],
                ['Periode', ($class->start_date?->format('d M Y') ?? '-') . ' — ' . ($class->end_date?->format('d M Y') ?? '-')],
                ['Jam Hari Utama', $class->start_time_primary . ' — ' . $class->end_time_primary],
                ['Jam Hari Kedua', $class->start_time_secondary && $class->end_time_secondary ? $class->start_time_secondary . ' — ' . $class->end_time_secondary : '-'],
                ['Jumlah Minggu', $class->week_count . ' minggu'],
                ['Member Aktif', $class->memberClasses->where('status', 'active')->count()],
            ] as [$label, $value])
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">{{ $label }}</p>
                <p class="font-medium" style="color: var(--dash-text-heading);">{{ $value }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Member Section --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between px-5 py-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">group</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Member Kelas</h2>
            </div>
            @can('class.member.assign')
                <button type="button"
                        onclick="document.getElementById('add-member-form').classList.toggle('hidden')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[13px]">person_add</span>Daftarkan Member
                </button>
            @endcan
        </div>

        @can('class.member.assign')
        <div id="add-member-form" class="hidden px-5 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);"
             x-data="memberDropdown({{ Illuminate\Support\Js::from($memberOptions) }})">
            <form action="{{ route('member-class.store', $class) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-body);">Pilih Member</label>
                        <div class="relative" @click.away="open = false">
                            <div @click="open = !open"
                                 class="w-full rounded-lg px-3 py-2 text-sm flex justify-between items-center cursor-pointer"
                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                <span x-text="selectedId ? selectedLabel : 'Pilih member...'"
                                      :class="selectedId ? '' : 'opacity-50'" class="truncate block pr-2 text-sm"></span>
                                <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-muted);">expand_more</span>
                            </div>
                            <div x-show="open" x-transition x-cloak
                                 class="absolute z-50 w-full mt-1 rounded-xl shadow-xl overflow-hidden"
                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                <div class="p-2" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-[16px]" style="color: var(--dash-text-muted);">search</span>
                                        <input type="text" x-model="search" placeholder="Cari nama member..."
                                               style="padding-left: 2.5rem !important; background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"
                                               class="w-full pr-3 py-2 rounded-lg text-sm focus:outline-none"
                                               @click.stop>
                                    </div>
                                </div>
                                <ul class="max-h-52 overflow-y-auto py-1">
                                    <li x-show="filtered.length === 0" class="px-4 py-3 text-xs text-center"
                                        style="color: var(--dash-text-muted);">Tidak ada member tersedia</li>
                                    <template x-for="m in filtered" :key="m.id">
                                        <li @click="select(m)"
                                            class="px-4 py-2 text-sm cursor-pointer hover:bg-blue-500 hover:text-white transition-colors"
                                            :style="selectedId == m.id ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary-text); font-weight:600;' : 'color: var(--dash-text-body);'"
                                            x-text="m.label"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" name="member_registration_id" x-model="selectedId" />
                        @error('member_registration_id')
                            <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">{{ $availableRegistrations->count() }} member tersedia</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-body);">Tanggal Mulai</label>
                        <input type="date" name="start_date" required
                               value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                        @error('start_date')
                            <p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <button type="submit"
                                :disabled="!selectedId"
                                :style="selectedId ? 'background-color: var(--dash-primary); cursor:pointer;' : 'background-color: var(--dash-nav-hover-bg); cursor:not-allowed; color: var(--dash-text-muted);'"
                                class="w-full py-2 rounded-xl text-sm font-semibold text-white transition-opacity hover:opacity-90">
                            Daftarkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endcan

        <table class="w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[40%]"><col class="w-[15%]"><col class="w-[20%]"><col class="w-[25%]">
            </colgroup>
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Mulai</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($class->memberClasses as $mc)
                    <tr x-data="{ transferOpen: false }"
                        style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor=''">
                        <td class="px-5 py-3 font-medium text-sm" style="color: var(--dash-text-heading);">
                            {{ $mc->memberRegistration?->memberData?->full_name ?? 'Member #' . $mc->member_registration_id }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $mcStyle = match($mc->status) {
                                    'active'    => 'background-color:#f0fdf4;color:#059669;',
                                    'completed' => 'background-color:#eff6ff;color:#2563eb;',
                                    default     => 'background-color:var(--dash-nav-hover-bg);color:var(--dash-text-muted);',
                                };
                            @endphp
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                  style="{{ $mcStyle }}">{{ ucfirst($mc->status) }}</span>
                        </td>
                        <td class="px-5 py-3 text-sm" style="color: var(--dash-text-muted);">
                            {{ $mc->start_date?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-5 py-3 text-right text-sm">
                            @can('class.manage')
                            @if($mc->status === 'active')
                                <span x-show="transferOpen" x-cloak class="inline-flex items-center gap-2">
                                    <form action="{{ route('member-class.transfer', $mc) }}" method="POST"
                                          class="inline-flex items-center gap-2">
                                        @csrf @method('PUT')
                                        <select name="new_class_id" required data-no-enhance
                                                class="rounded-lg px-2 py-1 text-xs focus:outline-none"
                                                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                            <option value="">Pilih kelas tujuan</option>
                                            @foreach($transferableClasses as $tc)
                                                <option value="{{ $tc->id }}">{{ $tc->class_name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit"
                                                class="text-xs font-semibold hover:opacity-75"
                                                style="color: var(--dash-primary);">Pindah</button>
                                    </form>
                                    <button type="button" @click="transferOpen = false"
                                            class="text-xs" style="color: var(--dash-text-muted);">Batal</button>
                                </span>

                                @if($transferableClasses->isNotEmpty())
                                    <button type="button" x-show="!transferOpen" @click="transferOpen = true"
                                            class="text-xs font-semibold mr-2 hover:opacity-75"
                                            style="color: var(--dash-primary);">Pindahkan</button>
                                @endif

                                <form id="deact-mc-{{ $mc->id }}" action="{{ route('member-class.deactivate', $mc) }}"
                                      method="POST" class="hidden">@csrf @method('PATCH')</form>
                                <button type="button" x-show="!transferOpen"
                                        onclick="openConfirmModal('Nonaktifkan Member', 'Nonaktifkan member ini dari kelas? Data kehadiran tetap tersimpan.', () => document.getElementById('deact-mc-{{ $mc->id }}').submit())"
                                        class="text-xs font-semibold mr-2 hover:opacity-75"
                                        style="color: #d97706;">Nonaktifkan</button>

                                <form id="rm-mc-{{ $mc->id }}" action="{{ route('member-class.destroy', $mc) }}"
                                      method="POST" class="hidden">@csrf @method('DELETE')</form>
                                <button type="button" x-show="!transferOpen"
                                        onclick="openConfirmModal('Hapus Member dari Kelas', 'Hapus permanen member ini dari kelas? Progress kurikulum juga akan dihapus.', () => document.getElementById('rm-mc-{{ $mc->id }}').submit())"
                                        class="text-xs font-semibold hover:opacity-75"
                                        style="color: #dc2626;">Hapus</button>
                            @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-sm" style="color: var(--dash-text-muted);">
                            Belum ada member di kelas ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Sesi & Absensi --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between px-5 py-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">calendar_month</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Sesi & Absensi</h2>
            </div>
            <span class="text-xs" style="color: var(--dash-text-muted);">{{ $class->schedules->count() }} sesi</span>
        </div>

        @if($class->schedules->isEmpty())
            <p class="px-5 py-10 text-sm text-center" style="color: var(--dash-text-muted);">
                Jadwal sesi akan otomatis dibuat saat kelas berstatus aktif.
            </p>
        @else
            @php $activeMembers = $class->memberClasses->where('status', 'active'); @endphp
            <div>
                @foreach($class->schedules->sortBy('schedule_date') as $schedule)
                    @php $isPast = $schedule->schedule_date <= today(); @endphp
                    <div x-data="{ open: false }" style="border-bottom: 1px solid var(--dash-card-border);">
                        <div class="px-5 py-4 flex items-center gap-3 cursor-pointer"
                             style="color: var(--dash-text-body);"
                             onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                             onmouseout="this.style.backgroundColor=''"
                             @click="open = !open">
                            <span class="text-sm font-medium w-24 shrink-0" style="color: var(--dash-text-heading);">
                                {{ $schedule->schedule_date?->format('d M Y') }}
                            </span>
                            <span class="text-sm w-16 shrink-0" style="color: var(--dash-text-muted);">
                                {{ $schedule->schedule_date?->locale('id')->isoFormat('dddd') }}
                            </span>
                            <span class="text-sm w-36 shrink-0" style="color: var(--dash-text-muted);">
                                {{ $schedule->start_time }} — {{ $schedule->end_time }}
                            </span>
                            <span class="text-sm flex-1 truncate" style="color: var(--dash-text-muted);">
                                {{ $schedule->material_taught ?? ($isPast ? '— belum diisi' : '— sesi mendatang') }}
                            </span>
                            @if(!$isPast)
                                <span class="text-xs rounded px-2 py-0.5 shrink-0"
                                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Mendatang</span>
                            @elseif($schedule->schedule_date->isToday())
                                <span class="text-xs rounded px-2 py-0.5 shrink-0"
                                      style="background-color: #eff6ff; color: #2563eb;">Hari ini</span>
                            @endif
                            <span class="material-symbols-outlined text-[18px] shrink-0" style="color: var(--dash-text-muted);"
                                  x-text="open ? 'expand_less' : 'expand_more'"></span>
                        </div>

                        <div x-show="open" x-cloak class="px-5 py-5 space-y-5"
                             style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">

                            @can('class.session.update')
                            @if($isPast)
                            <div>
                                <h4 class="text-xs font-semibold uppercase mb-2" style="color: var(--dash-text-muted);">Materi Sesi</h4>
                                <form action="{{ route('class-schedules.material', $schedule) }}" method="POST"
                                      class="flex gap-2">
                                    @csrf @method('PATCH')
                                    <input type="text" name="material_taught"
                                           value="{{ old('material_taught', $schedule->material_taught) }}"
                                           placeholder="Tulis materi yang diajarkan..."
                                           class="flex-1 rounded-lg px-3 py-2 text-sm focus:outline-none"
                                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                    <button type="submit"
                                            class="px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                            style="background-color: var(--dash-primary);">Simpan</button>
                                </form>
                            </div>
                            @else
                            <p class="text-xs" style="color: var(--dash-text-faint);">Materi hanya bisa diisi pada atau setelah tanggal sesi.</p>
                            @endif
                            @endcan

                            @can('class.attendance.record')
                            @if($isPast && $activeMembers->isNotEmpty())
                            <div>
                                <h4 class="text-xs font-semibold uppercase mb-2" style="color: var(--dash-text-muted);">Absensi & Nilai</h4>
                                <form action="{{ route('member-attendance.store', $schedule) }}" method="POST">
                                    @csrf
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="text-xs uppercase" style="color: var(--dash-text-muted);">
                                                <th class="text-left pb-2 pr-4 font-semibold">Member</th>
                                                <th class="text-left pb-2 pr-4 font-semibold w-36">Status</th>
                                                <th class="text-left pb-2 pr-4 font-semibold">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($activeMembers as $mc)
                                            <tr style="border-top: 1px solid var(--dash-card-border);">
                                                <td class="py-2 pr-4 font-medium" style="color: var(--dash-text-heading);">
                                                    {{ $mc->memberRegistration?->memberData?->full_name ?? 'Member #'.$mc->id }}
                                                </td>
                                                <td class="py-2 pr-4">
                                                    <input type="hidden" name="attendances[{{ $loop->index }}][member_class_id]" value="{{ $mc->id }}">
                                                    <select name="attendances[{{ $loop->index }}][status]" data-no-enhance
                                                            class="w-full rounded-lg px-2 py-1.5 text-sm focus:outline-none"
                                                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                                        <option value="present">Hadir</option>
                                                        <option value="absent">Absen</option>
                                                        <option value="late">Terlambat</option>
                                                        <option value="excused">Izin</option>
                                                        <option value="sick">Sakit</option>
                                                    </select>
                                                </td>
                                                <td class="py-2">
                                                    <input type="text" name="attendances[{{ $loop->index }}][notes]"
                                                           placeholder="Catatan (opsional)"
                                                           class="w-full rounded-lg px-2 py-1.5 text-sm focus:outline-none"
                                                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <button type="submit"
                                            class="mt-3 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                            style="background-color: #059669;">Simpan Absensi</button>
                                </form>
                            </div>
                            @endif
                            @endcan

                            @can('class.assessment.record')
                            @if($isPast && $activeMembers->isNotEmpty())
                            <div>
                                <h4 class="text-xs font-semibold uppercase mb-2" style="color: var(--dash-text-muted);">Nilai Sesi</h4>
                                <div class="space-y-2">
                                    @foreach($activeMembers as $mc)
                                    <form action="{{ route('member-assessments.store', $schedule) }}" method="POST"
                                          class="flex items-center gap-3">
                                        @csrf
                                        <input type="hidden" name="member_class_id" value="{{ $mc->id }}">
                                        <span class="text-sm w-40 shrink-0" style="color: var(--dash-text-body);">
                                            {{ $mc->memberRegistration?->memberData?->full_name ?? 'Member #'.$mc->id }}
                                        </span>
                                        <input type="number" name="score" min="0" max="100" placeholder="0-100"
                                               class="w-20 rounded-lg px-2 py-1.5 text-sm focus:outline-none"
                                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                        <input type="text" name="notes" placeholder="Catatan"
                                               class="flex-1 rounded-lg px-2 py-1.5 text-sm focus:outline-none"
                                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                                                style="background-color: var(--dash-primary);">Simpan</button>
                                    </form>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Test & Sertifikat --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between px-5 py-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">quiz</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Test & Sertifikat</h2>
            </div>
            @can('class.test.create')
                <a href="{{ route('class-tests.create', $class) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[13px]">add</span>Tambah Test
                </a>
            @endcan
        </div>

        @if($class->tests->isEmpty())
            <p class="px-5 py-10 text-sm text-center" style="color: var(--dash-text-muted);">Belum ada test untuk kelas ini.</p>
        @else
            @php $activeMembers = $class->memberClasses->where('status', 'active'); @endphp
            <div>
                @foreach($class->tests as $test)
                    <div x-data="{ open: false }" style="border-bottom: 1px solid var(--dash-card-border);">
                        <div class="px-5 py-4 flex items-center gap-3 cursor-pointer"
                             onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                             onmouseout="this.style.backgroundColor=''"
                             @click="open = !open">
                            <span class="text-sm font-medium flex-1" style="color: var(--dash-text-heading);">{{ $test->test_name }}</span>
                            @if($test->test_type === 'pre_test')
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold shrink-0"
                                      style="background-color: #eff6ff; color: #1d4ed8;">Pre-Test</span>
                            @else
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold shrink-0"
                                      style="background-color: #faf5ff; color: #7e22ce;">Post-Test</span>
                            @endif
                            <span class="text-sm shrink-0" style="color: var(--dash-text-muted);">
                                {{ $test->date?->format('d M Y') ?? '-' }}
                            </span>
                            @can('class.test.update')
                                <a href="{{ route('class-tests.edit', [$class, $test]) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors shrink-0"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                                   @click.stop>
                                    <span class="material-symbols-outlined text-[12px]">edit</span>Edit
                                </a>
                            @endcan
                            @can('class.test.delete')
                                <form id="del-test-{{ $test->id }}" action="{{ route('class-tests.destroy', [$class, $test]) }}"
                                      method="POST" class="hidden">@csrf @method('DELETE')</form>
                                <button type="button"
                                        onclick="openConfirmModal('Hapus Test', 'Hapus test {{ addslashes($test->test_name) }}? Semua nilai yang sudah diinput akan ikut terhapus.', () => document.getElementById('del-test-{{ $test->id }}').submit())"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold shrink-0"
                                        style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                                        onmouseover="this.style.backgroundColor='#fecaca'"
                                        onmouseout="this.style.backgroundColor='#fee2e2'"
                                        @click.stop>
                                    <span class="material-symbols-outlined text-[12px]">delete</span>Hapus
                                </button>
                            @endcan
                            <span class="material-symbols-outlined text-[18px] shrink-0" style="color: var(--dash-text-muted);"
                                  x-text="open ? 'expand_less' : 'expand_more'"></span>
                        </div>

                        @can('class.test.score')
                        <div x-show="open" x-cloak class="px-5 py-4"
                             style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                            <h4 class="text-xs font-semibold uppercase mb-3" style="color: var(--dash-text-muted);">Input Nilai Per Member</h4>
                            @php $resultsByMcId = $test->memberTestResults->keyBy('member_class_id'); @endphp
                            <div class="space-y-2">
                                @foreach($class->memberClasses as $mc)
                                    @php $existing = $resultsByMcId->get($mc->id); @endphp
                                    <form action="{{ route('member-test-results.store', [$mc, $test]) }}" method="POST"
                                          class="flex items-center gap-3 flex-wrap">
                                        @csrf
                                        <span class="text-sm w-40 shrink-0" style="color: var(--dash-text-body);">
                                            {{ $mc->memberRegistration?->memberData?->full_name ?? 'Member #'.$mc->id }}
                                        </span>
                                        <input type="text" name="level" value="{{ old('level', $existing?->level) }}"
                                               placeholder="Level (A/B/C/Lulus)"
                                               class="w-32 rounded-lg px-2 py-1.5 text-sm focus:outline-none"
                                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                        <input type="number" name="final_score" value="{{ old('final_score', $existing?->final_score) }}"
                                               min="0" placeholder="Nilai"
                                               class="w-24 rounded-lg px-2 py-1.5 text-sm focus:outline-none"
                                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                        <button type="submit"
                                                class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                                                style="background-color: var(--dash-primary);">
                                            {{ $existing ? 'Update' : 'Simpan' }}
                                        </button>
                                        @if($existing)
                                            <span class="text-xs" style="color: var(--dash-text-faint);">
                                                Tersimpan: Level {{ $existing->level }}, Nilai {{ $existing->final_score }}
                                            </span>
                                        @endif
                                    </form>
                                @endforeach
                            </div>
                        </div>
                        @endcan
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Sertifikat Member Lulus --}}
    @php $graduatedMembers = $class->memberClasses->filter(fn($mc) => $mc->memberRegistration?->graduation_status === 'LULUS'); @endphp
    @if($graduatedMembers->isNotEmpty())
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center gap-2 px-5 py-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">workspace_premium</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Sertifikat Member Lulus</h2>
        </div>
        <table class="w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[20%]"><col class="w-[18%]"><col class="w-[12%]">
                <col class="w-[12%]"><col class="w-[14%]"><col class="w-[24%]">
            </colgroup>
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    @foreach(['Member','No. Sertifikat','Tersedia','Hardcopy','Sudah Diambil',''] as $h)
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($graduatedMembers as $mc)
                    @php $cert = $mc->memberRegistration?->certificate; @endphp
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor=''">
                        <td class="px-4 py-3 font-medium text-sm" style="color: var(--dash-text-heading);">
                            {{ $mc->memberRegistration?->memberData?->full_name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm" style="color: var(--dash-text-muted);">{{ $cert?->certificate_number ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @if($cert?->certificate_available === 'available')
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                      style="background-color:#f0fdf4;color:#059669;">Tersedia</span>
                            @else
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                      style="background-color:var(--dash-nav-hover-bg);color:var(--dash-text-muted);">Belum</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm" style="color: var(--dash-text-muted);">
                            {{ $cert?->certificate_hardcopy ? 'Ya' : 'Tidak' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($cert?->certificate_taken === 'taken')
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                      style="background-color:#eff6ff;color:#2563eb;">Sudah Diambil</span>
                            @else
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                      style="background-color:var(--dash-nav-hover-bg);color:var(--dash-text-muted);">Belum Diambil</span>
                            @endif
                        </td>
                        @can('class.certificate.update')
                        <td class="px-4 py-3">
                            @if($cert)
                            <form action="{{ route('member-certificates.update', $cert) }}" method="POST"
                                  class="flex items-center gap-2 flex-wrap">
                                @csrf @method('PATCH')
                                <input type="text" name="certificate_number" value="{{ $cert->certificate_number }}"
                                       placeholder="No. Sertifikat"
                                       class="w-24 rounded-lg px-2 py-1 text-xs focus:outline-none"
                                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                <select name="certificate_available" data-no-enhance
                                        class="rounded-lg px-2 py-1 text-xs focus:outline-none"
                                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                    <option value="not_available" @selected($cert->certificate_available === 'not_available')>Belum Tersedia</option>
                                    <option value="available" @selected($cert->certificate_available === 'available')>Tersedia</option>
                                </select>
                                <select name="certificate_taken" data-no-enhance
                                        class="rounded-lg px-2 py-1 text-xs focus:outline-none"
                                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                    <option value="not_taken" @selected($cert->certificate_taken === 'not_taken')>Belum Diambil</option>
                                    <option value="taken" @selected($cert->certificate_taken === 'taken')>Sudah Diambil</option>
                                </select>
                                <input type="hidden" name="certificate_hardcopy" value="{{ $cert->certificate_hardcopy ? '1' : '0' }}">
                                <button type="submit"
                                        class="px-2 py-1 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: #059669;">Simpan</button>
                            </form>
                            @endif
                        </td>
                        @endcan
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Riwayat Perubahan Tutor --}}
    @if($class->tutorChangeHistories->isNotEmpty())
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center gap-2 px-5 py-4"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">swap_horiz</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Riwayat Perubahan Tutor</h2>
        </div>
        <table class="w-full table-fixed text-sm">
            <colgroup>
                <col class="w-[15%]"><col class="w-[28%]"><col class="w-[28%]"><col class="w-[29%]">
            </colgroup>
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    @foreach(['Tanggal','Dari','Ke','Alasan'] as $h)
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($class->tutorChangeHistories as $history)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor=''">
                        <td class="px-5 py-3 text-sm" style="color: var(--dash-text-muted);">{{ $history->changed_at?->format('d M Y') ?? '-' }}</td>
                        <td class="px-5 py-3 text-sm" style="color: var(--dash-text-body);">{{ $history->fromTutor?->full_name ?? '-' }}</td>
                        <td class="px-5 py-3 text-sm" style="color: var(--dash-text-body);">{{ $history->toTutor?->full_name ?? '-' }}</td>
                        <td class="px-5 py-3 text-sm" style="color: var(--dash-text-muted);">{{ $history->reason ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>

{{-- Confirm Modal --}}
<div id="confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background-color: rgba(0,0,0,0.5);">
    <div class="rounded-2xl shadow-xl max-w-sm w-full p-6"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-start gap-4 mb-5">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                 style="background-color: #fee2e2;">
                <span class="material-symbols-outlined text-[20px]" style="color: #dc2626;">warning</span>
            </div>
            <div>
                <h3 class="font-semibold text-base" id="confirm-title" style="color: var(--dash-text-heading);"></h3>
                <p class="text-sm mt-1" id="confirm-message" style="color: var(--dash-text-muted);"></p>
            </div>
        </div>
        <div class="flex gap-3 justify-end">
            <button onclick="closeConfirmModal()"
                    class="px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                    style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</button>
            <button onclick="submitConfirmAction()"
                    class="px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: #dc2626;">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

@endsection

@push('head-scripts')
    @vite('resources/js/classroom-show.js')
@endpush
