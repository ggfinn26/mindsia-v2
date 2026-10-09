@extends('layouts.dashboard')

@section('title', 'Detail Kontrak Sewa')
@section('header_title', 'Kontrak Sewa Cabang')

@section('content')
@php
    $terminStatusBadge = [
        'paid'    => 'bg-emerald-100 text-emerald-800',
        'unpaid'  => 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]',
        'overdue' => 'bg-red-100 text-red-800',
    ];
@endphp

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('facility.rent-contracts.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kontrak Sewa Cabang
            </a>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Kontrak — {{ $branchRentContract->owner_name }}</h1>
            <div class="mt-1 flex items-center gap-3">
                <p class="text-sm text-[color:var(--dash-text-muted)]">{{ $branchRentContract->branch?->branch_name ?? '—' }}</p>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $branchRentContract->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' }}">{{ ucfirst($branchRentContract->status) }}</span>
            </div>
        </div>
        @can('facility.rent_contract.edit')
            <a href="{{ route('facility.rent-contracts.edit', $branchRentContract) }}" class="inline-flex min-h-10 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">
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
    @php
        $paidTerminTotal = $branchRentContract->termins->where('status', 'paid')->sum('amount');
        $unpaidTerminTotal = $branchRentContract->termins->whereIn('status', ['unpaid', 'overdue'])->sum('amount');
        $totalSudahBayar = $branchRentContract->down_payment + $paidTerminTotal;
        $rentPeriodLabel = ['monthly' => 'Bulanan', 'yearly' => 'Tahunan', 'one_time' => 'Sekali Bayar'];
    @endphp
    <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 text-sm">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Pemilik</p><p class="mt-1 font-medium">{{ $branchRentContract->owner_name }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Telepon</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $branchRentContract->owner_phone ? '+62 ' . $branchRentContract->owner_phone : '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Cabang</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $branchRentContract->branch?->branch_name ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Periode</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $branchRentContract->start_date?->format('d M Y') ?? '—' }} – {{ $branchRentContract->end_date?->format('d M Y') ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Durasi Sewa</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $rentPeriodLabel[$branchRentContract->rent_period] ?? $branchRentContract->rent_period ?? '—' }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Total Sewa</p><p class="mt-1 font-semibold text-[color:var(--dash-text-heading)]">Rp {{ number_format($branchRentContract->rent_amount, 0, ',', '.') }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Down Payment</p><p class="mt-1 font-semibold text-[color:var(--dash-text-heading)]">Rp {{ number_format($branchRentContract->down_payment, 0, ',', '.') }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Per Termin</p><p class="mt-1 font-semibold text-[color:var(--dash-text-heading)]">Rp {{ number_format($branchRentContract->terminAmountPerCycle(), 0, ',', '.') }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Jumlah Termin</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $branchRentContract->termin_count }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Sudah Dibayar</p><p class="mt-1 font-semibold text-emerald-700">Rp {{ number_format($totalSudahBayar, 0, ',', '.') }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Sisa Pembayaran</p><p class="mt-1 font-semibold {{ $unpaidTerminTotal > 0 ? 'text-red-700' : 'text-[color:var(--dash-text-faint)]' }}">Rp {{ number_format($unpaidTerminTotal, 0, ',', '.') }}</p></div>
            <div><p class="text-xs text-[color:var(--dash-text-muted)]">Dibuat oleh</p><p class="mt-1 text-[color:var(--dash-text-body)]">{{ $branchRentContract->createdBy?->full_name ?? '—' }}</p></div>
        </div>
        @if ($branchRentContract->notes)
            <div class="mt-4 border-t border-[color:var(--dash-card-border)] pt-4">
                <p class="text-xs text-[color:var(--dash-text-muted)]">Catatan</p>
                <p class="mt-1 text-[color:var(--dash-text-body)] whitespace-pre-line">{{ $branchRentContract->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Termin Schedule --}}
    @if ($branchRentContract->termins->isNotEmpty())
    <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[color:var(--dash-card-border)] px-5 py-3 flex items-center justify-between">
            <h3 class="font-jakarta text-sm font-bold text-[color:var(--dash-text-heading)]">Jadwal Termin</h3>
            <p class="text-xs text-[color:var(--dash-text-muted)]">{{ $branchRentContract->termins->where('status', 'paid')->count() }} / {{ $branchRentContract->termin_count }} lunas</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-nav-hover-bg)] text-left text-xs font-semibold text-[color:var(--dash-text-muted)]">
                    <tr>
                        <th class="px-5 py-3">Termin</th>
                        <th class="px-5 py-3">Jatuh Tempo</th>
                        <th class="px-5 py-3">Jumlah</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Dibayar</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[color:var(--dash-card-border)]">
                    @foreach ($branchRentContract->termins as $termin)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-3 font-semibold text-[color:var(--dash-text-heading)]">{{ $termin->termin_number }}</td>
                            <td class="px-5 py-3 text-[color:var(--dash-text-body)]">{{ $termin->due_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-3 font-semibold text-[color:var(--dash-text-heading)]">Rp {{ number_format($termin->amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $terminStatusBadge[$termin->status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' }}">{{ ucfirst($termin->status) }}</span>
                            </td>
                            <td class="px-5 py-3 text-[color:var(--dash-text-body)]">
                                @if ($termin->paid_at)
                                    {{ $termin->paid_at->format('d M Y') }}
                                    <p class="text-xs text-[color:var(--dash-text-faint)]">oleh {{ $termin->paidBy?->full_name }}</p>
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