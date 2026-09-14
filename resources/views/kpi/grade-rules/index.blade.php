@extends('layouts.dashboard')

@section('title', 'Grade KPI')
@section('header_title', 'Grade KPI')

@section('content')
@php
    $tabs = [
        ['label' => 'Grade Rules', 'active' => true],
    ];
@endphp

<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">KPI</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Grade KPI</h2>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Definisikan range skor untuk setiap grade. Grade dipakai otomatis saat evaluasi di-finalize.</p>
        </div>
        @can('kpi.grade_rule.view')
            <a href="{{ route('kpi.grade-rules.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">add</span> Tambah Grade
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Grade</th>
                        <th class="px-5 py-3 text-right">Skor Min</th>
                        <th class="px-5 py-3 text-right">Skor Maks</th>
                        <th class="px-5 py-3">Deskripsi</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rules as $rule)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-bold text-slate-900 text-lg">{{ $rule->grade }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-slate-700">{{ $rule->minimum_score }}</td>
                            <td class="px-5 py-4 text-right tabular-nums text-slate-700">{{ $rule->maximum_score ?? '∞' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $rule->description ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $rule->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $rule->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                @can('kpi.grade_rule.view')
                                    <a href="{{ route('kpi.grade-rules.edit', $rule) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50 mr-1">Edit</a>
                                @endcan
                                @can('kpi.grade_rule.delete')
                                    <form method="POST" action="{{ route('kpi.grade-rules.destroy', $rule) }}" onsubmit="return confirm('Hapus grade ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-9 items-center border border-red-200 px-3 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">grade</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada grade rule.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
