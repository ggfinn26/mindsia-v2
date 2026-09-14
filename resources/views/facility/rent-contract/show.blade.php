@extends('layouts.dashboard')

@section('title', 'Detail Kontrak Sewa')
@section('header_title', 'Kontrak Sewa Cabang')

@section('content')
@php
    $terminStatusBadge = [
        'paid'    => 'bg-emerald-100 text-emerald-800',
        'unpaid'  => 'bg-slate-100 text-slate-600',
        'overdue' => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('facility.rent-contracts.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kontrak Sewa Cabang
            </a>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Kontrak — {{ $branchRentContract->owner_name }}</h2>
            <div class="mt-1 flex items-center gap-3">
                <p class="text-sm text-slate-500">{{ $branchRentContract->branch?->branch_name ?? '—' }}</p>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $branchRentContract->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ ucfirst($branchRentContract->status) }}</span>
            </div>
        </div>
        @can('facility.rent_contract.edit')
            <a href="{{ route('facility.rent-contracts.edit', $branchRentContract) }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <span class="material-symbols-outlined text-[18px]">edit</span> Edit
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Contract Info --}}
    <div class="border border-slate-200 bg-white p-5 text-sm">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            <div><p class="text-xs text-slate-500">Pemilik</p><p class="mt-1 font-medium">{{ $branchRentContract->owner_name }}</p></div>
            <div><p class="text-xs text-slate-500">Telepon</p><p class="mt-1 text-slate-700">{{ $branchRentContract->owner_phone ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Cabang</p><p class="mt-1 text-slate-700">{{ $branchRentContract->branch?->branch_name ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Area</p><p class="mt-1 text-slate-700">{{ $branchRentContract->area?->area_name ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Periode</p><p class="mt-1 text-slate-700">{{ $branchRentContract->start_date?->format('d M Y') ?? '—' }} – {{ $branchRentContract->end_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Durasi Sewa</p><p class="mt-1 text-slate-700">{{ $branchRentContract->rent_period ?? '—' }}</p></div>
            <div><p class="text-xs text-slate-500">Total Sewa</p><p class="mt-1 font-semibold text-slate-900">Rp {{ number_format($branchRentContract->rent_amount, 0, ',', '.') }}</p></div>
            <div><p class="text-xs text-slate-500">Down Payment</p><p class="mt-1 font-semibold text-slate-900">Rp {{ number_format($branchRentContract->down_payment, 0, ',', '.') }}</p></div>
            <div><p class="text-xs text-slate-500">Per Termin</p><p class="mt-1 font-semibold text-slate-900">Rp {{ number_format($branchRentContract->terminAmountPerCycle(), 0, ',', '.') }}</p></div>
            <div><p class="text-xs text-slate-500">Jumlah Termin</p><p class="mt-1 text-slate-700">{{ $branchRentContract->termin_count }}</p></div>
            <div><p class="text-xs text-slate-500">Dibuat oleh</p><p class="mt-1 text-slate-700">{{ $branchRentContract->createdBy?->full_name ?? '—' }}</p></div>
        </div>
        @if ($branchRentContract->notes)
            <div class="mt-4 border-t border-slate-100 pt-4">
                <p class="text-xs text-slate-500">Catatan</p>
                <p class="mt-1 text-slate-700 whitespace-pre-line">{{ $branchRentContract->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Termin Schedule --}}
    @if ($branchRentContract->termins->isNotEmpty())
    <div class="border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-5 py-3 flex items-center justify-between">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Jadwal Termin</h3>
            <p class="text-xs text-slate-500">{{ $branchRentContract->termins->where('status', 'paid')->count() }} / {{ $branchRentContract->termin_count }} lunas</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Termin</th>
                        <th class="px-5 py-3">Jatuh Tempo</th>
                        <th class="px-5 py-3">Jumlah</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Dibayar</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($branchRentContract->termins as $termin)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-3 font-semibold text-slate-900">{{ $termin->termin_number }}</td>
                            <td class="px-5 py-3 text-slate-600">{{ $termin->due_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-3 font-semibold text-slate-900">Rp {{ number_format($termin->amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $terminStatusBadge[$termin->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($termin->status) }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-600">
                                @if ($termin->paid_at)
                                    {{ $termin->paid_at->format('d M Y') }}
                                    <p class="text-xs text-slate-400">oleh {{ $termin->paidBy?->full_name }}</p>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                @if ($termin->status !== 'paid')
                                    @can('facility.rent_contract.mark_paid')
                                        <form method="POST" action="{{ route('facility.rent-contracts.termins.mark-paid', [$branchRentContract, $termin]) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex min-h-8 items-center bg-emerald-600 px-2.5 text-xs font-semibold text-white hover:bg-emerald-700">Lunas</button>
                                        </form>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection