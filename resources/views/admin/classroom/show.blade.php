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
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($class->schedules->sortBy('schedule_date') as $schedule)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $schedule->schedule_date?->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $schedule->schedule_date?->locale('id')->isoFormat('dddd') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $schedule->start_time }} — {{ $schedule->end_time }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $schedule->material_taught ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Test</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($class->tests as $test)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $test->test_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $test->test_type === 'pre_test' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $test->test_type === 'pre_test' ? 'Pre-Test' : 'Post-Test' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $test->date?->format('d M Y') ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @can('class.test.update')
                                        <a href="{{ route('class-tests.edit', [$class, $test]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    @endcan
                                    @can('class.test.delete')
                                        <form id="del-test-{{ $test->id }}" action="{{ route('class-tests.destroy', [$class, $test]) }}" method="POST" class="hidden">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button"
                                                onclick="openConfirmModal('Hapus Test', 'Hapus test {{ addslashes($test->test_name) }}? Semua nilai yang sudah diinput akan ikut terhapus.', () => document.getElementById('del-test-{{ $test->id }}').submit())"
                                                class="text-red-600 hover:text-red-900">Hapus</button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

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
