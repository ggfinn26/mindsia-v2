@extends('layouts.dashboard')

@section('title', 'Kontrak Sewa Cabang')
@section('header_title', 'Kontrak Sewa Cabang')

@section('content')
@php
    $statusBadge = [
        'active'    => 'bg-emerald-100 text-emerald-800',
        'expired'   => 'bg-slate-100 text-slate-600',
        'terminated'=> 'bg-red-100 text-red-800',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Fasilitas</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Kontrak Sewa Cabang</h2>
        </div>
        @can('facility.rent_contract.create')
            <a href="{{ route('facility.rent-contracts.create') }}" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
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

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
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
                <tbody class="divide-y divide-slate-100">
                    @php $contracts = \App\Models\BranchRentContract::with('branch', 'termins')->latest()->paginate(20); @endphp
                    @forelse ($contracts as $contract)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $contract->owner_name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $contract->branch?->branch_name ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ $contract->start_date?->format('M Y') ?? '—' }} – {{ $contract->end_date?->format('M Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-900">Rp {{ number_format($contract->rent_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-slate-700">
                                {{ $contract->termins->where('status', 'paid')->count() }}/{{ $contract->termin_count }}
                            </td>
                            <td class="px-5 py-4 text-slate-600">Rp {{ number_format($contract->down_payment, 0, ',', '.') }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$contract->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($contract->status) }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('facility.rent-contracts.show', $contract) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">description</span>
                                <p class="mt-2 font-semibold text-slate-700">Belum ada kontrak sewa.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($contracts->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $contracts->links() }}</div>
        @endif
    </div>
</div>
@endsection