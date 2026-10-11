@extends('layouts.dashboard')

@section('title', 'Evaluasi KPI')
@section('header_title', 'Evaluasi KPI')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">KPI</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Evaluasi KPI</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Buat dan kelola evaluasi KPI pegawai.</p>
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

    {{-- Flash message --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter form --}}
    <form method="GET" action="{{ route('kpi.evaluations.index') }}"
          class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Pegawai</label>
            <select name="employee_id"
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Template</label>
            <select name="kpi_template_id"
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua</option>
                @foreach ($templates as $t)
                    <option value="{{ $t->id }}" @selected(request('kpi_template_id') == $t->id)>{{ $t->template_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide" style="color: var(--dash-text-faint);">Status</label>
            <select name="status"
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="finalized" @selected(request('status') === 'finalized')>Finalized</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>Filter
            </button>
            @if (request()->hasAny(['employee_id', 'kpi_template_id', 'status']))
                <a href="{{ route('kpi.evaluations.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Template</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Periode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Evaluator</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Skor</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Grade</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($evaluations as $eval)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">{{ $eval->employee_name_snapshot }}</p>
                                <p class="text-xs" style="color: var(--dash-text-faint);">{{ $eval->position_name_snapshot }}</p>
                            </td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                                {{ $eval->template?->template_name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                                {{ $eval->period_start_date?->format('d M Y') }} - {{ $eval->period_end_date?->format('d M Y') }}
                            </td>
                            <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                                {{ $eval->evaluator?->full_name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right tabular-nums font-semibold" style="color: var(--dash-text-heading);">
                                {{ $eval->total_score !== null ? number_format($eval->total_score, 1) : '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($eval->grade)
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold"
                                          style="background-color: #dbeafe; color: #1e40af;">{{ $eval->grade }}</span>
                                @else
                                    <span style="color: var(--dash-text-faint);">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($eval->isDraft())
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold"
                                          style="background-color: #fef3c7; color: #92400e;">Draft</span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold"
                                          style="background-color: #d1fae5; color: #065f46;">Final</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('kpi.evaluations.show', $eval) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">assessment</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada evaluasi KPI.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($evaluations->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $evaluations->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
