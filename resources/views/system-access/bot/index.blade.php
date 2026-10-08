@extends('layouts.dashboard')

@section('header_title', 'Kelola Bot')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Kelola Bot</h1>
            <p class="mt-1 text-sm text-slate-500">Kredensial bot integrasi (Telegram, WhatsApp).</p>
        </div>
        @can('system.bot.manage')
        <a href="{{ route('system.bots.create') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[#215aac] px-5 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Bot
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
                        <th class="px-6 py-4">Nama Bot</th>
                        <th class="px-6 py-4">Platform</th>
                        <th class="px-6 py-4">Token</th>
                        <th class="px-6 py-4">Webhook</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($bots as $bot)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $bot->name }}</td>
                        <td class="px-6 py-4">
                            @php $typeColors = ['telegram' => 'bg-sky-100 text-sky-800', 'whatsapp' => 'bg-green-100 text-green-800']; @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColors[$bot->type] ?? 'bg-slate-100 text-slate-700' }}">{{ ucfirst($bot->type) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <code class="rounded bg-slate-100 px-2 py-0.5 text-xs font-mono text-slate-500">••••••••</code>
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $bot->webhooks->count() }} endpoint</td>
                        <td class="px-6 py-4">
                            @if($bot->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('system.bot.manage')
                                <a href="{{ route('system.bots.edit', $bot) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">Edit</a>
                                <form method="POST" action="{{ route('system.bots.toggle', $bot) }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors {{ $bot->is_active ? 'border-orange-200 text-orange-600 hover:bg-orange-50' : 'border-green-200 text-green-600 hover:bg-green-50' }}">
                                        {{ $bot->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada konfigurasi bot.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
