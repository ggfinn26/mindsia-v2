@extends('layouts.dashboard')

@section('title', $class->class_name)

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $class->class_name }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $class->program?->program_name }} &middot; {{ $class->branch?->branch_name }}</p>
        </div>
        <div class="flex gap-3">
            @can('class.manage')
            @if($class->status === 'planned')
                <a href="{{ route('classrooms.edit', $class) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Edit</a>
                <button type="button"
                        onclick="openConfirmModal('Aktifkan Kelas', 'Aktifkan kelas ini? Status akan berubah menjadi Aktif.', () => document.getElementById('activate-form').submit())"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium">Aktifkan</button>
                <form id="activate-form" action="{{ route('classrooms.activate', $class) }}" method="POST" class="hidden">
                    @csrf @method('PATCH')
                </form>
            @endif
            @if(in_array($class->status, ['planned', 'active']))
                <button type="button"
                        onclick="openConfirmModal('Batalkan Kelas', 'Batalkan kelas ini? Status akan berubah menjadi Dibatalkan dan tidak bisa diubah lagi.', () => document.getElementById('cancel-form').submit())"
                        class="bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg font-medium">Batalkan Kelas</button>
                <form id="cancel-form" action="{{ route('classrooms.cancel', $class) }}" method="POST" class="hidden">
                    @csrf @method('PATCH')
                </form>
            @endif
            @endcan
            @can('class.graduate')
                @if($class->status === 'active')
                    <button type="button"
                            onclick="openConfirmModal('Luluskan Semua Member', 'Luluskan semua member aktif di kelas ini? Tindakan ini tidak bisa dibatalkan.', () => document.getElementById('graduate-form').submit())"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">Luluskan Semua</button>
                    <form id="graduate-form" action="{{ route('classrooms.graduate', $class) }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @endif
            @endcan
            <a href="{{ route('classrooms.index') }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">Kembali</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <!-- Class Info -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5">
            <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tutor</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $class->tutor?->full_name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Jadwal</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $class->day_of_week }}{{ $class->day_of_week2 ? ' & ' . $class->day_of_week2 : '' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Periode</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $class->start_date?->format('d M Y') ?? '-' }} — {{ $class->end_date?->format('d M Y') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        @php
                            $statusColors = [
                                'planned' => 'bg-yellow-100 text-yellow-800',
                                'active' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                            $statusLabels = [
                                'planned' => 'Direncanakan',
                                'active' => 'Aktif',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$class->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$class->status] ?? $class->status }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Jam Hari Utama</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $class->start_time_primary }} — {{ $class->end_time_primary }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Jam Hari Kedua</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $class->start_time_secondary && $class->end_time_secondary ? $class->start_time_secondary . ' — ' . $class->end_time_secondary : '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Jumlah Minggu</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $class->week_count }} minggu</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Member Aktif</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $class->memberClasses->where('status', 'active')->count() }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Member Section -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">👥 Member Kelas</h2>
            @can('class.member.assign')
                <button type="button" onclick="document.getElementById('add-member-form').classList.toggle('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm font-medium">
                    + Daftarkan Member
                </button>
            @endcan
        </div>

        <!-- Add Member Form -->
        @can('class.member.assign')
        <div id="add-member-form" class="hidden border-b border-gray-200 bg-gray-50 px-6 py-4"
             x-data="memberDropdown({{ Illuminate\Support\Js::from($memberOptions) }})">
            <form action="{{ route('member-class.store', $class) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Member</label>
                        <div class="relative" @click.away="open = false">
                            <div @click="open = !open"
                                 class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-[13px] shadow-sm flex justify-between items-center cursor-pointer"
                                 :class="open ? 'ring-2 ring-blue-500 border-blue-500' : ''">
                                <span x-text="selectedId ? selectedLabel : 'Pilih member...'"
                                      :class="selectedId ? 'text-gray-900' : 'text-gray-400'"
                                      class="truncate block pr-2"></span>
                                <span class="material-symbols-outlined text-[16px] text-gray-400 flex-shrink-0">expand_more</span>
                            </div>
                            <div x-show="open" x-transition x-cloak
                                 class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                <div class="p-2 bg-gray-50 border-b border-gray-100">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-[16px]">search</span>
                                        <input type="text" x-model="search" placeholder="Cari nama member..."
                                               style="padding-left: 2.5rem !important" class="w-full pr-3 py-2 bg-white border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all"
                                               @click.stop>
                                    </div>
                                </div>
                                <ul class="max-h-52 overflow-y-auto py-1">
                                    <li x-show="filtered.length === 0" class="px-4 py-3 text-[12px] text-gray-500 text-center">Tidak ada member tersedia</li>
                                    <template x-for="m in filtered" :key="m.id">
                                        <li @click="select(m)"
                                            class="px-4 py-2 text-[12px] text-gray-700 hover:bg-blue-500 hover:text-white cursor-pointer transition-colors"
                                            :class="selectedId == m.id ? 'bg-blue-50 font-semibold' : ''"
                                            x-text="m.label"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" name="member_registration_id" x-model="selectedId" />
                        @error('member_registration_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-400">{{ $availableRegistrations->count() }} member tersedia</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" required
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-[13px] shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" />
                        @error('start_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <button type="submit"
                                :disabled="!selectedId"
                                :class="selectedId ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-300 cursor-not-allowed'"
                                class="text-white px-6 py-2.5 rounded-xl text-[13px] font-medium w-full transition-colors">
                            Daftarkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endcan

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($class->memberClasses as $mc)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $mc->memberRegistration?->memberData?->full_name ?? 'Member #' . $mc->member_registration_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    $mcStatusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'inactive' => 'bg-gray-100 text-gray-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $mcStatusColors[$mc->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($mc->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $mc->start_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @can('class.manage')
                                @if($mc->status === 'active')
                                    <form id="deact-mc-{{ $mc->id }}" action="{{ route('member-class.deactivate', $mc) }}" method="POST" class="hidden">
                                        @csrf @method('PATCH')
                                    </form>
                                    <button type="button"
                                            onclick="openConfirmModal('Nonaktifkan Member', 'Nonaktifkan member ini dari kelas? Data kehadiran tetap tersimpan.', () => document.getElementById('deact-mc-{{ $mc->id }}').submit())"
                                            class="text-yellow-600 hover:text-yellow-900 mr-3">Nonaktifkan</button>
                                    <form id="rm-mc-{{ $mc->id }}" action="{{ route('member-class.destroy', $mc) }}" method="POST" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button"
                                            onclick="openConfirmModal('Hapus Member dari Kelas', 'Hapus permanen member ini dari kelas? Progress kurikulum juga akan dihapus.', () => document.getElementById('rm-mc-{{ $mc->id }}').submit())"
                                            class="text-red-600 hover:text-red-900">Hapus</button>
                                @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada member di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Session Schedule Section -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">📅 Sesi & Absensi</h2>
            <span class="text-sm text-gray-500">{{ $class->schedules->count() }} sesi</span>
        </div>

        @if($class->schedules->isEmpty())
            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                Jadwal sesi akan otomatis dibuat saat kelas berstatus aktif.
            </div>
        @else
            @php $activeMembers = $class->memberClasses->where('status', 'active'); @endphp
            <div class="divide-y divide-gray-200">
                @foreach($class->schedules->sortBy('schedule_date') as $schedule)
                    @php $isPast = $schedule->schedule_date <= today(); @endphp
                    <div x-data="{ open: false }">
                        {{-- Row header --}}
                        <div class="px-6 py-4 flex items-center gap-4 cursor-pointer hover:bg-gray-50"
                             @click="open = !open">
                            <span class="text-sm font-medium text-gray-900 w-28">{{ $schedule->schedule_date?->format('d M Y') }}</span>
                            <span class="text-sm text-gray-500 w-16">{{ $schedule->schedule_date?->locale('id')->isoFormat('dddd') }}</span>
                            <span class="text-sm text-gray-500 w-36">{{ $schedule->start_time }} — {{ $schedule->end_time }}</span>
                            <span class="text-sm text-gray-500 flex-1 truncate">
                                {{ $schedule->material_taught ?? ($isPast ? '— belum diisi' : '— sesi mendatang') }}
                            </span>
                            @if(!$isPast)
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">Mendatang</span>
                            @elseif($schedule->schedule_date->isToday())
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">Hari ini</span>
                            @endif
                            <span class="material-symbols-outlined text-gray-400 text-[18px]" x-text="open ? 'expand_less' : 'expand_more'"></span>
                        </div>

                        {{-- Expanded detail --}}
                        <div x-show="open" x-cloak class="bg-gray-50 border-t border-gray-100 px-6 py-5 space-y-5">

                            {{-- Material form --}}
                            @can('class.session.update')
                            @if($isPast)
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Materi Sesi</h4>
                                <form action="{{ route('class-schedules.material', $schedule) }}" method="POST" class="flex gap-2">
                                    @csrf @method('PATCH')
                                    <input type="text" name="material_taught"
                                           value="{{ old('material_taught', $schedule->material_taught) }}"
                                           placeholder="Tulis materi yang diajarkan..."
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Simpan</button>
                                </form>
                            </div>
                            @else
                            <p class="text-xs text-gray-400">Materi hanya bisa diisi pada atau setelah tanggal sesi.</p>
                            @endif
                            @endcan

                            {{-- Attendance + Score --}}
                            @can('class.attendance.record')
                            @if($isPast && $activeMembers->isNotEmpty())
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Absensi & Nilai</h4>
                                <form action="{{ route('member-attendance.store', $schedule) }}" method="POST">
                                    @csrf
                                    <table class="min-w-full text-sm">
                                        <thead>
                                            <tr class="text-xs text-gray-500 uppercase">
                                                <th class="text-left pb-2 pr-4 font-medium">Member</th>
                                                <th class="text-left pb-2 pr-4 font-medium w-36">Status</th>
                                                <th class="text-left pb-2 pr-4 font-medium">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($activeMembers as $mc)
                                            <tr>
                                                <td class="py-2 pr-4 font-medium text-gray-800">{{ $mc->memberRegistration?->memberData?->full_name ?? 'Member #'.$mc->id }}</td>
                                                <td class="py-2 pr-4">
                                                    <input type="hidden" name="attendances[{{ $loop->index }}][member_class_id]" value="{{ $mc->id }}">
                                                    <select name="attendances[{{ $loop->index }}][status]"
                                                            class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500">
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
                                                           class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <button type="submit" class="mt-3 px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">Simpan Absensi</button>
                                </form>
                            </div>
                            @endif
                            @endcan

                            {{-- Assessment --}}
                            @can('class.assessment.record')
                            @if($isPast && $activeMembers->isNotEmpty())
                            <div>
                                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Nilai Sesi</h4>
                                <div class="space-y-2">
                                    @foreach($activeMembers as $mc)
                                    <form action="{{ route('member-assessments.store', $schedule) }}" method="POST" class="flex items-center gap-3">
                                        @csrf
                                        <input type="hidden" name="member_class_id" value="{{ $mc->id }}">
                                        <span class="text-sm text-gray-700 w-40">{{ $mc->memberRegistration?->memberData?->full_name ?? 'Member #'.$mc->id }}</span>
                                        <input type="number" name="score" min="0" max="100" placeholder="0-100"
                                               class="w-20 px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500">
                                        <input type="text" name="notes" placeholder="Catatan"
                                               class="flex-1 px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500">
                                        <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">Simpan</button>
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

    <!-- Test Section -->
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">📝 Test & Sertifikat</h2>
            @can('class.test.create')
                <a href="{{ route('class-tests.create', $class) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm font-medium">
                    + Tambah Test
                </a>
            @endcan
        </div>

        @if($class->tests->isEmpty())
            <div class="px-6 py-8 text-center text-gray-500 text-sm">
                Belum ada test untuk kelas ini.
            </div>
        @else
            @php $activeMembers = $class->memberClasses->where('status', 'active'); @endphp
            <div class="divide-y divide-gray-200">
                @foreach($class->tests as $test)
                    <div x-data="{ open: false }">
                        <div class="px-6 py-4 flex items-center gap-4 cursor-pointer hover:bg-gray-50" @click="open = !open">
                            <span class="text-sm font-medium text-gray-900 flex-1">{{ $test->test_name }}</span>
                            <span class="px-2 text-xs leading-5 font-semibold rounded-full {{ $test->test_type === 'pre_test' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ $test->test_type === 'pre_test' ? 'Pre-Test' : 'Post-Test' }}
                            </span>
                            <span class="text-sm text-gray-500">{{ $test->date?->format('d M Y') ?? '-' }}</span>
                            @can('class.test.update')
                                <a href="{{ route('class-tests.edit', [$class, $test]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm" @click.stop>Edit</a>
                            @endcan
                            @can('class.test.delete')
                                <form id="del-test-{{ $test->id }}" action="{{ route('class-tests.destroy', [$class, $test]) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                                <button type="button"
                                        onclick="openConfirmModal('Hapus Test', 'Hapus test {{ addslashes($test->test_name) }}? Semua nilai yang sudah diinput akan ikut terhapus.', () => document.getElementById('del-test-{{ $test->id }}').submit())"
                                        class="text-red-600 hover:text-red-900 text-sm" @click.stop>Hapus</button>
                            @endcan
                            <span class="material-symbols-outlined text-gray-400 text-[18px]" x-text="open ? 'expand_less' : 'expand_more'"></span>
                        </div>

                        {{-- Score input per member --}}
                        @can('class.test.score')
                        <div x-show="open" x-cloak class="bg-gray-50 border-t border-gray-100 px-6 py-4">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase mb-3">Input Nilai Per Member</h4>
                            @php
                                $resultsByMcId = $test->memberTestResults->keyBy('member_class_id');
                            @endphp
                            <div class="space-y-3">
                                @foreach($class->memberClasses as $mc)
                                    @php $existing = $resultsByMcId->get($mc->id); @endphp
                                    <form action="{{ route('member-test-results.store', [$mc, $test]) }}" method="POST"
                                          class="flex items-center gap-3">
                                        @csrf
                                        <span class="text-sm text-gray-700 w-40">{{ $mc->memberRegistration?->memberData?->full_name ?? 'Member #'.$mc->id }}</span>
                                        <input type="text" name="level" value="{{ old('level', $existing?->level) }}"
                                               placeholder="Level (A/B/C/Lulus)"
                                               class="w-32 px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500">
                                        <input type="number" name="final_score" value="{{ old('final_score', $existing?->final_score) }}"
                                               min="0" placeholder="Nilai"
                                               class="w-24 px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-blue-500">
                                        <button type="submit" class="px-3 py-1.5 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                                            {{ $existing ? 'Update' : 'Simpan' }}
                                        </button>
                                        @if($existing)
                                            <span class="text-xs text-gray-400">Tersimpan: Level {{ $existing->level }}, Nilai {{ $existing->final_score }}</span>
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

    <!-- Sertifikat -->
    @php $graduatedMembers = $class->memberClasses->filter(fn($mc) => $mc->memberRegistration?->graduation_status === 'LULUS'); @endphp
    @if($graduatedMembers->isNotEmpty())
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">🎓 Sertifikat Member Lulus</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Member</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Sertifikat</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tersedia</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Hardcopy</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sudah Diambil</th>
                        @can('class.certificate.update') <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th> @endcan
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($graduatedMembers as $mc)
                        @php $cert = $mc->memberRegistration?->certificate; @endphp
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $mc->memberRegistration?->memberData?->full_name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $cert?->certificate_number ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($cert?->certificate_available === 'available')
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">Tersedia</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">Belum</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-500">
                                {{ $cert?->certificate_hardcopy ? 'Ya' : 'Tidak' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($cert?->certificate_taken === 'taken')
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">Sudah Diambil</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">Belum Diambil</span>
                                @endif
                            </td>
                            @can('class.certificate.update')
                            <td class="px-4 py-3 text-right">
                                @if($cert)
                                <form action="{{ route('member-certificates.update', $cert) }}" method="POST"
                                      class="flex items-center justify-end gap-2">
                                    @csrf @method('PATCH')
                                    <input type="text" name="certificate_number"
                                           value="{{ $cert->certificate_number }}"
                                           placeholder="No. Sertifikat"
                                           class="w-28 px-2 py-1 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-blue-500">
                                    <select name="certificate_available" class="px-2 py-1 border border-gray-300 rounded text-xs">
                                        <option value="not_available" @selected($cert->certificate_available === 'not_available')>Belum Tersedia</option>
                                        <option value="available" @selected($cert->certificate_available === 'available')>Tersedia</option>
                                    </select>
                                    <select name="certificate_taken" class="px-2 py-1 border border-gray-300 rounded text-xs">
                                        <option value="not_taken" @selected($cert->certificate_taken === 'not_taken')>Belum Diambil</option>
                                        <option value="taken" @selected($cert->certificate_taken === 'taken')>Sudah Diambil</option>
                                    </select>
                                    <input type="hidden" name="certificate_hardcopy" value="{{ $cert->certificate_hardcopy ? '1' : '0' }}">
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">Simpan</button>
                                </form>
                                @endif
                            </td>
                            @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Tutor Change History -->
    @if($class->tutorChangeHistories->isNotEmpty())
    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900">🔄 Riwayat Perubahan Tutor</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dari</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ke</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($class->tutorChangeHistories as $history)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $history->changed_at?->format('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $history->fromTutor?->full_name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $history->toTutor?->full_name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $history->reason ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
<!-- Confirm Modal -->
<div id="confirm-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-sm w-full mx-4 p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-red-600 text-[20px]">warning</span>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 text-base" id="confirm-title"></h3>
                <p class="text-sm text-gray-500 mt-1" id="confirm-message"></p>
            </div>
        </div>
        <div class="flex gap-3 mt-5 justify-end">
            <button onclick="closeConfirmModal()" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
            <button onclick="submitConfirmAction()" class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

@endsection

@push('head-scripts')
    @vite('resources/js/classroom-show.js')
@endpush
