@extends('layouts.dashboard')

@section('title', 'Dokumen SOP')
@section('header_title', 'Dokumen SOP')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Surat</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Dokumen SOP</h2>
        </div>
        @can('letter.sop.create')
            <a href="{{ route('sop-documents.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">upload</span> Upload SOP
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Dokumen</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Versi</th>
                        <th class="px-5 py-3">Efektif</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($documents as $doc)
                        <tr class="{{ $doc->is_active ? '' : 'opacity-60' }} hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $doc->title }}</p>
                                <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $doc->document_code ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $doc->category ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $doc->version ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $doc->effective_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $doc->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $doc->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('sop-documents.show', $doc) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">policy</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada dokumen SOP.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($documents->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $documents->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
