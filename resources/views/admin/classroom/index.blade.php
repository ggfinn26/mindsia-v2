@extends('layouts.dashboard')

@section('title', 'Semua Kelas - MINDSIA')
@section('header_title', 'Semua Kelas')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Akademik</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
               style="color: var(--dash-text-heading);">Semua Kelas</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola daftar kelas, jadwal belajar, tutor, dan status kelas.
            </p>
        </div>
        <div>
            <a href="{{ route('classrooms.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Kelas Baru
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">warning</span>
            <p>{{ $errors->first() }}</p>
        </div>
    @endif

    {{-- Filter Card --}}
    <form method="GET" action="{{ route('classrooms.index') }}"
          class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex-1 min-w-[200px]">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Cabang</label>
            <select name="branch_id" onchange="this.form.submit()"
                    class="w-full px-3.5 py-2.5 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Cabang</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->branch_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Program</label>
            <select name="program_id" onchange="this.form.submit()"
                    class="w-full px-3.5 py-2.5 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Program</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                        {{ $program->program_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[180px]">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Status</label>
            <select name="status" onchange="this.form.submit()"
                    class="w-full px-3.5 py-2.5 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Status</option>
                @foreach($statuses as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        @if(request()->hasAny(['branch_id', 'program_id', 'status']))
            <div>
                <a href="{{ route('classrooms.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">refresh</span>
                    Reset
                </a>
            </div>
        @endif
    </form>

    {{-- Data Table --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Kelas & Program</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Tutor</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Jadwal & Mulai</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                            style="color: var(--dash-text-muted);">Member</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($classes as $class)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3 font-medium">
                                <a href="{{ route('classrooms.show', $class) }}"
                                   class="font-semibold block hover:underline transition-colors"
                                   style="color: var(--dash-text-heading);">
                                    {{ $class->class_name }}
                                </a>
                                <p class="text-xs mt-0.5 flex items-center gap-1.5" style="color: var(--dash-text-muted);">
                                    <span>{{ $class->program?->program_name ?? '-' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $class->branch?->branch_name ?? '-' }}</span>
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs" style="color: var(--dash-text-body);">
                                <span class="font-medium">{{ $class->tutor?->full_name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-xs whitespace-nowrap">
                                <p class="font-medium" style="color: var(--dash-text-body);">
                                    {{ $class->day_of_week }}{{ $class->day_of_week2 ? ' & ' . $class->day_of_week2 : '' }}
                                </p>
                                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">
                                    Mulai: {{ $class->start_date?->format('d M Y') ?? '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs whitespace-nowrap text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full font-medium"
                                      style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                    <span class="material-symbols-outlined text-[14px]" style="color: var(--dash-text-muted);">group</span>
                                    {{ $class->memberClasses->where('status', 'active')->count() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs whitespace-nowrap">
                                @php
                                    $statusStyles = [
                                        'planned' => 'background-color: #fef9c3; color: #854d0e; border: 1px solid #fef08a;',
                                        'active' => 'background-color: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;',
                                        'completed' => 'background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;',
                                        'cancelled' => 'background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca;',
                                    ];
                                    $statusLabels = [
                                        'planned' => 'Direncanakan',
                                        'active' => 'Aktif',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-md"
                                      style="{{ $statusStyles[$class->status] ?? 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);' }}">
                                    {{ $statusLabels[$class->status] ?? $class->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <a href="{{ route('classrooms.show', $class) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                                        Detail
                                    </a>
                                    @if($class->status === 'planned')
                                        <a href="{{ route('classrooms.edit', $class) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[14px]">edit</span>
                                            Edit
                                        </a>
                                        <button type="button"
                                                @click="showConfirmModal = true; confirmTitle = 'Hapus Kelas'; confirmMessage = 'Kelas yang sudah memiliki member tidak bisa dihapus. Lanjutkan?'; confirmActionUrl = '{{ route('classrooms.destroy', $class) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                                style="background-color: #dc2626;">
                                            <span class="material-symbols-outlined text-[14px]">delete</span>
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">school</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada kelas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($classes->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $classes->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Konfirmasi --}}
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
