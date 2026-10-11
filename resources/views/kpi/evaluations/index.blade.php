@extends('layouts.dashboard')

@section('title', 'Evaluasi KPI')
@section('header_title', 'Evaluasi KPI')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">KPI</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Evaluasi KPI</h2>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Buat dan kelola evaluasi KPI pegawai.</p>
        </div>
        @can('kpi.evaluation.create')
            <a href="{{ route('kpi.evaluations.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">add</span> Buat Evaluasi
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <form method="GET" class="flex flex-wrap gap-3 border border-slate-200 bg-white p-4">
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Pegawai</label>
            <select name="employee_id" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">Semua</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Template</label>
            <select name="kpi_template_id" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">Semua</option>
                @foreach ($templates as $t)
                    <option value="{{ $t->id }}" @selected(request('kpi_template_id') == $t->id)>{{ $t->template_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Status</label>
            <select name="status" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">Semua</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="finalized" @selected(request('status') === 'finalized')>Finalized</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="inline-flex min-h-9 items-center bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Filter</button>
        </div>
    </form>

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Pegawai</th>
                        <th class="px-5 py-3">Template</th>
                        <th class="px-5 py-3">Periode</th>
                        <th class="px-5 py-3">Evaluator</th>
                        <th class="px-5 py-3 text-right">Skor</th>
                        <th class="px-5 py-3">Grade</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($evaluations as $eval)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $eval->employee_name_snapshot }}</p>
                                <p class="text-xs text-slate-400">{{ $eval->position_name_snapshot }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $eval->template?->template_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $eval->period_start_date?->format('d M Y') }} – {{ $eval->period_end_date?->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $eval->evaluator?->full_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-right tabular-nums font-semibold text-slate-900">
                                {{ $eval->total_score !== null ? number_format($eval->total_score, 1) : '—' }}
                            </td>
                            <td class="px-5 py-4">
                                @if ($eval->grade)
                                    <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-900">{{ $eval->grade }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $eval->isDraft() ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $eval->isDraft() ? 'Draft' : 'Final' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('kpi.evaluations.show', $eval) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">assessment</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada evaluasi KPI.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($evaluations->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $evaluations->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
