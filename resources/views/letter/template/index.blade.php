@extends('layouts.dashboard')

@section('title', 'Template Surat')
@section('header_title', 'Template Surat')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Surat</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Template Surat</h2>
            <p class="mt-1 text-sm text-slate-600">Upload file Word (.docx) sebagai template. Placeholder menggunakan alt text pada elemen.</p>
        </div>
        @can('letter.template.create')
            <a href="{{ route('letter-templates.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">add</span> Upload Template
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
                        <th class="px-5 py-3">Template</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Format Nomor</th>
                        <th class="px-5 py-3">File</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($templates as $tpl)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $tpl->template_name }}</p>
                                <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $tpl->template_code }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $tpl->letter_category ?? '—' }}</td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-600">{{ $tpl->letter_number_format ?? '—' }}</td>
                            <td class="px-5 py-4">
                                @if ($tpl->telegram_file_id)
                                    <span class="inline-flex items-center gap-1 text-xs text-emerald-700">
                                        <span class="material-symbols-outlined text-[14px]">description</span> Ada
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $tpl->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $tpl->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right flex justify-end gap-2">
                                <a href="{{ route('letter-templates.show', $tpl) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                                @can('letter.template.update')
                                    <form method="POST" action="{{ route('letter-templates.toggle-active', $tpl) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex min-h-9 items-center border {{ $tpl->is_active ? 'border-amber-300 text-amber-800 hover:bg-amber-50' : 'border-emerald-300 text-emerald-800 hover:bg-emerald-50' }} px-3 text-xs font-semibold">
                                            {{ $tpl->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">article</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada template surat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
