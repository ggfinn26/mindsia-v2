@extends('layouts.dashboard')

@section('title', 'Status Webhook - MINDSIA')
@section('header_title', 'Status Webhook')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto space-y-6">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Status Webhook</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Pantau konektivitas endpoint webhook yang terdaftar.
            </p>
        </div>
        @can('system.webhook.manage')
            <a href="{{ route('system.webhooks.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity self-start sm:self-auto"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Webhook
            </a>
        @endcan
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Nama</th>
                        <th class="hidden md:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Bot</th>
                        <th class="hidden lg:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Endpoint URL</th>
                        <th class="hidden sm:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="hidden sm:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Ping Terakhir</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($webhooks as $webhook)
                        @php
                            $statusMap = [
                                'ok'      => ['background-color:#f0fdf4;color:#059669;', 'OK'],
                                'failed'  => ['background-color:#fee2e2;color:#dc2626;', 'Gagal'],
                                'unknown' => ['background-color:var(--dash-nav-hover-bg);color:var(--dash-text-muted);border:1px solid var(--dash-card-border);', 'Belum dicek'],
                            ];
                            [$statusStyle, $statusLabel] = $statusMap[$webhook->last_status] ?? $statusMap['unknown'];
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">

                            {{-- Nama --}}
                            <td class="px-5 py-4">
                                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">
                                    {{ $webhook->name }}
                                </p>
                                {{-- Status inline on mobile --}}
                                <div class="sm:hidden mt-1.5 flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                          style="{{ $statusStyle }}">{{ $statusLabel }}</span>
                                    <span class="text-xs" style="color: var(--dash-text-faint);">
                                        {{ $webhook->last_ping_at ? $webhook->last_ping_at->diffForHumans() : 'Belum pernah' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Bot --}}
                            <td class="hidden md:table-cell px-5 py-4 text-sm"
                                style="color: var(--dash-text-muted);">
                                {{ $webhook->bot?->name ?? '—' }}
                            </td>

                            {{-- Endpoint URL --}}
                            <td class="hidden lg:table-cell px-5 py-4 max-w-[220px]">
                                <code class="rounded-lg px-2 py-0.5 text-xs font-mono break-all"
                                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);">
                                    {{ $webhook->endpoint_url }}
                                </code>
                            </td>

                            {{-- Status --}}
                            <td class="hidden sm:table-cell px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                      style="{{ $statusStyle }}">{{ $statusLabel }}</span>
                            </td>

                            {{-- Ping Terakhir --}}
                            <td class="hidden sm:table-cell px-5 py-4 text-xs"
                                style="color: var(--dash-text-muted);">
                                {{ $webhook->last_ping_at ? $webhook->last_ping_at->diffForHumans() : 'Belum pernah' }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    @can('system.webhook.view')
                                        <form method="POST" action="{{ route('system.webhooks.ping', $webhook) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                                    style="border: 1px solid var(--dash-card-border); color: var(--dash-primary); background-color: var(--dash-card-bg);"
                                                    onmouseover="this.style.backgroundColor='var(--dash-primary-bg)'"
                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">wifi_tethering</span>
                                                <span class="hidden sm:inline">Ping</span>
                                            </button>
                                        </form>
                                    @endcan
                                    @can('system.webhook.manage')
                                        <a href="{{ route('system.webhooks.edit', $webhook) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                           style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[14px]">edit</span>
                                            <span class="hidden sm:inline">Edit</span>
                                        </a>
                                        <form method="POST" action="{{ route('system.webhooks.destroy', $webhook) }}"
                                              onsubmit="return confirm('Hapus webhook ini?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                                                    style="background-color: #dc2626;">
                                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                                <span class="hidden sm:inline">Hapus</span>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">webhook</span>
                                <p class="text-sm" style="color: var(--dash-text-muted);">
                                    Belum ada webhook yang dikonfigurasi.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
