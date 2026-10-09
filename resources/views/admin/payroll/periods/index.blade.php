@extends('layouts.dashboard')

@section('title', 'Periode Payroll')
@section('header_title', 'Periode Payroll')

@section('content')
@php
    $months = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $statusBadge = [
        'draft'     => 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]',
        'review'    => 'bg-amber-100 text-amber-800',
        'finalized' => 'bg-emerald-100 text-emerald-800',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6" x-data="{
        showConfirmModal: false,
        confirmActionUrl: '',
        confirmMethod: 'POST',
        confirmMessage: '',
        confirmTitle: '',
        confirmButtonText: 'Konfirmasi',
        confirmButtonClass: 'bg-red-600 hover:bg-red-700'
    }">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-[var(--dash-primary)]">Payroll</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Periode Payroll</h2>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-[var(--dash-text-body)]">Kelola periode penggajian. Generate payroll dilakukan per periode.</p>
        </div>
        @can('payroll.period.create')
            <button type="button" @click="
                    showConfirmModal = true;
                    confirmActionUrl = '{{ route('payroll.periods.auto-create') }}';
                    confirmMethod = 'POST';
                    confirmTitle = 'Buat Otomatis Periode';
                    confirmMessage = 'Buat otomatis semua periode tahun ini dan tahun depan?';
                    confirmButtonText = 'Buat Otomatis';
                    confirmButtonClass = 'bg-[var(--dash-primary)] hover:opacity-90';
                "
                class="inline-flex min-h-10 items-center gap-2 border border-[var(--dash-primary)] px-4 text-sm font-semibold text-[var(--dash-primary)] hover:bg-[var(--dash-input-bg)] rounded-lg">
                <span class="material-symbols-outlined text-[18px]">auto_fix_high</span>
                Buat Otomatis
            </button>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex gap-3 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Create Form --}}
    @can('payroll.period.create')
    <form method="POST" action="{{ route('payroll.periods.store') }}" class="border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5">
        @csrf
        <div class="mb-4 flex items-start gap-3">
            <span class="material-symbols-outlined mt-0.5 text-[var(--dash-primary)]">add_circle</span>
            <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)]">Buat periode baru</h3>
        </div>
        <div class="grid gap-4 sm:grid-cols-4">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Bulan</label>
                <select name="period_month" required class="bg-[var(--dash-card-bg)] text-[var(--dash-text-heading)] block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
                    @foreach (array_slice($months, 1, null, true) as $num => $label)
                        <option value="{{ $num }}" @selected(old('period_month') == $num)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Tahun</label>
                <input type="number" name="period_year" value="{{ old('period_year', now()->year) }}"
                       min="2020" class="block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-[var(--dash-text-body)]">Tanggal Gajian</label>
                <input type="date" name="pay_date" value="{{ old('pay_date') }}"
                       class="block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)]/30">
            </div>
            <div class="flex items-end">
                <button type="submit" class="inline-flex min-h-10 w-full items-center rounded-lg justify-center gap-2 bg-[var(--dash-primary)] px-4 text-sm font-semibold text-white hover:opacity-90">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Buat Periode
                </button>
            </div>
        </div>
    </form>
    @endcan

    {{-- Periods Table --}}
    <div class="overflow-hidden border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[var(--dash-card-border)] px-5 py-4">
            <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)]">Semua periode</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-semibold text-[var(--dash-text-muted)]">
                    <tr>
                        <th class="px-5 py-3">Periode</th>
                        <th class="px-5 py-3">Tanggal Gajian</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Pegawai</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)]">
                    @forelse ($periods as $period)
                        <tr class="hover:bg-[var(--dash-page-bg)]">
                            <td class="px-5 py-4 font-semibold text-[var(--dash-text-heading)]">
                                {{ $months[$period->period_month] }} {{ $period->period_year }}
                            </td>
                            <td class="px-5 py-4 text-[var(--dash-text-body)]">
                                {{ $period->pay_date?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[$period->status] ?? 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]' }}">
                                    {{ ucfirst($period->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-[var(--dash-text-body)]">
                                {{ $period->employeePayrolls->count() }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('payroll.periods.show', $period) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-[var(--dash-text-faint)]">calendar_month</span>
                                <p class="mt-2 font-semibold text-[var(--dash-text-body)]">Belum ada periode payroll.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Confirm Modal --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>
            <div class="relative z-10 inline-block align-bottom bg-[var(--dash-card-bg)] rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-[var(--dash-card-bg)] px-4 pt-5 pb-4 sm:p-6 sm:pb-4 rounded-t-lg">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                             :class="confirmMethod === 'DELETE' ? 'bg-red-100' : 'bg-blue-100'">
                            <span class="material-symbols-outlined"
                                  :class="confirmMethod === 'DELETE' ? 'text-red-600' : 'text-[var(--dash-primary)]'"
                                  x-text="confirmMethod === 'DELETE' ? 'warning' : 'help'"></span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-[var(--dash-text-heading)]" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-[var(--dash-text-muted)]" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-[var(--dash-page-bg)] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse rounded-b-lg">
                    <button type="button" @click="$refs.confirmForm.submit()"
                            :class="confirmButtonClass"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm"
                            x-text="confirmButtonText"></button>
                    <button type="button" @click="showConfirmModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-[var(--dash-card-border)] shadow-sm px-4 py-2 bg-[var(--dash-card-bg)] text-base font-medium text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
