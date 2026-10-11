@extends('layouts.dashboard')

@section('title', 'Assignment Evaluator KPI')
@section('header_title', 'Assignment Evaluator')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">KPI</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Assignment Evaluator</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Tentukan siapa yang mengevaluasi siapa dan dengan template apa.</p>
        </div>
        @can('kpi.evaluator_assignment.create')
            <a href="{{ route('kpi.evaluator-assignments.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>Tambah Assignment
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    <form method="GET"
          class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Evaluator</label>
            <select name="evaluator_employee_id"
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(request('evaluator_employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Evaluatee</label>
            <select name="evaluatee_employee_id"
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(request('evaluatee_employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>Filter
            </button>
            @if (request()->hasAny(['evaluator_employee_id', 'evaluatee_employee_id']))
                <a href="{{ route('kpi.evaluator-assignments.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>Reset
                </a>
            @endif
        </div>
    </form>

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Evaluator</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Evaluatee</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Template</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Berlaku Mulai</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assignments as $assignment)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                            {{ $assignment->evaluator?->full_name ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                            {{ $assignment->evaluatee?->full_name ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-muted);">
                            {{ $assignment->template?->template_name ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-muted);">
                            {{ $assignment->effective_start_date?->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full
                                {{ $assignment->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $assignment->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @can('kpi.evaluator_assignment.update')
                                <a href="{{ route('kpi.evaluator-assignments.edit', $assignment) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">supervisor_account</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada assignment evaluator.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($assignments->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $assignments->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
