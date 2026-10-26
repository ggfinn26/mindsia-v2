@extends('layouts.dashboard')

@section('title', 'API Keys - MINDSIA')
@section('header_title', 'API Keys')

@section('content')
<div x-data="{
    deleteTarget: null,
    showDeleteModal: false,
    confirmDelete(id, label) {
        this.deleteTarget = { id, label };
        this.showDeleteModal = true;
    }
}" class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">API Keys</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Rotasi key eksternal (ConvertAPI, dll). Key aktif dipilih otomatis menggunakan least-recently-used (LRU).
            </p>
        </div>
        @can('system.api_key.create')
        <a href="{{ route('system.api-keys.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>Tambah Key
        </a>
        @endcan
    </div>

    {{-- Flash Messages --}}
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

    {{-- Compact Metric Strip --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="px-4 py-3 rounded-xl flex items-center justify-between"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary-text);">vpn_key</span>
                <span class="text-xs font-semibold" style="color: var(--dash-text-muted);">Total Key</span>
            </div>
            <span class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">{{ $keys->count() }}</span>
        </div>

        <div class="px-4 py-3 rounded-xl flex items-center justify-between"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                <span class="text-xs font-semibold text-emerald-700">Key Aktif</span>
            </div>
            <span class="font-jakarta text-base font-bold text-emerald-600">{{ $keys->where('is_active', true)->count() }}</span>
        </div>

        <div class="px-4 py-3 rounded-xl flex items-center justify-between"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-rose-600">pause_circle</span>
                <span class="text-xs font-semibold text-rose-700">Nonaktif</span>
            </div>
            <span class="font-jakarta text-base font-bold text-rose-600">{{ $keys->where('is_active', false)->count() }}</span>
        </div>

        <div x-data="totalQuotaChecker('{{ route('system.api-keys.total-quota') }}')"
             x-init="check()"
             class="px-4 py-3 rounded-xl flex items-center justify-between"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-violet-600">tune</span>
                <span class="text-xs font-semibold text-violet-700">Sisa Kuota ConvertAPI</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-jakarta text-sm sm:text-base font-bold text-violet-700"
                      x-text="loading ? '...' : (result ? formatQuota(result.total_remaining_quota ?? result.total_seconds_left) : (error ? 'Gagal' : '-'))">
                    -
                </span>
                <button type="button" @click="check()" :disabled="loading" title="Cek ulang total kuota"
                        class="text-violet-600 hover:text-violet-800 disabled:opacity-50 inline-flex items-center">
                    <span class="material-symbols-outlined text-[16px]" :class="loading ? 'animate-spin' : ''">sync</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Main Table Container --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <tr>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Label</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Tipe Service</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Key Value</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Terakhir Dipakai</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-right" style="color: var(--dash-text-faint);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($keys as $key)
                    <tr onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                            {{ $key->label }}
                            @if($key->notes)
                            <p class="text-xs font-normal mt-0.5 line-clamp-1" style="color: var(--dash-text-muted);">{{ $key->notes }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($key->type === 'convertapi')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-violet-100 text-violet-800">
                                <span class="material-symbols-outlined text-[13px]">description</span>ConvertAPI
                            </span>
                            @elseif($key->type === 'telegram')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-sky-800">
                                <span class="material-symbols-outlined text-[13px]">send</span>Telegram
                            </span>
                            @elseif($key->type === 'wa')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                <span class="material-symbols-outlined text-[13px]">chat</span>WhatsApp
                            </span>
                            @elseif($key->type === 'email')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                <span class="material-symbols-outlined text-[13px]">mail</span>Email
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                                {{ \App\Support\EnumLabels::get('integration_type', $key->type) }}
                            </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <code class="rounded px-2 py-0.5 text-xs font-mono"
                                  style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">••••••••</code>
                        </td>
                        <td class="px-5 py-3.5 text-xs" style="color: var(--dash-text-muted);">
                            {{ $key->last_used_at ? $key->last_used_at->diffForHumans() : 'Belum pernah' }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($key->is_active)
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
                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                @if($key->type === 'convertapi')
                                <button type="button"
                                        x-data="quotaChecker('{{ route('system.api-keys.quota', $key) }}')"
                                        x-init="check()"
                                        @click="check()"
                                        :disabled="loading"
                                        :title="result ? ('Sisa kuota: ' + formatQuota(result.remaining_quota ?? result.seconds_left) + '. Klik untuk cek ulang.') : (error ? (error + '. Klik untuk coba lagi.') : 'Cek kuota ConvertAPI')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors border"
                                        :class="(result && (expired || habis)) || error ? 'text-rose-700 bg-rose-50 border-rose-200 hover:bg-rose-100' : 'text-violet-700 bg-violet-50 border-violet-200 hover:bg-violet-100 disabled:opacity-50'">
                                    <span class="material-symbols-outlined text-[14px]"
                                          :class="loading ? 'animate-spin' : ''"
                                          x-text="loading ? 'sync' : ((result && (expired || habis)) || error ? 'error' : (result ? 'tune' : 'analytics'))">analytics</span>
                                    <span x-text="statusText">Cek Kuota</span>
                                </button>
                                @endif

                                @can('system.api_key.update')
                                <a href="{{ route('system.api-keys.edit', $key) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                </a>

                                <form method="POST" action="{{ route('system.api-keys.toggle', $key) }}" class="m-0">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors {{ $key->is_active ? 'text-amber-700 bg-amber-50 border border-amber-200 hover:bg-amber-100' : 'text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100' }}">
                                        <span class="material-symbols-outlined text-[14px]">{{ $key->is_active ? 'toggle_off' : 'toggle_on' }}</span>
                                        {{ $key->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                @endcan

                                @can('system.api_key.delete')
                                <button type="button"
                                        @click="confirmDelete({{ $key->id }}, '{{ addslashes($key->label) }}')"
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
                                  style="color: var(--dash-text-faint);">vpn_key</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada API key yang terdaftar.</p>
                            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Tambah key ConvertAPI untuk fitur konversi docx ke PDF.</p>
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
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus API Key?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Key <strong x-text="deleteTarget?.label"></strong> akan dihapus permanen. Sistem tidak akan lagi menyertakan key ini dalam rotasi layanan.
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
                            <form :action="'{{ url('/system/api-keys') }}/' + deleteTarget?.id" method="POST" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>Ya, Hapus Key
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<style>[x-cloak]{display:none!important}</style>
<script>
function formatQuota(val) {
    if (val === null || val === undefined) return '-';
    val = Number(val);
    if (isNaN(val)) return '-';
    if (val <= 0) return '0 kuota (Habis)';
    return val.toLocaleString('id-ID') + ' kuota';
}

function totalQuotaChecker(url) {
    return {
        loading: false,
        result: null,
        error: null,
        async check() {
            this.loading = true;
            this.error = null;
            try {
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const text = await res.text();
                let data = null;
                try {
                    data = JSON.parse(text);
                } catch {
                    data = null;
                }

                if (!res.ok) {
                    this.error = (data && (data.error || data.message)) ? (data.error || data.message) : ('Gagal memeriksa kuota (HTTP ' + res.status + ')');
                    this.result = null;
                } else {
                    this.result = data;
                    this.error = null;
                }
            } catch (e) {
                console.error('totalQuotaChecker error:', e);
                this.error = 'Gagal menghubungi server.';
                this.result = null;
            } finally {
                this.loading = false;
            }
        }
    };
}

function quotaChecker(url) {
    return {
        loading: false,
        result: null,
        error: null,
        // Expired = tanggal langganan (SubscriptionValidUntil) sudah lewat.
        get expired() {
            if (!this.result || !this.result.reset_date) return false;
            const d = new Date(this.result.reset_date);
            return !isNaN(d.getTime()) && d < new Date();
        },
        // Habis = sisa kuota <= 0.
        get habis() {
            if (!this.result) return false;
            return Number(this.result.remaining_quota ?? this.result.seconds_left) <= 0;
        },
        get statusText() {
            if (this.loading) return 'Memeriksa...';
            if (this.error) return 'Gagal cek';
            if (!this.result) return 'Cek Kuota';
            if (this.expired) return 'EXPIRED';
            if (this.habis) return 'HABIS';
            return 'Sisa: ' + formatQuota(this.result.remaining_quota ?? this.result.seconds_left);
        },
        async check() {
            this.loading = true;
            this.error = null;
            try {
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const text = await res.text();
                let data = null;
                try {
                    data = JSON.parse(text);
                } catch {
                    data = null;
                }

                if (!res.ok) {
                    this.error = (data && (data.error || data.message)) ? (data.error || data.message) : ('Gagal memeriksa kuota (HTTP ' + res.status + ')');
                    this.result = null;
                } else {
                    this.result = data;
                    this.error = null;
                }
            } catch (e) {
                console.error('quotaChecker error:', e);
                this.error = 'Gagal menghubungi server.';
                this.result = null;
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endsection
