@extends('layouts.dashboard')

@section('title', 'Financial Statement Marketing')
@section('header_title', 'Financial Statement')

@section('content')
@php
    $nextDir = function($col) use ($sort, $direction) {
        if ($sort !== $col) return 'asc';
        return $direction === 'asc' ? 'desc' : 'asc';
    };
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Financial Statement Marketing</h1>
            <p class="mt-1 max-w-3xl text-sm" style="color: var(--dash-text-muted);">Data pembayaran member per program. Read-only: menampilkan status cicilan, DP, dan kewajiban pembayaran.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: var(--dash-status-success-bg); border-color: var(--dash-card-border); color: var(--dash-status-success-text);">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: var(--dash-status-danger-bg); border-color: var(--dash-card-border); color: var(--dash-status-danger-text);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters --}}
    <div class="rounded-2xl px-5 py-4"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" action="{{ route('marketing.member-payment-statement') }}" class="flex flex-wrap items-end gap-3">
            @can('marketing.member_payment_statement.view_branch')
                <div class="w-full sm:w-auto min-w-[200px]">
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-muted);">Cabang</label>
                    <div class="relative">
                        <select name="branch_id"
                                class="w-full appearance-none rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                            <option value="">Semua cabang</option>
                            @foreach (\App\Models\Branch::where('is_active', true)->orderBy('branch_name')->get() as $branch)
                                <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-2 text-[18px] pointer-events-none" style="color: var(--dash-text-muted);">arrow_drop_down</span>
                    </div>
                </div>
            @endcan

            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="direction" value="{{ $direction }}">

            <div class="w-full sm:w-auto min-w-[200px]">
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-muted);">Program</label>
                <div class="relative">
                    <select name="program_id"
                            class="w-full appearance-none rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        <option value="">Semua program</option>
                        @foreach (\App\Models\Program::orderBy('program_name')->get() as $program)
                            <option value="{{ $program->id }}" @selected(request('program_id') == $program->id)>{{ $program->program_name }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-2 text-[18px] pointer-events-none" style="color: var(--dash-text-muted);">arrow_drop_down</span>
                </div>
            </div>

            <div class="w-full sm:w-auto min-w-[160px]">
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-muted);">Status Pembayaran</label>
                <div class="relative">
                    <select name="payment_status"
                            class="w-full appearance-none rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        <option value="">Semua status</option>
                        <option value="paid" @selected(request('payment_status') === 'paid')>Lunas</option>
                        <option value="installment" @selected(request('payment_status') === 'installment')>Cicilan</option>
                        <option value="partial" @selected(request('payment_status') === 'partial')>Sebagian</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-2 text-[18px] pointer-events-none" style="color: var(--dash-text-muted);">arrow_drop_down</span>
                </div>
            </div>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity h-[38px]"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">filter_list</span> Terapkan
            </button>

            @if(request()->anyFilled(['branch_id', 'program_id', 'payment_status', 'sort', 'direction']) && (request('sort') !== 'member_name' || request('direction') !== 'asc' || request('branch_id') || request('program_id') || request('payment_status')))
                <a href="{{ route('marketing.member-payment-statement') }}" class="text-sm font-semibold hover:underline px-2 py-2 h-[38px] inline-flex items-center border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)] gap-1.5" style="color: var(--dash-primary-text);">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Data Pembayaran Member</h2>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full" style="background-color: var(--dash-card-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">
                {{ $registrations->total() }} Pendaftaran
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <tr>
                        <th class="px-5 py-3 text-left w-1/5 min-w-[150px]">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'member_name', 'direction' => $nextDir('member_name'), 'page' => 1]) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider hover:opacity-70 transition-opacity"
                               style="color: var(--dash-text-muted);">
                                Nama Member
                                <span class="material-symbols-outlined text-[14px]">
                                    @if($sort === 'member_name' && $direction === 'asc') arrow_upward
                                    @elseif($sort === 'member_name' && $direction === 'desc') arrow_downward
                                    @else unfold_more @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left w-1/5 min-w-[150px]">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'institution', 'direction' => $nextDir('institution'), 'page' => 1]) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider hover:opacity-70 transition-opacity"
                               style="color: var(--dash-text-muted);">
                                Institusi
                                <span class="material-symbols-outlined text-[14px]">
                                    @if($sort === 'institution' && $direction === 'asc') arrow_upward
                                    @elseif($sort === 'institution' && $direction === 'desc') arrow_downward
                                    @else unfold_more @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left w-1/5 min-w-[150px]">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'program', 'direction' => $nextDir('program'), 'page' => 1]) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider hover:opacity-70 transition-opacity"
                               style="color: var(--dash-text-muted);">
                                Program
                                <span class="material-symbols-outlined text-[14px]">
                                    @if($sort === 'program' && $direction === 'asc') arrow_upward
                                    @elseif($sort === 'program' && $direction === 'desc') arrow_downward
                                    @else unfold_more @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'whatsapp', 'direction' => $nextDir('whatsapp'), 'page' => 1]) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider hover:opacity-70 transition-opacity"
                               style="color: var(--dash-text-muted);">
                                WhatsApp
                                <span class="material-symbols-outlined text-[14px]">
                                    @if($sort === 'whatsapp' && $direction === 'asc') arrow_upward
                                    @elseif($sort === 'whatsapp' && $direction === 'desc') arrow_downward
                                    @else unfold_more @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'dp', 'direction' => $nextDir('dp'), 'page' => 1]) }}"
                               class="inline-flex items-center justify-end gap-1 text-xs font-semibold uppercase tracking-wider hover:opacity-70 transition-opacity w-full"
                               style="color: var(--dash-text-muted);">
                                DP
                                <span class="material-symbols-outlined text-[14px]">
                                    @if($sort === 'dp' && $direction === 'asc') arrow_upward
                                    @elseif($sort === 'dp' && $direction === 'desc') arrow_downward
                                    @else unfold_more @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-5 py-3 text-center whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'cicilan', 'direction' => $nextDir('cicilan'), 'page' => 1]) }}"
                               class="inline-flex items-center justify-center gap-1 text-xs font-semibold uppercase tracking-wider hover:opacity-70 transition-opacity w-full"
                               style="color: var(--dash-text-muted);">
                                Cicilan
                                <span class="material-symbols-outlined text-[14px]">
                                    @if($sort === 'cicilan' && $direction === 'asc') arrow_upward
                                    @elseif($sort === 'cicilan' && $direction === 'desc') arrow_downward
                                    @else unfold_more @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'total_dibayar', 'direction' => $nextDir('total_dibayar'), 'page' => 1]) }}"
                               class="inline-flex items-center justify-end gap-1 text-xs font-semibold uppercase tracking-wider hover:opacity-70 transition-opacity w-full"
                               style="color: var(--dash-text-muted);">
                                Total Dibayar
                                <span class="material-symbols-outlined text-[14px]">
                                    @if($sort === 'total_dibayar' && $direction === 'asc') arrow_upward
                                    @elseif($sort === 'total_dibayar' && $direction === 'desc') arrow_downward
                                    @else unfold_more @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'sisa', 'direction' => $nextDir('sisa'), 'page' => 1]) }}"
                               class="inline-flex items-center justify-end gap-1 text-xs font-semibold uppercase tracking-wider hover:opacity-70 transition-opacity w-full"
                               style="color: var(--dash-text-muted);">
                                Sisa
                                <span class="material-symbols-outlined text-[14px]">
                                    @if($sort === 'sisa' && $direction === 'asc') arrow_upward
                                    @elseif($sort === 'sisa' && $direction === 'desc') arrow_downward
                                    @else unfold_more @endif
                                </span>
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left whitespace-nowrap">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'payment_status', 'direction' => $nextDir('payment_status'), 'page' => 1]) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider hover:opacity-70 transition-opacity"
                               style="color: var(--dash-text-muted);">
                                Status
                                <span class="material-symbols-outlined text-[14px]">
                                    @if($sort === 'payment_status' && $direction === 'asc') arrow_upward
                                    @elseif($sort === 'payment_status' && $direction === 'desc') arrow_downward
                                    @else unfold_more @endif
                                </span>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registrations as $registration)
                        @php
                            $member = $registration->memberData;
                            $totalPaid = $registration->payments->where('payment_status', 'paid')->sum('amount');
                            $remaining = max(0, ($registration->final_price ?? 0) - $totalPaid);
                            $totalInstallments = $registration->payments->count();
                            $paidInstallments = $registration->payments->where('payment_status', 'paid')->count();

                            $statusStyles = match ($registration->payment_status) {
                                'paid', 'lunas' => 'background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);',
                                'partial', 'installment' => 'background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);',
                                default => 'background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);',
                            };
                            $statusLabel = match ($registration->payment_status) {
                                'paid', 'lunas' => 'Lunas',
                                'partial', 'installment' => 'Cicil',
                                default => ucfirst($registration->payment_status),
                            };

                            $dp = $registration->payments->where('installment_number', 1)->first();
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">{{ $member?->full_name ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm" style="color: var(--dash-text-muted);">
                                {{ $member?->institution?->institution_name ?? $member?->institution_name_custom ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-sm" style="color: var(--dash-text-muted);">
                                {{ $registration->program?->program_name ?? '-' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-sm" style="color: var(--dash-text-muted);">
                                {{ $member?->whatsapp_number ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums whitespace-nowrap text-sm" style="color: var(--dash-text-body);">
                                @if ($dp && $dp->payment_status === 'paid')
                                    Rp {{ number_format($dp->amount, 0, ',', '.') }}
                                @else
                                    <span style="color: var(--dash-text-faint);">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center tabular-nums whitespace-nowrap text-sm" style="color: var(--dash-text-body);">
                                {{ $paidInstallments }}/{{ $totalInstallments }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums whitespace-nowrap font-semibold" style="color: var(--dash-text-heading);">
                                Rp {{ number_format($totalPaid, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 text-right tabular-nums whitespace-nowrap font-semibold" style="{{ $remaining > 0 ? 'color: var(--dash-status-danger-text);' : 'color: var(--dash-text-muted);' }}">
                                Rp {{ number_format($remaining, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold" style="{{ $statusStyles }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">receipt_long</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data pembayaran member.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($registrations->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
