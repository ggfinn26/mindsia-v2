@extends('layouts.dashboard')

@section('title', 'Pengaturan Ranking Rules MPI')
@section('header_title', 'Ranking Rules MPI')

@section('content')
<div class="w-full space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="font-jakarta text-xl font-bold tracking-tight text-[color:var(--dash-text-heading)]">Pengaturan Ranking Rules MPI</h2>
            <p class="mt-1 text-sm text-[color:var(--dash-text-muted)]">Kelola aturan dan metadata yang digunakan saat menghitung skor MPI marketing.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 rounded-xl">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        <p>{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-2 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 rounded-xl">
        <span class="material-symbols-outlined text-[18px]">error</span>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    {{-- Buat Rule Baru --}}
    <div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)]">
        <div class="border-b border-[color:var(--dash-card-border)] px-5 py-4 bg-slate-50/50 rounded-t-xl">
            <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Buat Rule Baru</h3>
        </div>
        <form method="POST" action="{{ route('marketing-kpi.rules.store') }}" class="p-5 space-y-4">
            @csrf
            @if($errors->any())
            <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 rounded-lg">
                <ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="mb-1.5 block text-[11px] font-semibold text-[color:var(--dash-text-faint)] uppercase tracking-wide">Kode Rule</label>
                    <input type="text" name="rule_code" value="{{ old('rule_code') }}" required maxlength="100"
                        placeholder="MPI_2025"
                        class="block w-full bg-[var(--dash-nav-hover-bg)] border border-[color:var(--dash-card-border)] rounded-lg text-sm px-3 py-2 uppercase outline-none focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)] transition-colors">
                    <p class="mt-1 text-xs text-[color:var(--dash-text-faint)]">ID unik, huruf kapital.</p>
                </div>
                <div>
                    <label class="mb-1.5 block text-[11px] font-semibold text-[color:var(--dash-text-faint)] uppercase tracking-wide">Nama Rule</label>
                    <input type="text" name="rule_name" value="{{ old('rule_name') }}" required maxlength="255"
                        placeholder="MPI Default (50% Kelas + 50% Omzet)"
                        class="block w-full bg-[var(--dash-nav-hover-bg)] border border-[color:var(--dash-card-border)] rounded-lg text-sm px-3 py-2 outline-none focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)] transition-colors">
                </div>
                <div>
                    <label class="mb-1.5 block text-[11px] font-semibold text-[color:var(--dash-text-faint)] uppercase tracking-wide">Basis Pendapatan</label>
                    <select name="revenue_basis" required
                        class="block w-full bg-[var(--dash-nav-hover-bg)] border border-[color:var(--dash-card-border)] rounded-lg text-sm px-3 py-2 outline-none focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)] transition-colors">
                        <option value="registration_value" @selected(old('revenue_basis') === 'registration_value')>Nilai Pendaftaran</option>
                        <option value="cash_collected" @selected(old('revenue_basis') === 'cash_collected')>Cash Terkumpul</option>
                    </select>
                    <p class="mt-1 text-xs text-[color:var(--dash-text-faint)]">Omzet yang dihitung untuk MPI.</p>
                </div>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[var(--dash-primary,#215aac)] rounded-lg hover:bg-[#194a91] transition-colors">
                    Buat Rule
                </button>
            </div>
        </form>
    </div>

    {{-- Daftar Rules --}}
    <div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)]">
        <div class="border-b border-[color:var(--dash-card-border)] px-5 py-4 flex items-center justify-between bg-slate-50/50 rounded-t-xl">
            <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Daftar Rules</h3>
            <span class="px-2.5 py-1 bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)] text-xs font-semibold rounded-full">{{ $rules->count() }} rule</span>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-[var(--dash-card-bg)] text-[color:var(--dash-text-muted)] border-b border-[color:var(--dash-card-border)]">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">Kode</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">Nama</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">Basis Pendapatan</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide">Status</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wide"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($rules as $rule)
                    <tr class="hover:bg-slate-50/70 transition-colors {{ $rule->is_active ? 'bg-blue-50/20' : '' }}">
                        <td class="px-5 py-4 font-mono text-xs text-[color:var(--dash-text-body)]">{{ $rule->rule_code }}</td>
                        <td class="px-5 py-4 font-medium text-[color:var(--dash-text-heading)]">{{ $rule->rule_name }}</td>
                        <td class="px-5 py-4 text-[color:var(--dash-text-muted)]">
                            {{ $rule->revenue_basis === 'registration_value' ? 'Nilai Pendaftaran' : 'Cash Terkumpul' }}
                        </td>
                        <td class="px-5 py-4">
                            @if($rule->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-muted)] text-xs font-semibold rounded-full">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if(!$rule->is_active)
                            <form method="POST" action="{{ route('marketing-kpi.rules.activate', $rule) }}">
                                @csrf
                                <button type="submit" class="text-[color:var(--dash-primary-text,#215aac)] text-xs font-medium hover:underline">
                                    Aktifkan
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <span class="material-symbols-outlined text-4xl text-slate-200 mb-2 block">tune</span>
                            <p class="font-semibold text-[color:var(--dash-text-body)]">Belum ada ranking rule.</p>
                            <p class="text-sm text-[color:var(--dash-text-faint)] mt-1">Buat rule di atas agar snapshot MPI bisa dibuat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
