@extends('layouts.dashboard')

@section('title', 'Status Webhook - MINDSIA')
@section('header_title', 'Status Webhook')

@section('content')
@php
    $webhooksData = $webhooks->map(function($w) {
        return [
            'id' => $w->id,
            'name' => $w->name,
            'notes' => $w->notes,
            'endpoint_url' => $w->endpoint_url,
            'bot_name' => $w->bot?->name,
            'bot_type' => $w->bot?->type,
            'status' => $w->last_status ?? 'unknown',
            'last_ping_at' => $w->last_ping_at ? $w->last_ping_at->diffForHumans() : 'Belum pernah',
            'is_pinging' => false,
        ];
    })->values();
@endphp

<div x-data="{
    webhooks: {{ Js::from($webhooksData) }},
    isPingingAll: false,
    deleteTarget: null,
    showDeleteModal: false,
    toastMessage: '',
    toastType: 'success',
    showToast: false,
    triggerToast(msg, type = 'success') {
        this.toastMessage = msg;
        this.toastType = type;
        this.showToast = true;
        setTimeout(() => { this.showToast = false; }, 3500);
    },
    async pingWebhook(id) {
        const item = this.webhooks.find(w => w.id === id);
        if (!item || item.is_pinging) return;
        item.is_pinging = true;
        try {
            const res = await fetch('{{ url('/system/webhooks') }}/' + id + '/ping', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();
            if (data.status) {
                item.status = data.status;
                item.last_ping_at = data.last_ping_at;
                this.triggerToast(data.message, data.success ? 'success' : 'error');
            }
        } catch (e) {
            item.status = 'failed';
            this.triggerToast('Gagal menghubungi endpoint webhook.', 'error');
        } finally {
            item.is_pinging = false;
        }
    },
    async pingAll() {
        if (this.isPingingAll || this.webhooks.length === 0) return;
        this.isPingingAll = true;
        for (const item of this.webhooks) {
            await this.pingWebhook(item.id);
        }
        this.isPingingAll = false;
    },
    confirmDelete(id, name) {
        this.deleteTarget = { id, name };
        this.showDeleteModal = true;
    }
}" class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Live Toast Notification --}}
    <div x-show="showToast"
         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border text-sm font-medium"
         :style="toastType === 'success' ? 'background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;' : 'background-color: #fef2f2; border-color: #fecaca; color: #dc2626;'"
         style="display: none;">
        <span class="material-symbols-outlined text-[18px]" x-text="toastType === 'success' ? 'check_circle' : 'error'"></span>
        <span x-text="toastMessage"></span>
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Status Webhook</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Pantau konektivitas dan kesehatan endpoint webhook integrasi secara real-time.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            @can('system.webhook.view')
            <button type="button" @click="pingAll()" :disabled="isPingingAll || webhooks.length === 0"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors disabled:opacity-50"
                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]" :class="isPingingAll ? 'animate-spin' : ''">sync</span>
                <span x-text="isPingingAll ? 'Memeriksa Semua...' : 'Ping Semua'">Ping Semua</span>
            </button>
            @endcan

            @can('system.webhook.manage')
            <a href="{{ route('system.webhooks.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>Tambah Webhook
            </a>
            @endcan
        </div>
    </div>

    {{-- Server Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    {{-- Health Metric Pills --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="px-4 py-3 rounded-xl flex items-center justify-between"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary-text);">hub</span>
                <span class="text-xs font-semibold" style="color: var(--dash-text-muted);">Total Webhook</span>
            </div>
            <span class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);" x-text="webhooks.length">{{ $webhooks->count() }}</span>
        </div>

        <div class="px-4 py-3 rounded-xl flex items-center justify-between"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                <span class="text-xs font-semibold text-emerald-700">Endpoint OK</span>
            </div>
            <span class="font-jakarta text-base font-bold text-emerald-600" x-text="webhooks.filter(w => w.status === 'ok').length">{{ $webhooks->where('last_status', 'ok')->count() }}</span>
        </div>

        <div class="px-4 py-3 rounded-xl flex items-center justify-between"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-rose-600">cancel</span>
                <span class="text-xs font-semibold text-rose-700">Gagal</span>
            </div>
            <span class="font-jakarta text-base font-bold text-rose-600" x-text="webhooks.filter(w => w.status === 'failed').length">{{ $webhooks->where('last_status', 'failed')->count() }}</span>
        </div>

        <div class="px-4 py-3 rounded-xl flex items-center justify-between"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-amber-600">help</span>
                <span class="text-xs font-semibold text-amber-700">Belum Dicek</span>
            </div>
            <span class="font-jakarta text-base font-bold text-amber-600" x-text="webhooks.filter(w => w.status === 'unknown').length">{{ $webhooks->where('last_status', 'unknown')->count() }}</span>
        </div>
    </div>

    {{-- Main Table Container --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <tr>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Nama Webhook</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Bot Terkait</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Endpoint URL</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Ping Terakhir</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-right" style="color: var(--dash-text-faint);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($webhooks as $webhook)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                            {{ $webhook->name }}
                            @if($webhook->notes)
                            <p class="text-xs font-normal mt-0.5 line-clamp-1" style="color: var(--dash-text-muted);">{{ $webhook->notes }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($webhook->bot)
                                @if($webhook->bot->type === 'telegram')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-sky-800">
                                    <span class="material-symbols-outlined text-[13px]">send</span>{{ $webhook->bot->name }}
                                </span>
                                @elseif($webhook->bot->type === 'whatsapp')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    <span class="material-symbols-outlined text-[13px]">chat</span>{{ $webhook->bot->name }}
                                </span>
                                @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                                    {{ $webhook->bot->name }}
                                </span>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs" style="color: var(--dash-text-faint);">
                                    <span class="material-symbols-outlined text-[13px]">link_off</span>Tanpa Bot
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 max-w-[280px]">
                            <code class="rounded px-2 py-0.5 text-xs font-mono break-all inline-block max-w-full truncate"
                                  title="{{ $webhook->endpoint_url }}"
                                  style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-heading); border: 1px solid var(--dash-card-border);">{{ $webhook->endpoint_url }}</code>
                        </td>
                        <td class="px-5 py-3.5">
                            <template x-if="webhooks.find(w => w.id === {{ $webhook->id }})">
                                <div>
                                    <template x-if="webhooks.find(w => w.id === {{ $webhook->id }}).status === 'ok'">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>OK
                                        </span>
                                    </template>
                                    <template x-if="webhooks.find(w => w.id === {{ $webhook->id }}).status === 'failed'">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Gagal
                                        </span>
                                    </template>
                                    <template x-if="webhooks.find(w => w.id === {{ $webhook->id }}).status === 'unknown'">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Belum Dicek
                                        </span>
                                    </template>
                                </div>
                            </template>
                            <noscript>
                                @if($webhook->last_status === 'ok')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>OK
                                </span>
                                @elseif($webhook->last_status === 'failed')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Gagal
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Belum Dicek
                                </span>
                                @endif
                            </noscript>
                        </td>
                        <td class="px-5 py-3.5 text-xs" style="color: var(--dash-text-muted);">
                            <span x-text="webhooks.find(w => w.id === {{ $webhook->id }})?.last_ping_at ?? '{{ $webhook->last_ping_at ? $webhook->last_ping_at->diffForHumans() : 'Belum pernah' }}'">
                                {{ $webhook->last_ping_at ? $webhook->last_ping_at->diffForHumans() : 'Belum pernah' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('system.webhook.view')
                                <form method="POST" action="{{ route('system.webhooks.ping', $webhook) }}" class="m-0"
                                      @submit.prevent="pingWebhook({{ $webhook->id }})">
                                    @csrf
                                    <button type="submit"
                                            :disabled="webhooks.find(w => w.id === {{ $webhook->id }})?.is_pinging"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors text-sky-700 bg-sky-50 border border-sky-200 hover:bg-sky-100 disabled:opacity-50">
                                        <span class="material-symbols-outlined text-[14px]"
                                              :class="webhooks.find(w => w.id === {{ $webhook->id }})?.is_pinging ? 'animate-spin' : ''">
                                            sync
                                        </span>
                                        <span x-text="webhooks.find(w => w.id === {{ $webhook->id }})?.is_pinging ? 'Pinging...' : 'Ping'">Ping</span>
                                    </button>
                                </form>
                                @endcan

                                @can('system.webhook.manage')
                                <a href="{{ route('system.webhooks.edit', $webhook) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                </a>

                                <button type="button"
                                        @click="confirmDelete({{ $webhook->id }}, '{{ addslashes($webhook->name) }}')"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">webhook</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada webhook yang dikonfigurasi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Custom Alpine Modal Konfirmasi Hapus (Zero native confirm()) --}}
    <template x-teleport="body">
        <div x-show="showDeleteModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showDeleteModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showDeleteModal" @click.away="showDeleteModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        {{-- Header modal (rounded-t-2xl) --}}
                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-red-600 text-[22px]">delete</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Webhook?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Webhook <strong x-text="deleteTarget?.name"></strong> akan dihapus permanen dari sistem dan integrasi tidak lagi dapat mengirim sinyal ke endpoint tersebut.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Footer modal (rounded-b-2xl) --}}
                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showDeleteModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <form :action="'{{ url('/system/webhooks') }}/' + deleteTarget?.id" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>Ya, Hapus Webhook
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
