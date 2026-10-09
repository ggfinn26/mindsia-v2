@extends('layouts.dashboard')

@section('title', 'Evaluasi KPI')
@section('header_title', 'Evaluasi KPI')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">KPI</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Evaluasi KPI</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Buat dan kelola evaluasi KPI pegawai.
            </p>
        </div>
        @can('kpi.evaluation.create')
            <a href="{{ route('kpi.evaluations.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Evaluasi
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #a7f3d0; background-color: #ecfdf5; color: #065f46;">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Filters --}}
    <div class="rounded-2xl p-4"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Pegawai</label>
                <select name="employee_id" class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                    <option value="">Semua</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Template</label>
                <select name="kpi_template_id" class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                    <option value="">Semua</option>
                    @foreach ($templates as $t)
                        <option value="{{ $t->id }}" @selected(request('kpi_template_id') == $t->id)>{{ $t->template_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Status</label>
                <select name="status" class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                    <option value="">Semua</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="finalized" @selected(request('status') === 'finalized')>Finalized</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>Filter
                </button>
                @if(request()->hasAny(['employee_id', 'kpi_template_id', 'status']))
                    <a href="{{ route('kpi.evaluations.index') }}"
                       class="inline-flex items-center gap-1 text-xs font-medium transition-opacity hover:opacity-75"
                       style="color: var(--dash-text-muted);">
                        <span class="material-symbols-outlined text-[14px]">close</span>Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[20%]">
                    <col class="w-[15%]">
                    <col class="w-[15%]">
                    <col class="w-[15%]">
                    <col class="w-[10%]">
                    <col class="w-[8%]">
                    <col class="w-[7%]">
                    <col class="w-[10%]">
                </colgroup>
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Template</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Periode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Evaluator</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Skor</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Grade</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($evaluations as $eval)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4">
                                <p class="font-semibold truncate" style="color: var(--dash-text-heading);">{{ $eval->employee_name_snapshot }}</p>
                                <p class="text-xs truncate" style="color: var(--dash-text-muted);">{{ $eval->position_name_snapshot }}</p>
                            </td>
                            <td class="px-5 py-4 truncate" style="color: var(--dash-text-body);">{{ $eval->template?->template_name ?? '—' }}</td>
                            <td class="px-5 py-4 truncate" style="color: var(--dash-text-body);">
                                {{ $eval->period_start_date?->format('d M Y') }}<br>
                                <span class="text-xs" style="color: var(--dash-text-muted);">s.d. {{ $eval->period_end_date?->format('d M Y') }}</span>
                            </td>
                            <td class="px-5 py-4 truncate" style="color: var(--dash-text-body);">{{ $eval->evaluator?->full_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-semibold" style="color: var(--dash-text-heading);">
                                {{ $eval->total_score !== null ? number_format($eval->total_score, 1) : '—' }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($eval->grade)
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold"
                                          style="background-color: var(--dash-primary); color: white;">{{ $eval->grade }}</span>
                                @else
                                    <span style="color: var(--dash-text-faint);">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($eval->isDraft())
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: #fef3c7; color: #92400e;">Draft</span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: #f0fdf4; color: #059669;">Final</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('kpi.evaluations.show', $eval) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">assessment</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada evaluasi KPI.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($evaluations->hasPages())
            <div class="px-5 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $evaluations->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
