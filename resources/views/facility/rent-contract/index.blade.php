@extends('layouts.dashboard')

@section('title', 'Kontrak Sewa Cabang')
@section('header_title', 'Kontrak Sewa Cabang')

@section('content')
@php
    $statusBadge = [
        'active'    => 'bg-emerald-100 text-emerald-800',
        'expired'   => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]',
        'terminated'=> 'bg-red-100 text-red-800',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Fasilitas</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Kontrak Sewa Cabang</h1>
        </div>
        @can('facility.rent_contract.create')
            <a href="{{ route('facility.rent-contracts.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">add</span> Buat Kontrak
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-hidden border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-nav-hover-bg)] text-left text-xs font-semibold text-[color:var(--dash-text-muted)]">
                    <tr>
                        <th class="px-5 py-3">Pemilik / Cabang</th>
                        <th class="px-5 py-3">Periode</th>
                        <th class="px-5 py-3">Total Sewa</th>
                        <th class="px-5 py-3">Termin</th>
                        <th class="px-5 py-3">DP</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[color:var(--dash-card-border)]">
                    @php $contracts = \App\Models\BranchRentContract::with('branch', 'termins')->latest()->paginate(20); @endphp
                    @forelse ($contracts as $contract)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-[color:var(--dash-text-heading)]">{{ $contract->owner_name }}</p>
                                <p class="mt-0.5 text-xs text-[color:var(--dash-text-muted)]">{{ $contract->branch?->branch_name ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 text-[color:var(--dash-text-body)]">
                                {{ $contract->start_date?->format('M Y') ?? '—' }} – {{ $contract->end_date?->format('M Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-[color:var(--dash-text-heading)]">Rp {{ number_format($contract->rent_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-[color:var(--dash-text-body)]">
                                {{ $contract->termins->where('status', 'paid')->count() }}/{{ $contract->termin_count }}
                            </td>
                            <td class="px-5 py-4 text-[color:var(--dash-text-body)]">Rp {{ number_format($contract->down_payment, 0, ',', '.') }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$contract->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' }}">{{ ucfirst($contract->status) }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('facility.rent-contracts.show', $contract) }}" class="inline-flex min-h-9 items-center border border-[color:var(--dash-input-border)] px-3 text-xs font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">description</span>
                                <p class="mt-2 font-semibold text-[color:var(--dash-text-body)]">Belum ada kontrak sewa.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($contracts->hasPages())
            <div class="border-t border-[color:var(--dash-card-border)] px-5 py-4">{{ $contracts->links() }}</div>
        @endif
    </div>
</div>
@endsection