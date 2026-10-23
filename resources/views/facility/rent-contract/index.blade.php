@extends('layouts.dashboard')

@section('title', 'Kontrak Sewa Cabang')
@section('header_title', 'Kontrak Sewa Cabang')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header (Pola A) --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Fasilitas</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kontrak Sewa Cabang</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola kontrak sewa gedung dan cabang beserta jadwal termin pembayaran.
            </p>
        </div>
        @can('facility.rent_contract.create')
            <a href="{{ route('facility.rent-contracts.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Buat Kontrak
            </a>
        @endcan
    </div>

    {{-- Flash Success Message --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Card Table --}}
    @php
        $statusBadges = [
            'active'     => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'Active'],
            'expired'    => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'dot' => 'bg-slate-400', 'label' => 'Expired'],
            'terminated' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500', 'label' => 'Terminated'],
        ];
        $contracts = \App\Models\BranchRentContract::with(['branch', 'termins'])->latest()->paginate(20);
    @endphp

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Pemilik / Cabang</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Periode Sewa</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Total Nilai Sewa</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Progres Termin</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Down Payment</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contracts as $contract)
                        @php
                            $paidCount = $contract->termins->where('status', 'paid')->count();
                            $statusInfo = $statusBadges[$contract->status] ?? [
                                'bg' => 'bg-slate-100',
                                'text' => 'text-slate-700',
                                'dot' => 'bg-slate-400',
                                'label' => ucfirst($contract->status ?? 'Unknown'),
                            ];
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-4">
                                <p class="font-semibold" style="color: var(--dash-text-heading);">
                                    {{ $contract->owner_name }}
                                </p>
                                <p class="mt-0.5 text-xs" style="color: var(--dash-text-muted);">
                                    {{ $contract->branch?->branch_name ?? '-' }}
                                </p>
                            </td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">
                                <span class="font-medium">
                                    {{ $contract->start_date?->format('d M Y') ?? '-' }}
                                </span>
                                <span class="mx-1" style="color: var(--dash-text-faint);">-</span>
                                <span class="font-medium">
                                    {{ $contract->end_date?->format('d M Y') ?? '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-semibold" style="color: var(--dash-text-heading);">
                                Rp {{ number_format($contract->rent_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                                      style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                                    <span class="material-symbols-outlined text-[14px]" style="color: var(--dash-primary);">payments</span>
                                    {{ $paidCount }} / {{ $contract->termin_count }} Lunas
                                </span>
                            </td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">
                                Rp {{ number_format($contract->down_payment, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusInfo['dot'] }}"></span>
                                    {{ $statusInfo['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('facility.rent-contracts.show', $contract) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">description</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
                                    Belum ada kontrak sewa.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($contracts->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection