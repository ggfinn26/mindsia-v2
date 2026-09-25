@extends('layouts.dashboard')

@section('title', 'Kelola Kontrak Karyawan')
@section('header_title', 'Kelola Kontrak Karyawan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Kelola Kontrak Karyawan</h2>
            <p class="mt-1 text-sm text-slate-500">Daftar karyawan aktif beserta status kontrak dan tindakan yang tersedia.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Tabel kontrak --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Daftar Kontrak Aktif</h3>
        </div>

        @if ($employees->isEmpty())
            <div class="px-5 py-12 text-center">
                <span class="material-symbols-outlined text-3xl text-slate-300">work_history</span>
                <p class="mt-2 text-sm font-medium text-slate-600">Belum ada karyawan dengan kontrak aktif.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3 text-left">Karyawan</th>
                            <th class="px-5 py-3 text-left">Posisi</th>
                            <th class="px-5 py-3 text-left">Tipe</th>
                            <th class="px-5 py-3 text-left">Masa Kontrak</th>
                            <th class="px-5 py-3 text-left">Status</th>
                            <th class="px-5 py-3 text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($employees as $employee)
                            @php
                                $status = $employee->currentStatus;
                                $expiring = $status?->contract_end_date && $status->contract_end_date->lte(now()->addDays(30));
                                $expired  = $status?->contract_end_date && $status->contract_end_date->lt(now());
                                $hasPendingOffer = $status?->extendOffer?->status === 'pending';
                            @endphp
                            <tr class="{{ $expired ? 'bg-rose-50/30' : ($expiring ? 'bg-amber-50/30' : '') }}">
                                <td class="px-5 py-3">
                                    <a href="{{ route('employees.show', $employee) }}" class="font-medium text-slate-900 hover:text-[#215aac] hover:underline">
                                        {{ $employee->full_name }}
                                    </a>
                                    <p class="font-mono text-xs text-slate-400">{{ $employee->employee_code }}</p>
                                </td>
                                <td class="px-5 py-3 text-slate-600">
                                    {{ $status?->position?->position_name ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-slate-600">
                                    @php
                                        $contractTypes = [
                                            'permanent' => 'Tetap',
                                            'contract'  => 'Kontrak',
                                            'probation' => 'Probasi',
                                            'intern'    => 'Magang',
                                            'freelance' => 'Freelance',
                                        ];
                                    @endphp
                                    {{ $contractTypes[$status?->type_employment] ?? $status?->type_employment ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-slate-600">
                                    @if ($status?->contract_start_date)
                                        {{ $status->contract_start_date->format('d M Y') }}
                                        @if ($status->contract_end_date)
                                            → {{ $status->contract_end_date->format('d M Y') }}
                                        @else
                                            → Tidak ada batas
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @if ($hasPendingOffer)
                                        <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-800">
                                            Penawaran Aktif
                                        </span>
                                    @elseif ($expired)
                                        <span class="inline-flex rounded-full bg-rose-100 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                            Habis
                                        </span>
                                    @elseif ($expiring)
                                        <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            ≤30 Hari
                                        </span>
                                    @elseif ($status?->contract_end_date)
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                            Permanen
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('contract.manage')
                                            @if (!$status)
                                                <a href="{{ route('employment-statuses.create', $employee) }}"
                                                   class="inline-flex items-center gap-1 text-xs font-semibold text-[#215aac] hover:underline">
                                                    <span class="material-symbols-outlined text-[14px]">add</span>
                                                    Buat Kontrak
                                                </a>
                                            @else
                                                @if ($status->contract_end_date && !$hasPendingOffer)
                                                    <a href="{{ route('employment-statuses.extend.create', $status) }}"
                                                       class="inline-flex items-center gap-1 text-xs font-semibold text-amber-600 hover:underline">
                                                        <span class="material-symbols-outlined text-[14px]">update</span>
                                                        Perpanjang
                                                    </a>
                                                @endif
                                                @if ($hasPendingOffer)
                                                    <form method="POST" action="{{ route('employment-statuses.extend.accept', $status) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:underline">
                                                            <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                                            Setujui
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('employment-statuses.extend.reject', $status) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:underline">
                                                            <span class="material-symbols-outlined text-[14px]">cancel</span>
                                                            Tolak
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('employment-statuses.change-position.create', $status) }}"
                                                   class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:underline">
                                                    <span class="material-symbols-outlined text-[14px]">swap_horiz</span>
                                                    Ubah Posisi
                                                </a>
                                            @endif
                                        @endcan
                                        <a href="{{ route('employees.show', $employee) }}"
                                           class="inline-flex items-center gap-1 text-xs font-medium text-slate-400 hover:text-slate-600 hover:underline">
                                            <span class="material-symbols-outlined text-[14px]">person</span>
                                            Profil
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
