@extends('layouts.dashboard')

@section('title', 'Assignment Evaluator KPI')
@section('header_title', 'Assignment Evaluator')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">KPI</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Assignment Evaluator</h2>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Tentukan siapa yang mengevaluasi siapa dan dengan template apa.</p>
        </div>
        @can('kpi.evaluation.create')
            <a href="{{ route('kpi.evaluator-assignments.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">add</span> Tambah Assignment
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
            <label class="mb-1 block text-xs font-medium text-slate-700">Evaluator</label>
            <select name="evaluator_employee_id" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">Semua</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(request('evaluator_employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Evaluatee</label>
            <select name="evaluatee_employee_id" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">Semua</option>
                @foreach ($employees as $emp)
                    <option value="{{ $emp->id }}" @selected(request('evaluatee_employee_id') == $emp->id)>{{ $emp->full_name }}</option>
                @endforeach
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
                        <th class="px-5 py-3">Evaluator</th>
                        <th class="px-5 py-3">Evaluatee</th>
                        <th class="px-5 py-3">Template</th>
                        <th class="px-5 py-3">Berlaku Mulai</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($assignments as $assignment)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-medium text-slate-900">{{ $assignment->evaluator?->full_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ $assignment->evaluatee?->full_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $assignment->template?->template_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $assignment->effective_start_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $assignment->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $assignment->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                @can('kpi.evaluation.create')
                                    <a href="{{ route('kpi.evaluator-assignments.edit', $assignment) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">supervisor_account</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada assignment evaluator.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($assignments->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $assignments->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
