@extends('layouts.dashboard')

@section('title', 'Detail Member')

@section('content')
@php use App\Enums\Member\ActivationStatus; @endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-start gap-4">
        <a href="{{ route('members.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight text-[var(--dash-text-heading)] truncate">{{ $member->full_name }}</h1>
            <div class="flex items-center gap-2 mt-1">
                @php $status = $member->activation_status; @endphp
                <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $status instanceof ActivationStatus ? $status->badgeClass() : 'bg-[var(--dash-nav-hover-bg)] text-[var(--dash-text-body)]' }}">
                    {{ $status instanceof ActivationStatus ? $status->label() : '—' }}
                </span>
                <span class="text-sm text-[var(--dash-text-muted)]">&bull; Member & Marketing</span>
            </div>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('members.edit', $member) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-primary)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-body)'">
                <span class="material-symbols-outlined text-[18px]">edit</span> Edit
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #a7f3d0; background-color: #ecfdf5; color: #065f46;">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Data Pribadi -->
    <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-2xl overflow-hidden">
        <div class="bg-[var(--dash-page-bg)] px-6 py-4 border-b border-[var(--dash-card-border)]">
            <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)]">Data Pribadi</h3>
        </div>
        <dl class="divide-y divide-[var(--dash-card-border)]">
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Nama Lengkap</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->full_name }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Jenis Kelamin</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->gender === 'L' ? 'Laki-laki' : ($member->gender === 'P' ? 'Perempuan' : '—') }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Tanggal Lahir</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->birthdate?->format('d F Y') ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Email</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->email ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">WhatsApp</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->whatsapp_number ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Instagram</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->instagram ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Alamat</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->address ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Data Orang Tua -->
    <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-2xl overflow-hidden">
        <div class="bg-[var(--dash-page-bg)] px-6 py-4 border-b border-[var(--dash-card-border)]">
            <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)]">Data Orang Tua</h3>
        </div>
        <dl class="divide-y divide-[var(--dash-card-border)]">
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Nama Ayah</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->father_name ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Pekerjaan Ayah</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->father_occupation ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">WA Ayah</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->father_whatsapp ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Nama Ibu</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->mother_name ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Pekerjaan Ibu</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->mother_occupation ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">WA Ibu</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->mother_whatsapp ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Institusi & Program -->
    <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-2xl overflow-hidden">
        <div class="bg-[var(--dash-page-bg)] px-6 py-4 border-b border-[var(--dash-card-border)]">
            <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)]">Institusi & Program</h3>
        </div>
        <dl class="divide-y divide-[var(--dash-card-border)]">
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Institusi</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->institution?->institution_name ?? '—' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Program</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">
                    @forelse($member->registrations as $reg)
                        <span class="inline-flex items-center gap-1">{{ $reg->program?->program_name ?? '—' }}</span>{{ !$loop->last ? ', ' : '' }}
                    @empty
                        —
                    @endforelse
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-xs font-semibold text-[var(--dash-text-muted)]">Direferensikan oleh</dt>
                <dd class="col-span-2 text-sm text-[var(--dash-text-heading)]">{{ $member->referredBy?->full_name ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Akun Login -->
    <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-2xl overflow-hidden">
        <div class="bg-[var(--dash-page-bg)] px-6 py-4 border-b border-[var(--dash-card-border)]">
            <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)]">Akun Login</h3>
        </div>
        <div class="px-6 py-4">
            @if($member->account)
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
                    <div>
                        <p class="text-sm font-semibold text-[var(--dash-text-heading)]">Akun aktif</p>
                        <p class="text-xs text-[var(--dash-text-muted)]">{{ $member->account->email }}</p>
                        <p class="text-xs text-[var(--dash-text-muted)] mt-1">Email terverifikasi: {{ $member->account->email_verified_at ? $member->account->email_verified_at->format('d M Y H:i') : 'Belum' }}</p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[var(--dash-text-faint)] text-[20px]">person_off</span>
                    <p class="text-sm text-[var(--dash-text-muted)]">Belum memiliki akun login</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Registrasi -->
    <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-2xl overflow-hidden">
        <div class="bg-[var(--dash-page-bg)] px-6 py-4 border-b border-[var(--dash-card-border)] flex items-center justify-between">
            <h3 class="font-jakarta text-base font-bold text-[var(--dash-text-heading)]">Riwayat Registrasi</h3>
            @can('member.manage')
                <a href="{{ route('members.registrations.create', $member) }}" class="inline-flex min-h-9 items-center gap-1 bg-[var(--dash-primary)] px-3 text-xs font-semibold text-white hover:opacity-90 rounded-lg">
                    <span class="material-symbols-outlined text-[14px]">add</span> Buat Registrasi
                </a>
            @endcan
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-semibold text-[var(--dash-text-muted)]">
                    <tr>
                        <th class="px-5 py-3">Program</th>
                        <th class="px-5 py-3">Tanggal Daftar</th>
                        <th class="px-5 py-3">Status Bayar</th>
                        <th class="px-5 py-3">Kelulusan</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)]">
                    @forelse($member->registrations as $registration)
                        <tr class="hover:bg-[var(--dash-page-bg)]/70">
                            <td class="px-5 py-4 text-[var(--dash-text-heading)] font-medium">{{ $registration->program?->program_name ?? '—' }}</td>
                            <td class="px-5 py-4 text-[var(--dash-text-body)]">{{ $registration->created_at?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $registration->status_color }}">
                                    {{ $registration->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-[var(--dash-text-body)]">{{ ucfirst($registration->graduation_status ?? '—') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('registrations.show', $registration) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-primary)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-body)'">
                                   Detail <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-[var(--dash-text-muted)]">Belum ada registrasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
