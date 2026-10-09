@extends('layouts.dashboard')

@section('header_title', 'Template Notifikasi')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Notifikasi</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Template Notifikasi</h1>
        </div>
        @can('notification.template.create')
        <a href="{{ route('notification-templates.create') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[var(--dash-primary)] px-5 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-colors">
            <span class="material-symbols-outlined text-[18px]">add</span> Buat Template
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 border border-emerald-200 [.mode-dark_&]:border-emerald-500/20 px-4 py-3 text-sm text-emerald-700 [.mode-dark_&]:text-emerald-400">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="rounded-lg bg-red-50 [.mode-dark_&]:bg-red-500/10 border border-red-200 [.mode-dark_&]:border-red-500/20 px-4 py-3 text-sm text-red-700 [.mode-dark_&]:text-red-400">{{ session('error') }}</div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-bold uppercase tracking-wider text-[var(--dash-text-muted)]">
                    <tr>
                        <th class="px-6 py-4">Template Key</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Subject</th>
                        <th class="px-6 py-4">Variabel</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)]">
                    @forelse($templates as $tpl)
                    <tr class="hover:bg-[var(--dash-page-bg)] transition-colors">
                        <td class="px-6 py-4">
                            <code class="rounded bg-[var(--dash-input-bg)] px-2 py-0.5 text-xs text-[var(--dash-text-body)] font-mono">{{ $tpl->template_key }}</code>
                        </td>
                        <td class="px-6 py-4">
                            @php $typeColors = ['email' => 'bg-blue-100 [.mode-dark_&]:bg-blue-500/10 text-blue-800 [.mode-dark_&]:text-blue-400', 'telegram' => 'bg-sky-100 [.mode-dark_&]:bg-sky-500/10 text-sky-800 [.mode-dark_&]:text-sky-400', 'in-app' => 'bg-violet-100 [.mode-dark_&]:bg-violet-500/10 text-violet-800 [.mode-dark_&]:text-violet-400']; @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColors[$tpl->type] ?? 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]' }}">{{ $tpl->type }}</span>
                        </td>
                        <td class="px-6 py-4 text-[var(--dash-text-muted)]">{{ $tpl->subject ?: '-' }}</td>
                        <td class="px-6 py-4 text-[var(--dash-text-muted)]">{{ $tpl->variables->count() }} variabel</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('notification-templates.show', $tpl) }}" class="rounded-lg border border-[var(--dash-input-border)] px-3 py-1.5 text-xs font-medium text-[var(--dash-text-muted)] hover:bg-[var(--dash-page-bg)] transition-colors inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">visibility</span>Detail</a>
                                @can('notification.template.update')
                                <a href="{{ route('notification-templates.edit', $tpl) }}" class="rounded-lg border border-[var(--dash-input-border)] px-3 py-1.5 text-xs font-medium text-[var(--dash-text-muted)] hover:bg-[var(--dash-page-bg)] transition-colors inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">edit</span>Edit</a>
                                @endcan
                                @can('notification.template.delete')
                                <form method="POST" action="{{ route('notification-templates.destroy', $tpl) }}" onsubmit="return confirm('Hapus template ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 [.mode-dark_&]:border-red-500/20 px-3 py-1.5 text-xs font-medium text-red-600 [.mode-dark_&]:text-red-400 hover:bg-red-50 [.mode-dark_&]:hover:bg-red-500/10 transition-colors inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[14px]">delete</span>Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-[var(--dash-text-faint)]">Belum ada template notifikasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
