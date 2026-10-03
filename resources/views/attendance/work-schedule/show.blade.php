@extends('layouts.dashboard')

@section('title', 'Detail Aturan Jadwal Kerja')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">
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
                <button type="button"
                        @click="showConfirmModal = true; confirmTitle = 'Hapus Aturan Jadwal'; confirmMessage = 'Hapus aturan ini? Tidak bisa diundur jika sudah di-assign.'; confirmActionUrl = '{{ route('work-schedule-rules.destroy', $rule) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">delete</span> Hapus
                </button>
            </div>
        @endcan
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc pl-4 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
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

        {{-- Add assignment form --}}
        @can('attendance.schedule_rule.manage')
        <div class="bg-white shadow rounded-lg p-5 mb-4" x-data="workScheduleAssign()">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Tambah Assignment Baru</h3>
            <form action="{{ route('work-schedule-assignments.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="work_schedule_rule_id" value="{{ $rule->id }}">
                <input type="hidden" name="assignable_type" :value="type">

                {{-- Step 1: Pilih scope --}}
                <div class="max-w-xs" @click.outside="open = false">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Assign ke <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div @click="open = !open"
                             class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-[13px] shadow-sm flex justify-between items-center cursor-pointer transition-all min-h-[44px]"
                             :class="open ? 'border-[#5586DB] ring-2 ring-[#5586DB]/20' : 'border-[#E5E7EB]'">
                            <span class="truncate block pr-4"
                                  :class="type ? 'text-[#111827]' : 'text-[#9CA3AF]'"
                                  x-text="typeLabel || 'Pilih scope...'"></span>
                            <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform duration-200"
                                  :class="open ? 'rotate-180' : ''">expand_more</span>
                        </div>
                        <div x-show="open" x-transition
                             class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                            <ul class="py-1">
                                <template x-for="opt in options" :key="opt.value">
                                    <li @click="pick(opt.value, opt.label)"
                                        class="px-4 py-2.5 text-[13px] text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors"
                                        :class="type === opt.value ? 'bg-[#EFF6FF] font-semibold' : ''"
                                        x-text="opt.label"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Checkbox list muncul setelah scope dipilih --}}

                <template x-if="type === 'role'">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200">
                            <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Role</span>
                            <button type="button"
                                @click="$el.closest('[x-data]').querySelectorAll('input[name=\'assignable_id[]\']').forEach(i => i.checked = true)"
                                class="text-xs text-indigo-600 hover:underline font-medium">Pilih Semua</button>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-52 overflow-y-auto">
                            @foreach($roles as $role)
                            <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 cursor-pointer">
                                <input type="checkbox" name="assignable_id[]" value="{{ $role->id }}"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm text-gray-800">{{ $role->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </template>

                <template x-if="type === 'position'">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200">
                            <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Posisi</span>
                            <button type="button"
                                @click="$el.closest('[x-data]').querySelectorAll('input[name=\'assignable_id[]\']').forEach(i => i.checked = true)"
                                class="text-xs text-indigo-600 hover:underline font-medium">Pilih Semua</button>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-52 overflow-y-auto">
                            @foreach($positions as $position)
                            <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 cursor-pointer">
                                <input type="checkbox" name="assignable_id[]" value="{{ $position->id }}"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm text-gray-800">{{ $position->position_name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </template>

                <template x-if="type === 'employee'">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2 bg-gray-50 border-b border-gray-200">
                            <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Pegawai</span>
                            <button type="button"
                                @click="$el.closest('[x-data]').querySelectorAll('input[name=\'assignable_id[]\']').forEach(i => i.checked = true)"
                                class="text-xs text-indigo-600 hover:underline font-medium">Pilih Semua</button>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-52 overflow-y-auto">
                            @foreach($employees as $employee)
                            <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 cursor-pointer">
                                <input type="checkbox" name="assignable_id[]" value="{{ $employee->id }}"
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm text-gray-800">{{ $employee->full_name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </template>

                {{-- Tanggal + aktif --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Berlaku Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="effective_start_date" required value="{{ now()->toDateString() }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Berlaku Sampai <span class="text-gray-400">(kosong = selamanya)</span></label>
                        <input type="date" name="effective_end_date"
                            class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <label for="is_active" class="text-sm text-gray-700">Aktif sekarang</label>
                </div>

                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Assign
                </button>
            </form>
        </div>
        @endcan

        {{-- Assignment list --}}
        <div class="bg-white shadow rounded-lg">
            @if ($rule->assignments->isEmpty())
                <div class="text-center py-8 text-gray-400 text-sm">
                    <span class="material-symbols-outlined text-[36px] mb-1 block">person_off</span>
                    Belum ada assignment untuk aturan ini.
                </div>
            @else
                @php
                    $typeLabels = [
                        \App\Models\Employee::class => 'Pegawai',
                        \App\Models\Position::class => 'Posisi',
                        \Spatie\Permission\Models\Role::class => 'Role',
                    ];
                @endphp
                <div class="divide-y divide-gray-100">
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
                        <div class="px-5 py-3.5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">{{ $typeLabel }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $entityName ?? '-' }}</span>
                                <span class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($assignment->effective_start_date)->format('d M Y') }}
                                    @if($assignment->effective_end_date)
                                        – {{ \Carbon\Carbon::parse($assignment->effective_end_date)->format('d M Y') }}
                                    @else
                                        – selamanya
                                    @endif
                                </span>
                                @if(!$assignment->is_active)
                                    <span class="text-xs text-gray-400">(nonaktif)</span>
                                @endif
                            </div>
                            @can('attendance.schedule_rule.manage')
                                <button type="button"
                                        @click="showConfirmModal = true; confirmTitle = 'Hapus Assignment'; confirmMessage = 'Hapus assignment ini?'; confirmActionUrl = '{{ route('work-schedule-assignments.destroy', $assignment) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                        class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                            @endcan
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Confirm Modal --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="$refs.confirmForm.submit()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 hover:bg-red-700 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
                            x-text="confirmActionText"></button>
                    <button type="button" @click="showConfirmModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
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
