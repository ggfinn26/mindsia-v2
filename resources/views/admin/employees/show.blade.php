@extends('layouts.dashboard')

@section('title', 'Detail Pegawai')
@section('header_title', 'Detail Pegawai')

@section('content')
@php
    $eduLevels = [
        'sd' => 'SD', 'smp' => 'SMP', 'sma' => 'SMA/SMK',
        'd1' => 'D1', 'd2' => 'D2', 'd3' => 'D3', 'd4' => 'D4',
        's1' => 'S1', 's2' => 'S2', 's3' => 'S3',
    ];
    $contractTypes = [
        'permanent'  => 'Tetap',
        'contract'   => 'Kontrak',
        'probation'  => 'Probasi',
        'intern'     => 'Magang',
        'freelance'  => 'Freelance',
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 items-center justify-center bg-[#215aac]/10 text-xl font-bold text-[#215aac]">
                {{ mb_substr($employee->full_name, 0, 1) }}
            </div>
            <div>
                <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $employee->full_name }}</h2>
                <div class="mt-1 flex items-center gap-3">
                    <span class="font-mono text-sm text-slate-500">{{ $employee->employee_code }}</span>
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $employee->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-700' }}">
                        {{ $employee->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('employees.index') }}"
               class="inline-flex min-h-10 items-center gap-2 border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali
            </a>
            @can('update', $employee)
            <a href="{{ route('employees.edit', $employee) }}"
               class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-4 text-sm font-semibold text-white hover:bg-[#194a91]">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Edit Data
            </a>
            @endcan
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Kolom kiri (2/3) --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Informasi Pribadi --}}
            <div class="overflow-hidden border border-slate-200 bg-white">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h3 class="font-jakarta text-sm font-bold text-slate-900">Informasi Pribadi & Kontak</h3>
                </div>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-5 px-5 py-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Nama Lengkap</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-900">{{ $employee->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Kode Pegawai</dt>
                        <dd class="mt-1 font-mono text-sm text-slate-900">{{ $employee->employee_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            {{ $employee->gender === 'L' ? 'Laki-laki' : ($employee->gender === 'P' ? 'Perempuan' : '—') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tanggal Lahir</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            {{ $employee->birthdate?->format('d F Y') ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">No. WhatsApp</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $employee->whatsapp_number ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Email</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $employee->email ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Riwayat Kontrak --}}
            <div class="overflow-hidden border border-slate-200 bg-white">
                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h3 class="font-jakarta text-sm font-bold text-slate-900">Riwayat Kontrak</h3>
                    @can('employee.update')
                    <a href="{{ route('employment-statuses.create', $employee) }}"
                       class="text-xs font-semibold text-[#215aac] hover:underline">
                        + Buat Kontrak Baru
                    </a>
                    @endcan
                </div>
                @if ($employee->employmentStatuses->isNotEmpty())
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold text-slate-500">
                            <tr>
                                <th class="px-5 py-3 text-left">Posisi</th>
                                <th class="px-5 py-3 text-left">Tipe</th>
                                <th class="px-5 py-3 text-left">Mulai</th>
                                <th class="px-5 py-3 text-left">Kontrak</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($employee->employmentStatuses->sortByDesc('join_date') as $status)
                                <tr class="{{ $loop->first ? 'bg-emerald-50/40' : '' }}">
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-slate-900">{{ $status->position?->position_name ?? '—' }}</p>
                                        @if ($loop->first)
                                            <span class="mt-0.5 inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">
                                        {{ $contractTypes[$status->type_employment] ?? $status->type_employment ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">
                                        {{ $status->join_date?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">
                                        @if ($status->contract_start_date)
                                            {{ $status->contract_start_date->format('d M Y') }}
                                            @if ($status->contract_end_date)
                                                → {{ $status->contract_end_date->format('d M Y') }}
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-5 py-8 text-center">
                        <span class="material-symbols-outlined text-3xl text-slate-300">work_history</span>
                        <p class="mt-2 text-sm font-medium text-slate-600">Belum ada riwayat jabatan.</p>
                        @can('employee.update')
                        <a href="{{ route('employment-statuses.create', $employee) }}"
                           class="mt-2 inline-flex text-xs font-semibold text-[#215aac] hover:underline">
                            Buat kontrak →
                        </a>
                        @endcan
                    </div>
                @endif
            </div>

            {{-- Pendidikan (read-only — dikelola oleh pegawai sendiri) --}}
            <div class="border border-slate-200 bg-white">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h3 class="font-jakarta text-sm font-bold text-slate-900">Riwayat Pendidikan</h3>
                </div>
                @if ($employee->educations->isNotEmpty())
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold text-slate-500">
                            <tr>
                                <th class="px-5 py-3 text-left">Jenjang</th>
                                <th class="px-5 py-3 text-left">Institusi</th>
                                <th class="px-5 py-3 text-left">Jurusan</th>
                                <th class="px-5 py-3 text-left">Lulus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($employee->educations->sortByDesc('graduation_year') as $edu)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-slate-900">{{ $eduLevels[$edu->level] ?? strtoupper($edu->level) }}</td>
                                    <td class="px-5 py-3 text-slate-700">{{ $edu->institution ?? '—' }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ $edu->major ?? '—' }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ $edu->graduation_year ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-5 py-8 text-center">
                        <span class="material-symbols-outlined text-3xl text-slate-300">school</span>
                        <p class="mt-2 text-sm font-medium text-slate-600">Belum ada data pendidikan.</p>
                    </div>
                @endif
            </div>

            {{-- Pengalaman Kerja (read-only — dikelola oleh pegawai sendiri) --}}
            <div class="border border-slate-200 bg-white">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h3 class="font-jakarta text-sm font-bold text-slate-900">Pengalaman Kerja Sebelumnya</h3>
                </div>
                @if ($employee->workHistories->isNotEmpty())
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold text-slate-500">
                            <tr>
                                <th class="px-5 py-3 text-left">Perusahaan</th>
                                <th class="px-5 py-3 text-left">Posisi</th>
                                <th class="px-5 py-3 text-left">Periode</th>
                                <th class="px-5 py-3 text-left">Alasan Keluar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($employee->workHistories->sortByDesc('start_date') as $work)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-slate-900">{{ $work->company_name }}</td>
                                    <td class="px-5 py-3 text-slate-700">{{ $work->position ?? '—' }}</td>
                                    <td class="px-5 py-3 text-slate-600">
                                        {{ $work->start_date?->format('M Y') ?? '—' }}
                                        @if ($work->end_date) — {{ $work->end_date->format('M Y') }}
                                        @else — sekarang
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-slate-500">{{ $work->reason_leaving ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-5 py-8 text-center">
                        <span class="material-symbols-outlined text-3xl text-slate-300">business_center</span>
                        <p class="mt-2 text-sm font-medium text-slate-600">Belum ada riwayat pekerjaan.</p>
                    </div>
                @endif
            </div>

        </div>{{-- /kolom kiri --}}

        {{-- Kolom kanan (1/3) --}}
        <div class="space-y-6">

            {{-- Penempatan & Jabatan --}}
            <div class="overflow-hidden border border-slate-200 bg-white">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h3 class="font-jakarta text-sm font-bold text-slate-900">Penempatan & Jabatan</h3>
                </div>
                <dl class="space-y-4 px-5 py-5">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Jabatan Aktif</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-900">
                            {{ $employee->currentStatus?->position?->position_name ?? 'Belum ada jabatan' }}
                        </dd>
                        @if ($employee->currentStatus)
                            <dd class="mt-0.5 text-xs text-slate-500">
                                Sejak {{ $employee->currentStatus->join_date?->format('d M Y') ?? '—' }}
                            </dd>
                        @endif
                    </div>
                    <div class="border-t border-slate-100 pt-4">
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Penempatan</dt>
                        <dd class="mt-1 text-sm text-slate-900">
                            @if ($employee->is_hq)
                                <span class="inline-flex rounded px-2 py-0.5 text-xs font-semibold bg-purple-100 text-purple-800">Headquarters</span>
                            @elseif ($employee->branch)
                                Cabang · {{ $employee->branch->branch_name }}
                            @elseif ($employee->area)
                                Area · {{ $employee->area->name }}
                            @elseif ($employee->region)
                                Region · {{ $employee->region->name }}
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Kompensasi Gaji --}}
            <div class="overflow-hidden border border-slate-200 bg-white">
                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h3 class="font-jakarta text-sm font-bold text-slate-900">Kompensasi Gaji</h3>
                    @canany(['payroll.employee_compensation.create', 'payroll.employee_compensation.update'])
                    <a href="{{ route('employees.payroll.compensations.index', $employee) }}"
                       class="text-xs font-semibold text-[#215aac] hover:underline">
                        Kelola
                    </a>
                    @endcanany
                </div>
                @if ($employee->compensations->isNotEmpty())
                    <ul class="divide-y divide-slate-100">
                        @foreach ($employee->compensations->sortBy(fn($c) => $c->payrollComponent->component_type) as $comp)
                            <li class="flex items-center justify-between px-5 py-3">
                                <div>
                                    <p class="text-sm font-medium text-slate-800">{{ $comp->payrollComponent->component_name }}</p>
                                    <span class="mt-0.5 inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $comp->payrollComponent->component_type === 'earning' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $comp->payrollComponent->component_type === 'earning' ? 'Pendapatan' : 'Potongan' }}
                                    </span>
                                </div>
                                <span class="font-semibold text-slate-900">Rp {{ number_format($comp->value, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                    @php
                        $totalEarnings   = $employee->compensations->filter(fn($c) => $c->payrollComponent->component_type === 'earning')->sum('value');
                        $totalDeductions = $employee->compensations->filter(fn($c) => $c->payrollComponent->component_type === 'deduction')->sum('value');
                    @endphp
                    <div class="border-t border-slate-200 bg-slate-50 px-5 py-3">
                        <div class="flex justify-between text-xs text-slate-600">
                            <span>Total Pendapatan</span>
                            <span class="font-semibold text-emerald-700">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-1 flex justify-between text-xs text-slate-600">
                            <span>Total Potongan</span>
                            <span class="font-semibold text-red-700">Rp {{ number_format($totalDeductions, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-2 flex justify-between border-t border-slate-200 pt-2 text-sm font-bold">
                            <span>Take Home Pay</span>
                            <span class="text-slate-900">Rp {{ number_format($totalEarnings - $totalDeductions, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @else
                    <div class="px-5 py-8 text-center">
                        <span class="material-symbols-outlined text-3xl text-slate-300">payments</span>
                        <p class="mt-2 text-sm font-medium text-slate-600">Belum ada kompensasi.</p>
                        @canany(['payroll.employee_compensation.create'])
                        <a href="{{ route('employees.payroll.compensations.index', $employee) }}"
                           class="mt-2 inline-flex text-xs font-semibold text-[#215aac] hover:underline">
                            Tetapkan sekarang →
                        </a>
                        @endcanany
                    </div>
                @endif
            </div>

            {{-- Akun User --}}
            <div class="overflow-hidden border border-slate-200 bg-white">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h3 class="font-jakarta text-sm font-bold text-slate-900">Akun Login</h3>
                </div>
                <dl class="px-5 py-5">
                    @if ($employee->user)
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Email Akun</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $employee->user->email }}</dd>
                        </div>
                        <div class="mt-3">
                            <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Login Terakhir</dt>
                            <dd class="mt-1 text-sm text-slate-600">
                                {{ $employee->user->last_login_at?->format('d M Y H:i') ?? 'Belum pernah' }}
                            </dd>
                        </div>
                    @else
                        <p class="text-sm text-slate-500">Belum ada akun login.</p>
                    @endif
                </dl>
            </div>

        </div>{{-- /kolom kanan --}}

    </div>
</div>
@endsection
