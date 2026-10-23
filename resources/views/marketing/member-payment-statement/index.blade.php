@extends('layouts.dashboard')

@section('title', 'Financial Statement Marketing - MINDSIA')
@section('header_title', 'Financial Statement')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Pemasaran</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Financial Statement Marketing</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Data pembayaran member per program. Read-only - menampilkan status cicilan, DP, dan kewajiban pembayaran.
            </p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filter Card --}}
    <form method="GET" class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @can('marketing.member_payment_statement.view_branch')
            <div class="w-full sm:w-48">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Cabang</label>
                <select name="branch_id"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Cabang</option>
                    @foreach (\App\Models\Branch::where('is_active', true)->orderBy('branch_name')->get() as $branch)
                        <option value="{{ $branch->id }}" @selected(request('branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                    @endforeach
                </select>
            </div>
        @endcan
        <div class="w-full sm:w-48">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Urutkan</label>
            <select name="sort"
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="member_name" @selected($sort === 'member_name')>Nama member</option>
                <option value="institution" @selected($sort === 'institution')>Institusi</option>
                <option value="payment_status" @selected($sort === 'payment_status')>Status pembayaran</option>
            </select>
        </div>
        <div class="w-full sm:w-36">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Arah</label>
            <select name="direction"
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="asc" @selected($direction === 'asc')>Naik (A - Z)</option>
                <option value="desc" @selected($direction === 'desc')>Turun (Z - A)</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                Terapkan
            </button>
            @if(request()->hasAny(['branch_id', 'sort', 'direction']))
                <a href="{{ route('marketing.member-payment-statement') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Table Card (Fullpage - Zero Horizontal Scroll) --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Data Pembayaran Member</h2>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="background-color: var(--dash-card-border); color: var(--dash-text-muted);">{{ $registrations->total() }} pendaftaran</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Member & Kontak</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Program</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">DP & Cicilan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Pembayaran</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide" style="color: var(--dash-text-muted);">Status</th>
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

                            $statusClasses = match ($registration->payment_status) {
                                'paid', 'lunas' => 'bg-emerald-100 text-emerald-800',
                                'partial', 'installment' => 'bg-amber-100 text-amber-800',
                                default => 'bg-gray-100 text-gray-700',
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
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3.5">
                                <p class="font-bold text-sm" style="color: var(--dash-text-heading);">{{ $member?->full_name ?? '-' }}</p>
                                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                    {{ $member?->institution?->institution_name ?? $member?->institution_name_custom ?? '-' }}
                                    @if($member?->whatsapp_number)
                                        · {{ $member->whatsapp_number }}
                                    @endif
                                </p>
                            </td>
                            <td class="px-4 py-3.5" style="color: var(--dash-text-body);">
                                <p class="font-medium text-sm" style="color: var(--dash-text-heading);">{{ $registration->program?->program_name ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3.5" style="color: var(--dash-text-body);">
                                <div class="text-xs">
                                    <span class="font-semibold" style="color: var(--dash-text-heading);">DP:</span>
                                    @if ($dp && $dp->payment_status === 'paid')
                                        Rp {{ number_format($dp->amount, 0, ',', '.') }}
                                    @else
                                        <span style="color: var(--dash-text-faint);">-</span>
                                    @endif
                                    <span class="mx-1" style="color: var(--dash-card-border);">|</span>
                                    <span>Cicilan {{ $paidInstallments }}/{{ $totalInstallments }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 text-right tabular-nums">
                                <p class="font-bold text-sm" style="color: var(--dash-text-heading);">
                                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                                </p>
                                <p class="text-xs mt-0.5 {{ $remaining > 0 ? 'text-red-600 font-semibold' : '' }}" style="{{ $remaining <= 0 ? 'color: var(--dash-text-muted);' : '' }}">
                                    Sisa: Rp {{ number_format($remaining, 0, ',', '.') }}
                                </p>
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold {{ $statusClasses }}">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">receipt_long</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Tidak ada data pembayaran.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($registrations->hasPages())
            <div class="px-5 py-4 border-t" style="border-color: var(--dash-card-border);">
                {{ $registrations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
