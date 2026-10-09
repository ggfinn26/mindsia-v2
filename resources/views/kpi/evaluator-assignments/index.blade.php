@extends('layouts.dashboard')

@section('title', 'Assignment Evaluator KPI - MINDSIA')
@section('header_title', 'Assignment Evaluator')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">KPI</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Assignment Evaluator</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Tentukan siapa yang mengevaluasi siapa dan dengan template apa.
            </p>
        </div>
        @can('kpi.evaluator_assignment.create')
            <a href="{{ route('kpi.evaluator-assignments.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>Tambah Assignment
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="rounded-2xl px-5 py-4"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Evaluator</label>
                <select name="evaluator_employee_id"
                        class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Semua</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(request('evaluator_employee_id') == $emp->id)>
                            {{ $emp->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Evaluatee</label>
                <select name="evaluatee_employee_id"
                        class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Semua</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(request('evaluatee_employee_id') == $emp->id)>
                            {{ $emp->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>Filter
                </button>
                @if(request()->hasAny(['evaluator_employee_id', 'evaluatee_employee_id']))
                    <a href="{{ route('kpi.evaluator-assignments.index') }}"
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
                    <col class="w-[20%]">
                    <col class="w-[22%]">
                    <col class="w-[15%]">
                    <col class="w-[12%]">
                    <col class="w-[11%]">
                </colgroup>
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Evaluator</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Evaluatee</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Template</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Berlaku Mulai</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4 font-medium truncate" style="color: var(--dash-text-heading);">
                                {{ $assignment->evaluator?->full_name ?? '—' }}
                            </td>
                            <td class="px-5 py-4 truncate" style="color: var(--dash-text-body);">
                                {{ $assignment->evaluatee?->full_name ?? '—' }}
                            </td>
                            <td class="px-5 py-4 truncate" style="color: var(--dash-text-muted);">
                                {{ $assignment->template?->template_name ?? '—' }}
                            </td>
                            <td class="px-5 py-4 text-sm" style="color: var(--dash-text-muted);">
                                {{ $assignment->effective_start_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-4">
                                @if($assignment->is_active)
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                @can('kpi.evaluator_assignment.update')
                                    <a href="{{ route('kpi.evaluator-assignments.edit', $assignment) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]"
                                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                    </a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">supervisor_account</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
                                    Belum ada assignment evaluator.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($assignments->hasPages())
            <div class="px-5 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $assignments->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
