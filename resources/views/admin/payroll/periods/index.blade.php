@extends('layouts.dashboard')

@section('title', 'Kelola Periode Payroll - MINDSIA')
@section('header_title', 'Kelola Periode Payroll')

@section('content')
@php
    $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
        showConfirmModal: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmActionUrl: '',
        confirmMethod: 'POST',
        confirmActionText: 'Lanjutkan',
        openConfirm(title, message, url, method = 'POST', actionText = 'Lanjutkan') {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmActionUrl = url;
            this.confirmMethod = method;
            this.confirmActionText = actionText;
            this.showConfirmModal = true;
        }
     }">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Gaji & Bonus</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kelola Periode Payroll</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola periode penggajian pegawai. Generate dan finalisasi payroll dilakukan per periode.
            </p>
        </div>
        @can('payroll.period.create')
            <div class="flex items-center gap-2">
                <button type="button"
                        @click="openConfirm('Buat Otomatis Periode', 'Apakah Anda yakin ingin membuat otomatis semua periode payroll tahun ini dan tahun depan?', '{{ route('payroll.periods.auto-create') }}', 'POST', 'Buat Otomatis')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors"
                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-primary-text);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[18px]">auto_fix_high</span>
                    Buat Otomatis
                </button>
            </div>
        @endcan
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <div class="flex items-center gap-2 mb-2 font-semibold">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>Terdapat kesalahan pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Create Form Card --}}
    @can('payroll.period.create')
    <div class="rounded-2xl border shadow-sm"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center gap-2 rounded-t-2xl"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">add_circle</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Buat Periode Baru</h2>
        </div>

        <form method="POST" action="{{ route('payroll.periods.store') }}" class="p-6">
            @csrf
            <div class="grid gap-4 sm:grid-cols-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                           style="color: var(--dash-text-body);">
                        Bulan <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <select name="period_month" required
                            class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all cursor-pointer"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="" disabled {{ old('period_month') ? '' : 'selected' }}>Pilih Bulan</option>
                        @foreach (array_slice($months, 1, null, true) as $num => $label)
                            <option value="{{ $num }}" @selected(old('period_month') == $num)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                           style="color: var(--dash-text-body);">
                        Tahun <span style="color: var(--dash-danger);">*</span>
                    </label>
                    <input type="number" name="period_year" value="{{ old('period_year', now()->year) }}"
                           min="2020" required
                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                           style="color: var(--dash-text-body);">
                        Tanggal Gajian
                    </label>
                    <input type="date" name="pay_date" value="{{ old('pay_date') }}"
                           class="w-full text-sm px-3.5 py-2.5 rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Buat Periode
                    </button>
                </div>
            </div>
        </form>
    </div>
    @endcan

    {{-- Periods Table Card --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary-text);">calendar_month</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Semua Periode Payroll</h2>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                {{ $periods->count() }} Periode
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Periode</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Tanggal Gajian</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Pegawai Terdaftar</th>
                        <th class="px-6 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse ($periods as $period)
                        <tr class="transition-colors"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-6 py-4 font-semibold"
                                style="color: var(--dash-text-heading);">
                                {{ $months[$period->period_month] ?? $period->period_month }} {{ $period->period_year }}
                            </td>
                            <td class="px-6 py-4"
                                style="color: var(--dash-text-body);">
                                {{ $period->pay_date?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($period->status === 'finalized')
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                        Finalized
                                    </span>
                                @elseif ($period->status === 'review')
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
                                        <span class="material-symbols-outlined text-[14px]">hourglass_top</span>
                                        Review
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold"
                                          style="background-color: var(--dash-status-default-bg); color: var(--dash-status-default-text);">
                                        <span class="material-symbols-outlined text-[14px]">edit_note</span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4"
                                style="color: var(--dash-text-body);">
                                {{ $period->employeePayrolls->count() }} orang
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('payroll.periods.show', $period) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
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
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="material-symbols-outlined text-4xl mb-2"
                                          style="color: var(--dash-text-faint);">calendar_month</span>
                                    <p class="font-semibold" style="color: var(--dash-text-heading);">Belum ada periode payroll.</p>
                                    <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                                        Buat periode baru menggunakan formulir di atas atau gunakan tombol Buat Otomatis.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Custom Alpine Confirmation Modal --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="showConfirmModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                            <span class="material-symbols-outlined text-[24px]">help</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showConfirmModal = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="button" @click="$refs.confirmForm.submit()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);"
                            x-text="confirmActionText">
                    </button>
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
