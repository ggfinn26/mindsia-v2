@extends('layouts.dashboard')

@section('title', 'Pengaturan Izin/Cuti - MINDSIA')
@section('header_title', 'Pengaturan Izin/Cuti')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Absensi & Penggajian</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Pengaturan Izin/Cuti</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Tentukan status pembayaran untuk izin, sakit, dan cuti per posisi jabatan. Default: semua tipe izin dibayar penuh jika belum diatur khusus.
            </p>
        </div>
    </div>

    {{-- Info Alert --}}
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm"
         style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border); color: var(--dash-text-muted);">
        <span class="material-symbols-outlined text-[20px] shrink-0" style="color: var(--dash-primary-text);">info</span>
        <p>Klik tombol status pada setiap kolom untuk mengubah pengaturan antara <strong>Dibayar</strong> dan <strong>Tidak Dibayar</strong>. Posisi Board selalu dibayar penuh.</p>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Data Table Card --}}
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        @if($positions->isEmpty())
            <div class="text-center py-12 px-4">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">badge</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data posisi terdaftar.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold w-1/3"
                                style="color: var(--dash-text-muted);">Posisi</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                                style="color: var(--dash-text-muted);">Izin</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                                style="color: var(--dash-text-muted);">Sakit</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                                style="color: var(--dash-text-muted);">Cuti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                        @foreach($positions as $position)
                            @php
                                $types = [
                                    'permission' => 'Izin',
                                    'sick'       => 'Sakit',
                                    'leave'      => 'Cuti',
                                ];
                            @endphp
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3.5 whitespace-nowrap font-medium"
                                    style="color: var(--dash-text-heading);">
                                    {{ $position->position_name ?? $position->title }}
                                </td>
                                @foreach($types as $typeKey => $typeLabel)
                                    @php
                                        $key = $position->id . '_' . $typeKey;
                                        $isPaid = isset($settings[$key]) ? (bool)$settings[$key]->is_paid : true;
                                    @endphp
                                    <td class="px-5 py-3.5 whitespace-nowrap text-center">
                                        <form method="POST" action="{{ route('leave-pay-settings.upsert') }}" class="inline-block">
                                            @csrf
                                            <input type="hidden" name="position_id" value="{{ $position->id }}">
                                            <input type="hidden" name="leave_type" value="{{ $typeKey }}">
                                            <input type="hidden" name="is_paid" value="{{ $isPaid ? '0' : '1' }}">
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-opacity hover:opacity-90 shadow-sm cursor-pointer"
                                                    style="{{ $isPaid
                                                        ? 'background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #059669;'
                                                        : 'background-color: #fef2f2; border: 1px solid #fecaca; color: var(--dash-danger);' }}"
                                                    title="Klik untuk ubah ke {{ $isPaid ? 'Tidak Dibayar' : 'Dibayar' }}">
                                                <span class="material-symbols-outlined text-[15px]">
                                                    {{ $isPaid ? 'check_circle' : 'cancel' }}
                                                </span>
                                                {{ $isPaid ? 'Dibayar' : 'Tidak Dibayar' }}
                                            </button>
                                        </form>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <p class="text-xs" style="color: var(--dash-text-muted);">
        Klik badge status untuk toggle. Perubahan berlaku mulai periode payroll berikutnya.
    </p>
</div>
@endsection
