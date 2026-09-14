@extends('layouts.dashboard')

@section('title', 'Detail Aturan Jadwal Kerja')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('work-schedule-rules.index') }}" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $rule->setting_name }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">Detail aturan jadwal kerja</p>
            </div>
        </div>
        @can('attendance.schedule_rule.manage')
            <div class="flex gap-2">
                <a href="{{ route('work-schedule-rules.edit', $rule) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">edit</span> Edit
                </a>
                <form action="{{ route('work-schedule-rules.destroy', $rule) }}" method="POST"
                      onsubmit="return confirm('Hapus aturan ini? Tidak bisa diundur jika sudah di-assign.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                        <span class="material-symbols-outlined text-[16px]">delete</span> Hapus
                    </button>
                </form>
            </div>
        @endcan
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    {{-- Detail Card --}}
    <div class="bg-white shadow rounded-lg divide-y divide-gray-100">
        <div class="px-6 py-4 grid grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Jam Mulai Kerja</p>
                <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($rule->start_time)->format('H:i') }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Jam Selesai Kerja</p>
                <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($rule->end_time)->format('H:i') }}</p>
            </div>
            @if ($rule->break_start_time)
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Jam Istirahat Mulai</p>
                    <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($rule->break_start_time)->format('H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Jam Istirahat Selesai</p>
                    <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($rule->break_end_time)->format('H:i') }}</p>
                </div>
            @endif
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Toleransi Keterlambatan</p>
                <p class="text-sm font-medium text-gray-900">{{ $rule->late_tolerance_minutes }} menit</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Toleransi Pulang Cepat</p>
                <p class="text-sm font-medium text-gray-900">{{ $rule->early_leave_tolerance_minutes }} menit</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Absensi Wajib</p>
                <p class="text-sm font-medium text-gray-900">{{ $rule->is_required ? 'Ya' : 'Tidak' }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1">Status</p>
                @if ($rule->is_active)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Assignments --}}
    <div>
        <h2 class="text-lg font-bold text-gray-900 mb-3">Assignment</h2>
        <div class="bg-white shadow rounded-lg">
            @if ($rule->assignments->isEmpty())
                <div class="text-center py-8 text-gray-400 text-sm">
                    <span class="material-symbols-outlined text-[36px] mb-1 block">person_off</span>
                    Belum ada assignment untuk aturan ini.
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($rule->assignments as $assignment)
                        <div class="px-5 py-3.5 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wide text-gray-400 mr-2">{{ $assignment->scope_type }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $assignment->scope_id }}</span>
                            </div>
                            @can('attendance.schedule_rule.manage')
                                <form action="{{ route('work-schedule-assignments.destroy', $assignment) }}" method="POST"
                                      onsubmit="return confirm('Hapus assignment ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                                </form>
                            @endcan
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
