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
            @endif
            @can('class.graduate')
                @if($class->status === 'active')
                    <button type="button" onclick="if(confirm('Luluskan semua member aktif di kelas ini?')) document.getElementById('graduate-form').submit();"
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
            @can('class.manage')
                <button type="button" onclick="document.getElementById('add-member-form').classList.toggle('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-sm font-medium">
                    + Daftarkan Member
                </button>
            @endcan
        </div>

        <!-- Add Member Form -->
        <div id="add-member-form" class="hidden border-b border-gray-200 bg-gray-50 px-6 py-4">
            <form action="{{ route('member-class.store', $class) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Member Registration ID</label>
                        <input type="number" name="member_registration_id" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="ID Registrasi Member" />
                        @error('member_registration_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" />
                        @error('start_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium w-full">Daftarkan</button>
                    </div>
                </div>
            </form>
        </div>

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
                                {{ $mc->registration?->memberData?->full_name ?? 'Member #' . $mc->member_registration_id }}
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
                                    <form action="{{ route('member-class.destroy', $mc) }}" method="POST" class="inline" onsubmit="return confirm('Hapus member dari kelas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
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
                                        <form action="{{ route('class-tests.destroy', [$class, $test]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus test ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
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
@endsection
