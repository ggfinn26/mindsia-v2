@extends('layouts.dashboard')

@section('title', 'Pengaturan Ranking Rules MPI')
@section('header_title', 'Ranking Rules MPI')

@section('content')
<div x-data="{ activateModal: null }" class="space-y-6">

    {{-- Pola B3 Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('marketing-kpi.index') }}" 
                   class="dash-back inline-flex items-center gap-1 text-xs font-semibold transition-colors"
                   style="color: var(--dash-text-muted)">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Kembali ke KPI Snapshot
                </a>
            </div>
            <div class="text-xs font-semibold tracking-wider uppercase mb-1" style="color: var(--dash-primary-text)">Pemasaran</div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading)">Pengaturan Ranking Rules MPI</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted)">Kelola aturan dan metadata yang digunakan saat menghitung skor MPI marketing.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-2 border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-600 dark:text-emerald-400 rounded-xl">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-2 border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-600 dark:text-rose-400 rounded-xl">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Buat Rule Baru Card --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
        style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
        <div class="border-b px-5 py-4"
            style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg)">
            <h2 class="text-sm font-bold" style="color: var(--dash-text-heading)">Buat Rule Baru</h2>
        </div>
        <form method="POST" action="{{ route('marketing-kpi.rules.store') }}" class="p-5 sm:p-6 space-y-4">
            @csrf
            @if($errors->any())
                <div class="border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-xs sm:text-sm text-rose-600 dark:text-rose-400 rounded-xl">
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Kode Rule</label>
                    <input type="text" name="rule_code" value="{{ old('rule_code') }}" required maxlength="100"
                        placeholder="MPI_2025"
                        onfocus="this.style.borderColor='var(--dash-primary)'" 
                        onblur="this.style.borderColor='var(--dash-input-border)'"
                        class="block w-full rounded-lg border px-3 py-2 text-xs sm:text-sm uppercase outline-none transition-colors"
                        style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-heading)">
                    <p class="mt-1 text-[11px]" style="color: var(--dash-text-muted)">ID unik, huruf kapital.</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Nama Rule</label>
                    <input type="text" name="rule_name" value="{{ old('rule_name') }}" required maxlength="255"
                        placeholder="MPI Default (50% Kelas + 50% Omzet)"
                        onfocus="this.style.borderColor='var(--dash-primary)'" 
                        onblur="this.style.borderColor='var(--dash-input-border)'"
                        class="block w-full rounded-lg border px-3 py-2 text-xs sm:text-sm outline-none transition-colors"
                        style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-heading)">
                </div>
                <div>
                    <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted)">Basis Pendapatan</label>
                    <select name="revenue_basis" required
                        onfocus="this.style.borderColor='var(--dash-primary)'" 
                        onblur="this.style.borderColor='var(--dash-input-border)'"
                        class="block w-full rounded-lg border px-3 py-2 text-xs sm:text-sm outline-none transition-colors"
                        style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border); color: var(--dash-text-heading)">
                        <option value="registration_value" @selected(old('revenue_basis') === 'registration_value')>Nilai Pendaftaran</option>
                        <option value="cash_collected" @selected(old('revenue_basis') === 'cash_collected')>Cash Terkumpul</option>
                    </select>
                    <p class="mt-1 text-[11px]" style="color: var(--dash-text-muted)">Omzet yang dihitung untuk MPI.</p>
                </div>
            </div>
            <div class="pt-2">
                <button type="submit" 
                    class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-xs sm:text-sm font-semibold shadow-sm transition-opacity hover:opacity-90"
                    style="background-color: var(--dash-primary); color: #ffffff">
                    <span class="material-symbols-outlined text-[16px] mr-1">add</span>
                    Buat Rule
                </button>
            </div>
        </form>
    </div>

    {{-- Daftar Rules Card with Zero Horizontal Scroll --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
        style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
        <div class="flex items-center justify-between border-b px-5 py-4"
            style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg)">
            <h2 class="text-sm font-bold" style="color: var(--dash-text-heading)">Daftar Rules</h2>
            <span class="rounded-full border px-2.5 py-1 text-xs font-semibold"
                style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted)">
                {{ $rules->count() }} rule
            </span>
        </div>
        <div class="w-full">
            <table class="w-full text-sm text-left">
                <thead class="border-b text-[11px] font-semibold uppercase tracking-wider"
                    style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted)">
                    <tr>
                        <th class="px-5 py-3.5 w-32">Kode</th>
                        <th class="px-5 py-3.5">Nama & Basis Pendapatan</th>
                        <th class="px-5 py-3.5 text-center w-28">Status</th>
                        <th class="px-5 py-3.5 text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border)">
                    @forelse($rules as $rule)
                    <tr onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'" onmouseout="this.style.backgroundColor='transparent'" 
                        class="transition-colors">
                        <td class="px-5 py-3.5 font-mono text-xs font-semibold" style="color: var(--dash-text-heading)">
                            {{ $rule->rule_code }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="font-semibold" style="color: var(--dash-text-heading)">{{ $rule->rule_name }}</div>
                            <div class="text-xs mt-0.5" style="color: var(--dash-text-muted)">
                                Basis: {{ $rule->revenue_basis === 'registration_value' ? 'Nilai Pendaftaran' : 'Cash Terkumpul' }}
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($rule->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border"
                                    style="border-color: var(--dash-card-border); color: var(--dash-text-muted); background-color: var(--dash-nav-hover-bg)">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if(!$rule->is_active)
                                <button type="button" @click="activateModal = {{ $rule->id }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg border transition-colors hover:opacity-90"
                                    style="border-color: var(--dash-card-border); color: var(--dash-text-heading); background-color: var(--dash-card-bg)">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                    Aktifkan
                                </button>
                            @else
                                <span class="text-xs" style="color: var(--dash-text-muted)">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-16 text-center">
                            <span class="material-symbols-outlined text-4xl opacity-40 mb-2 block" style="color: var(--dash-text-faint)">tune</span>
                            <p class="font-semibold" style="color: var(--dash-text-heading)">Belum ada ranking rule.</p>
                            <p class="text-xs sm:text-sm mt-1" style="color: var(--dash-text-muted)">Buat rule di atas agar snapshot MPI bisa dibuat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Activation Confirmation Modals (tanpa overflow-hidden di card container) --}}
    @foreach($rules as $rule)
        @if(!$rule->is_active)
            <div x-show="activateModal === {{ $rule->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                <div @click.away="activateModal = null" class="w-full max-w-md rounded-2xl border shadow-xl"
                    style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border)">
                    <div class="px-6 py-5 border-b rounded-t-2xl flex items-center gap-3"
                        style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg)">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400">
                            <span class="material-symbols-outlined text-xl">tune</span>
                        </div>
                        <div>
                            <h3 class="text-base font-bold" style="color: var(--dash-text-heading)">Aktifkan Ranking Rule</h3>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted)">{{ $rule->rule_code }}</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-sm leading-relaxed" style="color: var(--dash-text-body)">
                            Apakah Anda yakin ingin mengaktifkan rule <strong style="color: var(--dash-text-heading)">{{ $rule->rule_name }}</strong>?
                        </p>
                        <p class="mt-2 text-xs" style="color: var(--dash-text-muted)">
                            Rule ini akan digunakan sebagai dasar perhitungan ranking KPI MPI selanjutnya.
                        </p>
                    </div>
                    <div class="px-6 py-4 border-t rounded-b-2xl flex items-center justify-end gap-3"
                        style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg)">
                        <button type="button" @click="activateModal = null" 
                            class="px-4 py-2 text-xs sm:text-sm font-medium rounded-lg border transition-colors"
                            style="border-color: var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg)">
                            Batal
                        </button>
                        <form method="POST" action="{{ route('marketing-kpi.rules.activate', $rule) }}" class="inline">
                            @csrf
                            <button type="submit" 
                                class="px-4 py-2 text-xs sm:text-sm font-semibold rounded-lg shadow-sm transition-opacity hover:opacity-90"
                                style="background-color: var(--dash-primary); color: #ffffff">
                                Ya, Aktifkan Rule
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

</div>
@endsection
