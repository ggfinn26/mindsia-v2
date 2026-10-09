@extends('layouts.dashboard')

@section('title', 'Kelola Kelas')
@section('header_title', 'Kelas')

@section('content')
@php
    $currentSort = request('sort', 'created_at');
    $currentDir = request('direction', 'desc');
    
    $sortUrl = function ($column) use ($currentSort, $currentDir) {
        $direction = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $direction]);
    };

    $sortIcon = function ($column) use ($currentSort, $currentDir) {
        if ($currentSort !== $column) return '';
        return '<span class="material-symbols-outlined text-[14px] ml-1">' . ($currentDir === 'asc' ? 'arrow_upward' : 'arrow_downward') . '</span>';
    };
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6" x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">
    
    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Kelas</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Kelola Kelas</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Daftar semua kelas. Anda dapat mengurutkan dengan mengklik judul kolom di tabel.
            </p>
        </div>
        <a href="{{ route('classrooms.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Buat Kelas Baru
        </a>
    </div>

    @if (session('success'))
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #a7f3d0; background-color: #ecfdf5; color: #065f46;">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #fecaca; background-color: #fef2f2; color: #991b1b;">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Filters --}}
    <div class="rounded-2xl p-4" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" action="{{ route('classrooms.index') }}" class="flex flex-wrap items-end gap-4">
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="direction" value="{{ request('direction') }}">
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Cabang</label>
                <select name="branch_id" onchange="this.form.submit()" class="rounded-lg px-3 py-2 text-sm focus:outline-none w-40"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Program</label>
                <select name="program_id" onchange="this.form.submit()" class="rounded-lg px-3 py-2 text-sm focus:outline-none w-40"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                    <option value="">Semua Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                            {{ $program->program_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Status</label>
                <select name="status" onchange="this.form.submit()" class="rounded-lg px-3 py-2 text-sm focus:outline-none w-40"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if(request()->hasAny(['branch_id', 'program_id', 'status', 'sort', 'direction']))
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('classrooms.index') }}"
                       class="inline-flex items-center gap-1 text-xs font-medium transition-opacity hover:opacity-75"
                       style="color: var(--dash-text-muted);">
                        <span class="material-symbols-outlined text-[14px]">close</span>Reset
                    </a>
                </div>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                            <a href="{{ $sortUrl('class_name') }}" class="inline-flex items-center hover:opacity-75 transition-opacity">Nama Kelas {!! $sortIcon('class_name') !!}</a>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                            <a href="{{ $sortUrl('program_id') }}" class="inline-flex items-center hover:opacity-75 transition-opacity">Program {!! $sortIcon('program_id') !!}</a>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                            <a href="{{ $sortUrl('branch_id') }}" class="inline-flex items-center hover:opacity-75 transition-opacity">Cabang {!! $sortIcon('branch_id') !!}</a>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                            <a href="{{ $sortUrl('tutor_id') }}" class="inline-flex items-center hover:opacity-75 transition-opacity">Tutor {!! $sortIcon('tutor_id') !!}</a>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Jadwal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Member</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                            <a href="{{ $sortUrl('status') }}" class="inline-flex items-center hover:opacity-75 transition-opacity">Status {!! $sortIcon('status') !!}</a>
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $class->class_name }}</p>
                                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Mulai: {{ $class->start_date?->format('d M Y') ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">{{ $class->program?->program_name ?? '-' }}</td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">{{ $class->branch?->branch_name ?? '-' }}</td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">{{ $class->tutor?->full_name ?? '-' }}</td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">{{ $class->day_of_week }}{{ $class->day_of_week2 ? ' & ' . $class->day_of_week2 : '' }}</td>
                            <td class="px-5 py-4 text-center tabular-nums" style="color: var(--dash-text-body);">{{ $class->memberClasses->where('status', 'active')->count() }}</td>
                            <td class="px-5 py-4">
                                @php
                                    $statusColors = [
                                        'planned' => 'background-color: #fef3c7; color: #92400e;', // amber
                                        'active' => 'background-color: #f0fdf4; color: #059669;', // emerald
                                        'completed' => 'background-color: #eff6ff; color: #1d4ed8;', // blue
                                        'cancelled' => 'background-color: #fef2f2; color: #991b1b;', // red
                                    ];
                                    $statusLabels = [
                                        'planned' => 'Direncanakan',
                                        'active' => 'Aktif',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold" style="{{ $statusColors[$class->status] ?? 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);' }}">
                                    {{ $statusLabels[$class->status] ?? $class->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('classrooms.show', $class) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>Detail
                                    </a>
                                    @if($class->status === 'planned')
                                        <a href="{{ route('classrooms.edit', $class) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]"
                                           style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                        </a>
                                        <button type="button"
                                            @click="showConfirmModal = true; confirmTitle = 'Hapus Kelas'; confirmMessage = 'Kelas yang sudah memiliki member tidak bisa dihapus. Lanjutkan?'; confirmActionUrl = '{{ route('classrooms.destroy', $class) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                            style="border: 1px solid var(--dash-card-border); color: #dc2626; background-color: var(--dash-card-bg);"
                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">school</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada kelas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($classes->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $classes->withQueryString()->links() }}
            </div>
        @endif
    </div>

    {{-- Confirm Modal --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>
            <div class="relative z-10 inline-block align-bottom bg-[var(--dash-card-bg)] rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-[var(--dash-card-bg)] px-4 pt-5 pb-4 sm:p-6 sm:pb-4 rounded-t-lg">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-[var(--dash-text-heading)]" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-[var(--dash-text-muted)]" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-[var(--dash-page-bg)] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                    <button type="button" @click="$refs.confirmForm.submit()" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-[#dc2626] hover:opacity-90 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm" x-text="confirmActionText"></button>
                    <button type="button" @click="showConfirmModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-[var(--dash-card-border)] shadow-sm px-4 py-2 bg-[var(--dash-card-bg)] text-base font-medium text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] focus:outline-none sm:mt-0 sm:w-auto sm:text-sm gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
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
