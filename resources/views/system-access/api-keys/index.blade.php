@extends('layouts.dashboard')

@section('header_title', 'API Keys')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">API Keys</h1>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">Rotasi key eksternal (ConvertAPI, dll). Key aktif dipilih otomatis LRU.</p>
        </div>
        @can('system.api_key.create')
        <a href="{{ route('system.api-keys.create') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[var(--dash-primary)] px-5 text-sm font-bold text-white shadow-sm hover:opacity-90 transition-colors">
            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Key
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">{{ session('error') }}</div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-bold uppercase tracking-wider text-[var(--dash-text-muted)]">
                    <tr>
                        <th class="px-6 py-4">Label</th>
                        <th class="px-6 py-4">Tipe Service</th>
                        <th class="px-6 py-4">Key Value</th>
                        <th class="px-6 py-4">Terakhir Dipakai</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($keys as $key)
                    <tbody x-data="quotaChecker('{{ route('system.api-keys.quota', $key) }}')" class="divide-y divide-[var(--dash-card-border)]">
                        <tr class="hover:bg-[var(--dash-page-bg)] transition-colors">
                            <td class="px-6 py-4 font-medium text-[var(--dash-text-heading)]">{{ $key->label }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $typeColors = [
                                        'convertapi' => 'bg-violet-100 text-violet-800',
                                        'telegram'   => 'bg-sky-100 text-sky-800',
                                        'wa'         => 'bg-green-100 text-green-800',
                                        'email'      => 'bg-orange-100 text-orange-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColors[$key->type] ?? 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]' }}">{{ $key->type }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <code class="rounded bg-[var(--dash-input-bg)] px-2 py-0.5 text-xs font-mono text-[var(--dash-text-muted)]">••••••••</code>
                            </td>
                            <td class="px-6 py-4 text-[var(--dash-text-muted)] text-xs">
                                {{ $key->last_used_at ? $key->last_used_at->diffForHumans() : 'Belum pernah' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($key->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                                @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($key->type === 'convertapi')
                                    <button @click="check()" :disabled="loading"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-[var(--dash-card-border)] px-3 py-1.5 text-xs font-semibold text-violet-600 bg-[var(--dash-card-bg)] transition-colors disabled:opacity-50 disabled:cursor-wait"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]" x-text="loading ? 'hourglass_empty' : 'analytics'">analytics</span>
                                        <span x-text="loading ? 'Memuat...' : 'Cek Quota'">Cek Quota</span>
                                    </button>
                                    @endif
                                    @can('system.api_key.update')
                                    <a href="{{ route('system.api-keys.edit', $key) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                    </a>
                                    <form method="POST" action="{{ route('system.api-keys.toggle', $key) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                                style="background-color: {{ $key->is_active ? '#dc2626' : '#059669' }};">
                                            <span class="material-symbols-outlined text-[14px]">swap_horiz</span>{{ $key->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                    @endcan
                                    @can('system.api_key.delete')
                                    <form method="POST" action="{{ route('system.api-keys.destroy', $key) }}" class="inline" onsubmit="return confirm('Hapus key ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: #dc2626;"
                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        {{-- Expandable quota row --}}
                        <tr x-show="result || error" x-cloak class="bg-violet-50/60">
                            <td colspan="6" class="px-6 py-3">
                                <template x-if="error">
                                    <p class="text-xs text-red-500 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">error</span>
                                        <span x-text="error"></span>
                                    </p>
                                </template>
                                <template x-if="result">
                                    <div class="flex flex-wrap items-center gap-6">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-violet-500 text-[16px]">timer</span>
                                            <span class="text-xs text-[var(--dash-text-muted)]">Sisa detik</span>
                                            <span class="text-sm font-bold text-violet-700" x-text="result.seconds_left ?? '—'"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[var(--dash-text-faint)] text-[16px]">swap_horiz</span>
                                            <span class="text-xs text-[var(--dash-text-muted)]">Konversi terpakai</span>
                                            <span class="text-sm font-bold text-[var(--dash-text-body)]" x-text="result.conversions_consumed ?? '—'"></span>
                                        </div>
                                        <template x-if="result.reset_date">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[var(--dash-text-faint)] text-[16px]">calendar_today</span>
                                                <span class="text-xs text-[var(--dash-text-muted)]">Reset</span>
                                                <span class="text-sm font-semibold text-[var(--dash-text-body)]" x-text="result.reset_date"></span>
                                            </div>
                                        </template>
                                        <template x-if="result.plan">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[var(--dash-text-faint)] text-[16px]">workspace_premium</span>
                                                <span class="text-xs text-[var(--dash-text-muted)]">Plan</span>
                                                <span class="text-sm font-semibold text-[var(--dash-text-body)] capitalize" x-text="result.plan"></span>
                                            </div>
                                        </template>
                                        <span class="text-xs text-[var(--dash-text-faint)] ml-auto">Data live dari ConvertAPI</span>
                                    </div>
                                </template>
                            </td>
                        </tr>
                    </tbody>
                    @empty
                    <tbody>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-[var(--dash-text-faint)]">Belum ada API key. Tambah key ConvertAPI untuk fitur docx → PDF.</td>
                        </tr>
                    </tbody>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>[x-cloak]{display:none!important}</style>
<script>
function quotaChecker(url) {
    return {
        loading: false,
        result: null,
        error: null,
        async check() {
            this.loading = true;
            this.result = null;
            this.error = null;
            try {
                const res = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (!res.ok) {
                    this.error = data.error ?? 'Gagal mengambil quota.';
                } else {
                    this.result = data;
                }
            } catch {
                this.error = 'Tidak dapat terhubung.';
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
@endsection
