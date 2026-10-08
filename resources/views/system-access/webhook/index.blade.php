@extends('layouts.dashboard')

@section('header_title', 'Status Webhook')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Status Webhook</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau konektivitas endpoint webhook yang terdaftar.</p>
        </div>
        @can('system.webhook.manage')
        <a href="{{ route('system.webhooks.create') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[#215aac] px-5 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Webhook
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
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Bot</th>
                        <th class="px-6 py-4">Endpoint URL</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Ping Terakhir</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($webhooks as $webhook)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $webhook->name }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ $webhook->bot?->name ?? '-' }}</td>
                        <td class="px-6 py-4 max-w-[260px]">
                            <code class="rounded bg-slate-100 px-2 py-0.5 text-xs font-mono text-slate-600 break-all">{{ $webhook->endpoint_url }}</code>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusMap = [
                                    'ok'      => ['bg-green-100 text-green-800', 'OK'],
                                    'failed'  => ['bg-red-100 text-red-700', 'Gagal'],
                                    'unknown' => ['bg-slate-100 text-slate-500', 'Belum dicek'],
                                ];
                                [$cls, $label] = $statusMap[$webhook->last_status] ?? $statusMap['unknown'];
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cls }}">{{ $label }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs">
                            {{ $webhook->last_ping_at ? $webhook->last_ping_at->diffForHumans() : 'Belum pernah' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('system.webhook.view')
                                <form method="POST" action="{{ route('system.webhooks.ping', $webhook) }}">
                                    @csrf
                                    <button type="submit" class="rounded-lg border border-sky-200 px-3 py-1.5 text-xs font-medium text-sky-600 hover:bg-sky-50 transition-colors">Ping</button>
                                </form>
                                @endcan
                                @can('system.webhook.manage')
                                <a href="{{ route('system.webhooks.edit', $webhook) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">Edit</a>
                                <form method="POST" action="{{ route('system.webhooks.destroy', $webhook) }}" onsubmit="return confirm('Hapus webhook ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">Hapus</button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada webhook yang dikonfigurasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
