@extends('layouts.dashboard')

@section('title', 'Kelola Bot - MINDSIA')
@section('header_title', 'Kelola Bot')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kelola Bot</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kredensial dan integrasi bot notifikasi (Telegram, WhatsApp).
            </p>
        </div>
        @can('system.bot.manage')
        <a href="{{ route('system.bots.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>Tambah Bot
        </a>
        @endcan
    </div>

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

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <tr>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Nama Bot</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Platform</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Token</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Webhook</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-right" style="color: var(--dash-text-faint);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bots as $bot)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                            {{ $bot->name }}
                            @if($bot->notes)
                            <p class="text-xs font-normal mt-0.5 line-clamp-1" style="color: var(--dash-text-muted);">{{ $bot->notes }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($bot->type === 'telegram')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-sky-800">
                                <span class="material-symbols-outlined text-[13px]">send</span>Telegram
                            </span>
                            @elseif($bot->type === 'whatsapp')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                <span class="material-symbols-outlined text-[13px]">chat</span>WhatsApp
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                                {{ \App\Support\EnumLabels::get('integration_type', $bot->type) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <code class="rounded px-2 py-0.5 text-xs font-mono"
                                  style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">••••••••</code>
                        </td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-muted);">
                            {{ $bot->webhooks->count() }} endpoint
                        </td>
                        <td class="px-5 py-3.5">
                            @if($bot->is_active)
                            <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>Nonaktif
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('system.bot.manage')
                                <a href="{{ route('system.bots.edit', $bot) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                </a>

                                <div x-data="{ showConfirm: false }" class="inline-block">
                                    @if($bot->is_active)
                                    <button type="button" @click="showConfirm = true"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100">
                                        <span class="material-symbols-outlined text-[14px]">toggle_off</span>Nonaktifkan
                                    </button>
                                    @else
                                    <button type="button" @click="showConfirm = true"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100">
                                        <span class="material-symbols-outlined text-[14px]">toggle_on</span>Aktifkan
                                    </button>
                                    @endif

                                    <template x-teleport="body">
                                        <div x-show="showConfirm" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
                                            <div x-show="showConfirm"
                                                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                                                 @click="showConfirm = false"></div>

                                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                                    <div x-show="showConfirm" @click.away="showConfirm = false"
                                                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                                                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                                                        <div class="rounded-t-2xl px-6 py-5"
                                                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                                            <div class="flex items-start gap-3.5">
                                                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $bot->is_active ? 'bg-amber-100' : 'bg-emerald-100' }}">
                                                                    <span class="material-symbols-outlined text-[22px] {{ $bot->is_active ? 'text-amber-600' : 'text-emerald-600' }}">
                                                                        {{ $bot->is_active ? 'pause_circle' : 'play_circle' }}
                                                                    </span>
                                                                </div>
                                                                <div>
                                                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                                                                        {{ $bot->is_active ? 'Nonaktifkan Bot?' : 'Aktifkan Bot?' }}
                                                                    </h3>
                                                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                                                        @if($bot->is_active)
                                                                        Bot <strong>{{ $bot->name }}</strong> akan dinonaktifkan dan sementara waktu tidak dapat merespons webhook.
                                                                        @else
                                                                        Bot <strong>{{ $bot->name }}</strong> akan diaktifkan kembali untuk memproses pesan dan webhook.
                                                                        @endif
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                                            <button type="button" @click="showConfirm = false"
                                                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                                Batal
                                                            </button>
                                                            <form method="POST" action="{{ route('system.bots.toggle', $bot) }}" class="m-0">
                                                                @csrf
                                                                <button type="submit"
                                                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                                                        style="{{ $bot->is_active ? 'background-color: #d97706;' : 'background-color: #059669;' }}">
                                                                    <span class="material-symbols-outlined text-[16px]">{{ $bot->is_active ? 'toggle_off' : 'toggle_on' }}</span>
                                                                    {{ $bot->is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">smart_toy</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada konfigurasi bot.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
