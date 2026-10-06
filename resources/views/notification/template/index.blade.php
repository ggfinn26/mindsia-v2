@extends('layouts.dashboard')

@section('header_title', 'Template Notifikasi')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Notifikasi</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Template Notifikasi</h1>
        </div>
        @can('notification.template.create')
        <a href="{{ route('notification-templates.create') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[#215aac] px-5 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
            <span class="material-symbols-outlined text-[18px]">add</span> Buat Template
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Template Key</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Subject</th>
                        <th class="px-6 py-4">Variabel</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($templates as $tpl)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <code class="rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-700 font-mono">{{ $tpl->template_key }}</code>
                        </td>
                        <td class="px-6 py-4">
                            @php $typeColors = ['email' => 'bg-blue-100 text-blue-800', 'telegram' => 'bg-sky-100 text-sky-800', 'in-app' => 'bg-violet-100 text-violet-800']; @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColors[$tpl->type] ?? 'bg-slate-100 text-slate-700' }}">{{ $tpl->type }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $tpl->subject ?: '-' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $tpl->variables->count() }} variabel</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('notification-templates.show', $tpl) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">Detail</a>
                                @can('notification.template.update')
                                <a href="{{ route('notification-templates.edit', $tpl) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">Edit</a>
                                @endcan
                                @can('notification.template.delete')
                                <form method="POST" action="{{ route('notification-templates.destroy', $tpl) }}" onsubmit="return confirm('Hapus template ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada template notifikasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
