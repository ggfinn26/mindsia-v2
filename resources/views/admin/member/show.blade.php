@extends('layouts.dashboard')

@section('title', 'Detail Member - MINDSIA')
@section('header_title', 'Detail Member')

@section('content')
@php use App\Enums\Member\ActivationStatus; @endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('members.index') }}" class="dash-back mb-3 inline-flex items-center gap-1.5 text-xs font-semibold">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Data Member
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $member->full_name }}</h1>
                    @php $status = $member->activation_status; @endphp
                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $status instanceof ActivationStatus ? $status->badgeClass() : 'bg-slate-100 text-slate-600' }}">
                        {{ $status instanceof ActivationStatus ? $status->label() : '-' }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('members.edit', $member) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors shadow-sm"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                    Edit Member
                </a>
            </div>
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Card 1: Data Pribadi --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Data Pribadi</h2>
        </div>
        <dl class="divide-y" style="border-color: var(--dash-card-border);">
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Nama Lengkap</dt>
                <dd class="sm:col-span-2 text-sm font-medium" style="color: var(--dash-text-heading);">{{ $member->full_name }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Jenis Kelamin</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">{{ $member->gender === 'L' ? 'Laki-laki' : ($member->gender === 'P' ? 'Perempuan' : '-') }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal Lahir</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">{{ $member->birthdate?->format('d F Y') ?? '-' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Email</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">{{ $member->email ?? '-' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">WhatsApp</dt>
                <dd class="sm:col-span-2 text-sm font-mono" style="color: var(--dash-text-body);">
                    @php
                        $waNum = preg_replace('/[^0-9]/', '', $member->whatsapp_number ?? '');
                        if (str_starts_with($waNum, '0')) {
                            $waNum = '62'.substr($waNum, 1);
                        } elseif ($waNum && ! str_starts_with($waNum, '62')) {
                            $waNum = '62'.$waNum;
                        }
                    @endphp
                    <div class="flex items-center gap-2">
                        <span>{{ $member->whatsapp_number ?? '-' }}</span>
                        @if ($waNum)
                            <div class="relative" x-data="waPicker({
                                wa: '{{ $waNum }}',
                                nama: @js($member->full_name),
                                institusi: @js($member->institution?->name ?? $member->institution?->institution_name ?? ''),
                                sosialisasi: '',
                                status: @js($member->activation_status instanceof ActivationStatus ? $member->activation_status->label() : (string) $member->activation_status)
                            })" @click.away="open = false">
                                <button type="button" @click="open = !open"
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-full hover:bg-emerald-50 focus:outline-none transition-colors"
                                    title="{{ $member->whatsapp_number }}">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 362" fill="none" aria-label="WhatsApp"><path fill="#25D366" fill-rule="evenodd" d="M307.546 52.566C273.709 18.684 228.706.017 180.756 0 81.951 0 1.538 80.404 1.504 179.235c-.017 31.594 8.242 62.432 23.928 89.609L0 361.736l95.024-24.925c26.179 14.285 55.659 21.805 85.655 21.814h.077c98.788 0 179.21-80.413 179.244-179.244.017-47.898-18.608-92.926-52.454-126.807v-.008Zm-126.79 275.788h-.06c-26.73-.008-52.952-7.194-75.831-20.765l-5.44-3.231-56.391 14.791 15.05-54.981-3.542-5.638c-14.912-23.721-22.793-51.139-22.776-79.286.035-82.14 66.867-148.973 149.051-148.973 39.793.017 77.198 15.53 105.328 43.695 28.131 28.157 43.61 65.596 43.593 105.398-.035 82.149-66.867 148.982-148.982 148.982v.008Zm81.719-111.577c-4.478-2.243-26.497-13.073-30.606-14.568-4.108-1.496-7.09-2.243-10.073 2.243-2.982 4.487-11.568 14.577-14.181 17.559-2.613 2.991-5.226 3.361-9.704 1.117-4.477-2.243-18.908-6.97-36.02-22.226-13.313-11.878-22.304-26.54-24.916-31.027-2.613-4.486-.275-6.91 1.959-9.136 2.011-2.011 4.478-5.234 6.721-7.847 2.244-2.613 2.983-4.486 4.478-7.469 1.496-2.991.748-5.603-.369-7.847-1.118-2.243-10.073-24.289-13.812-33.253-3.636-8.732-7.331-7.546-10.073-7.692-2.613-.13-5.595-.155-8.586-.155-2.991 0-7.839 1.118-11.947 5.604-4.108 4.486-15.677 15.324-15.677 37.361s16.047 43.344 18.29 46.335c2.243 2.991 31.585 48.225 76.51 67.632 10.684 4.615 19.029 7.374 25.535 9.437 10.727 3.412 20.49 2.931 28.208 1.779 8.604-1.289 26.498-10.838 30.228-21.298 3.73-10.46 3.73-19.433 2.613-21.298-1.117-1.865-4.108-2.991-8.586-5.234l.008-.017Z" clip-rule="evenodd"/></svg>
                                </button>
                                <div x-show="open" x-transition
                                    class="absolute left-0 top-8 z-20 w-56 rounded-xl shadow-xl py-1.5"
                                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                    <button type="button" @click="send(null)"
                                        class="w-full text-left px-4 py-2 text-xs font-medium transition-colors"
                                        style="color: var(--dash-text-body);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='transparent'">
                                        Kirim langsung (tanpa template)
                                    </button>
                                    <template x-if="templates.length > 0">
                                        <div>
                                            <p class="px-4 py-1 text-[10px] font-semibold uppercase tracking-wider border-t mt-1"
                                               style="color: var(--dash-text-faint); border-color: var(--dash-card-border);">Template Pesan</p>
                                            <template x-for="t in templates" :key="t.id">
                                                <button type="button" @click="send(t)"
                                                    class="w-full text-left px-4 py-2 text-xs font-medium transition-colors"
                                                    style="color: var(--dash-text-heading);"
                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                    onmouseout="this.style.backgroundColor='transparent'"
                                                    x-text="t.name">
                                                </button>
                                            </template>
                                        </div>
                                    </template>
                                    <p x-show="templates.length === 0"
                                        class="px-4 py-2 text-[10px] border-t mt-1"
                                        style="color: var(--dash-text-faint); border-color: var(--dash-card-border);">
                                        Belum ada template. Buat di pengaturan WA.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Instagram</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">{{ $member->instagram ?? '-' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Alamat Domisili</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">{{ $member->address ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Card 2: Data Orang Tua --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Data Orang Tua / Wali</h2>
        </div>
        <dl class="divide-y" style="border-color: var(--dash-card-border);">
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Nama Ayah</dt>
                <dd class="sm:col-span-2 text-sm font-medium" style="color: var(--dash-text-heading);">{{ $member->father_name ?? '-' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Pekerjaan Ayah</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">{{ $member->father_occupation ?? '-' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">WhatsApp Ayah</dt>
                <dd class="sm:col-span-2 text-sm font-mono" style="color: var(--dash-text-body);">{{ $member->father_whatsapp ?? '-' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Nama Ibu</dt>
                <dd class="sm:col-span-2 text-sm font-medium" style="color: var(--dash-text-heading);">{{ $member->mother_name ?? '-' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Pekerjaan Ibu</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">{{ $member->mother_occupation ?? '-' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">WhatsApp Ibu</dt>
                <dd class="sm:col-span-2 text-sm font-mono" style="color: var(--dash-text-body);">{{ $member->mother_whatsapp ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Card 3: Institusi & Program --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Institusi & Program</h2>
        </div>
        <dl class="divide-y" style="border-color: var(--dash-card-border);">
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Institusi</dt>
                <dd class="sm:col-span-2 text-sm font-medium" style="color: var(--dash-text-heading);">{{ $member->institution?->institution_name ?? '-' }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Program Terdaftar</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">
                    @forelse($member->registrations as $reg)
                        <span class="inline-flex items-center gap-1">{{ $reg->program?->program_name ?? '-' }}</span>{{ !$loop->last ? ', ' : '' }}
                    @empty
                        -
                    @endforelse
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-3 gap-2">
                <dt class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Direferensikan Oleh</dt>
                <dd class="sm:col-span-2 text-sm" style="color: var(--dash-text-body);">{{ $member->referredBy?->full_name ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Card 4: Akun Login --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Akun Login Portal</h2>
        </div>
        <div class="px-6 py-4">
            @if($member->account)
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[20px] text-emerald-600 mt-0.5">check_circle</span>
                    <div>
                        <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">Akun Aktif</p>
                        <p class="text-xs font-mono mt-0.5" style="color: var(--dash-text-body);">{{ $member->account->email }}</p>
                        <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Status Verifikasi Email: {{ $member->account->email_verified_at ? $member->account->email_verified_at->format('d M Y H:i') : 'Belum Verifikasi' }}</p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-text-faint);">person_off</span>
                    <p class="text-sm" style="color: var(--dash-text-muted);">Belum memiliki akun login portal member.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Card 5: Riwayat Registrasi (Zero horizontal scroll, compact columns) --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b flex items-center justify-between"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Riwayat Registrasi</h2>
            @can('member.manage')
                <a href="{{ route('members.registrations.create', $member) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[14px]">add</span>
                    Buat Registrasi
                </a>
            @endcan
        </div>
        <table class="w-full text-sm text-left">
            <thead>
                <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Program & Tanggal</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Status Pembayaran</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Kelulusan</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right" style="color: var(--dash-text-muted);">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($member->registrations as $registration)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">{{ $registration->program?->program_name ?? '-' }}</p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Daftar: {{ $registration->created_at?->format('d M Y') ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $registration->status_color }}">
                                {{ $registration->status_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-xs" style="color: var(--dash-text-body);">
                            {{ \App\Support\EnumLabels::get('graduation_status', $registration->graduation_status) }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('registrations.show', $registration) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
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
                        <td colspan="4" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">receipt_long</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data registrasi.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
