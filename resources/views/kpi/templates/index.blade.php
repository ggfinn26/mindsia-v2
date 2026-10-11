@extends('layouts.dashboard')

@section('title', 'Template KPI')
@section('header_title', 'Template KPI')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">KPI</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Template KPI</h2>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Kelola template evaluasi KPI per posisi. Template menentukan indikator dan bobot penilaian.</p>
        </div>
        @can('kpi.template.create')
            <a href="{{ route('kpi.templates.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Template
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <form method="GET" action="{{ route('kpi.templates.index') }}" class="flex flex-wrap gap-3 border border-slate-200 bg-white p-4">
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Posisi</label>
            <select name="position_id" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">Semua posisi</option>
                @foreach ($positions as $position)
                    <option value="{{ $position->id }}" @selected(request('position_id') == $position->id)>{{ $position->position_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-1 block text-xs font-medium text-slate-700">Status</label>
            <select name="is_active" class="block border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">Semua</option>
                <option value="1" @selected(request('is_active') === '1')>Aktif</option>
                <option value="0" @selected(request('is_active') === '0')>Nonaktif</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="inline-flex min-h-9 items-center bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">Filter</button>
            @if (request()->hasAny(['position_id', 'is_active', 'search']))
                <a href="{{ route('kpi.templates.index') }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
            @endif
        </div>
    </form>

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Template</th>
                        <th class="px-5 py-3">Posisi</th>
                        <th class="px-5 py-3 text-right">Indikator</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($templates as $template)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $template->template_name }}</p>
                                <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $template->template_code }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $template->position?->position_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-right text-slate-700">{{ $template->indicators->count() }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $template->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('kpi.templates.show', $template) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">assignment</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada template KPI.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($templates->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $templates->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
