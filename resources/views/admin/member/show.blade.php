@extends('layouts.dashboard')

@section('title', 'Detail Member')

@section('content')
@php
    $statusBadge = [
        'pending_activation' => 'bg-amber-100 text-amber-800',
        'active'             => 'bg-emerald-100 text-emerald-800',
        'inactive'           => 'bg-slate-100 text-slate-600',
    ];
@endphp

<div class="max-w-5xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Member & Marketing</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $member->full_name }}</h2>
            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold mt-1 {{ $statusBadge[$member->activation_status ?? 'inactive'] ?? 'bg-slate-100 text-slate-600' }}">
                {{ str_replace('_', ' ', ucfirst($member->activation_status ?? 'inactive')) }}
            </span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('members.edit', $member) }}" class="inline-flex min-h-10 items-center gap-2 border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <span class="material-symbols-outlined text-[18px]">edit</span> Edit
            </a>
            @if($member->activation_status !== 'active')
                <form method="POST" action="{{ route('members.activate', $member) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span> Aktifkan
                    </button>
                </form>
            @endif
            <a href="{{ route('members.index') }}" class="inline-flex min-h-10 items-center gap-2 border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Data Pribadi -->
    <div class="border border-slate-200 bg-white">
        <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-200">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Data Pribadi</h3>
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Nama Lengkap</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->full_name }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Jenis Kelamin</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->gender === 'L' ? 'Laki-laki' : ($member->gender === 'P' ? 'Perempuan' : '—') }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Tanggal Lahir</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->birthdate?->format('d F Y') ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Email</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->email ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">WhatsApp</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->whatsapp_number ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Instagram</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->instagram ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Alamat</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->address ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Data Orang Tua -->
    <div class="border border-slate-200 bg-white">
        <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-200">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Data Orang Tua</h3>
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Nama Ayah</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->father_name ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Pekerjaan Ayah</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->father_occupation ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">WA Ayah</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->father_whatsapp ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Nama Ibu</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->mother_name ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Pekerjaan Ibu</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->mother_occupation ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">WA Ibu</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->mother_whatsapp ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Institusi & Program -->
    <div class="border border-slate-200 bg-white">
        <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-200">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Institusi & Program</h3>
        </div>
        <dl class="divide-y divide-slate-100">
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Institusi</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->institution?->institution_name ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Program</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->program?->program_name ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-slate-500">Direferensikan oleh</dt>
                <dd class="col-span-2 text-sm text-slate-900">{{ $member->referredBy?->full_name ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Akun Login -->
    <div class="border border-slate-200 bg-white">
        <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-200">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Akun Login</h3>
        </div>
        <div class="px-6 py-4">
            @if($member->account)
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Akun aktif</p>
                        <p class="text-xs text-slate-500">{{ $member->account->email }}</p>
                        <p class="text-xs text-slate-500 mt-1">Email terverifikasi: {{ $member->account->email_verified_at ? $member->account->email_verified_at->format('d M Y H:i') : 'Belum' }}</p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-slate-400 text-[20px]">person_off</span>
                    <p class="text-sm text-slate-500">Belum memiliki akun login</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Registrasi -->
    <div class="border border-slate-200 bg-white">
        <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Riwayat Registrasi</h3>
            @can('member.manage')
                <a href="{{ route('members.registrations.create', $member) }}" class="inline-flex min-h-9 items-center gap-1 bg-[#215aac] px-3 text-xs font-semibold text-white hover:bg-[#194a91]">
                    <span class="material-symbols-outlined text-[14px]">add</span> Buat Registrasi
                </a>
            @endcan
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Program</th>
                        <th class="px-5 py-3">Tanggal Daftar</th>
                        <th class="px-5 py-3">Status Bayar</th>
                        <th class="px-5 py-3">Kelulusan</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($member->registrations as $registration)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 text-slate-900 font-medium">{{ $registration->program?->program_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $registration->created_at?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $registration->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($registration->payment_status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ ucfirst($registration->graduation_status ?? '—') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('registrations.show', $registration) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500">Belum ada registrasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
