@extends('layouts.dashboard')

@section('header_title', 'Kelola Bot')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Kelola Bot</h1>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Kredensial bot integrasi (Telegram, WhatsApp).</p>
        </div>
        @can('system.bot.manage')
        <a href="{{ route('system.bots.create') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[var(--dash-primary)] px-5 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-colors">
            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Bot
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 [.mode-dark_&]:bg-emerald-500/10 [.mode-dark_&]:border-emerald-500/20 [.mode-dark_&]:text-emerald-400">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 [.mode-dark_&]:bg-red-500/10 [.mode-dark_&]:border-red-500/20 [.mode-dark_&]:text-red-400">{{ session('error') }}</div>
    @endif

    <div class="overflow-hidden rounded-xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-bold uppercase tracking-wider text-[var(--dash-text-muted)]">
                    <tr>
                        <th class="px-6 py-4">Nama Bot</th>
                        <th class="px-6 py-4">Platform</th>
                        <th class="px-6 py-4">Token</th>
                        <th class="px-6 py-4">Webhook</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)]">
                    @forelse($bots as $bot)
                    <tr class="hover:bg-[var(--dash-page-bg)] transition-colors">
                        <td class="px-6 py-4 font-bold text-[var(--dash-text-heading)]">{{ $bot->name }}</td>
                        <td class="px-6 py-4">
                            @php $typeColors = ['telegram' => 'bg-sky-50 text-sky-700 border-sky-200 [.mode-dark_&]:bg-sky-500/10 [.mode-dark_&]:text-sky-400 [.mode-dark_&]:border-sky-500/20', 'whatsapp' => 'bg-emerald-50 text-emerald-700 border-emerald-200 [.mode-dark_&]:bg-emerald-500/10 [.mode-dark_&]:text-emerald-400 [.mode-dark_&]:border-emerald-500/20']; @endphp
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-bold {{ $typeColors[$bot->type] ?? 'bg-gray-50 text-gray-700 border-gray-200 [.mode-dark_&]:bg-gray-500/10 [.mode-dark_&]:text-gray-400 [.mode-dark_&]:border-gray-500/20' }}">{{ ucfirst($bot->type) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <code class="rounded bg-[var(--dash-input-bg)] px-2 py-0.5 text-xs font-mono text-[var(--dash-text-body)] border border-[var(--dash-input-border)]">••••••••</code>
                        </td>
                        <td class="px-6 py-4 text-[var(--dash-text-body)]">{{ $bot->webhooks->count() }} endpoint</td>
                        <td class="px-6 py-4">
                            @if($bot->is_active)
                            <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 [.mode-dark_&]:bg-emerald-500/10 [.mode-dark_&]:text-emerald-400 [.mode-dark_&]:border-emerald-500/20 px-2.5 py-0.5 text-xs font-bold">Aktif</span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-red-50 text-red-700 border border-red-200 [.mode-dark_&]:bg-red-500/10 [.mode-dark_&]:text-red-400 [.mode-dark_&]:border-red-500/20 px-2.5 py-0.5 text-xs font-bold">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('system.bot.manage')
                                <a href="{{ route('system.bots.edit', $bot) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                </a>
                                <form method="POST" action="{{ route('system.bots.toggle', $bot) }}">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                            style="background-color: {{ $bot->is_active ? '#dc2626' : '#059669' }};">
                                        <span class="material-symbols-outlined text-[14px]">swap_horiz</span>{{ $bot->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-[var(--dash-text-muted)]">Belum ada konfigurasi bot.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
