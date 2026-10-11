@extends('layouts.dashboard')

@section('title', 'Konfirmasi Terminasi - ' . $status->employee->full_name)
@section('header_title', 'Konfirmasi Terminasi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('contract-terminate.checklist', $status) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Checklist
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">SDM & Karyawan</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Konfirmasi Selesai Terminasi</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Pegawai: <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->full_name }}</span> ({{ $status->employee->employee_code }})
        </p>
    </div>

    @if($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <div class="flex items-center gap-2 font-semibold mb-1">
            <span class="material-symbols-outlined text-[18px]">error</span>
            Ada kesalahan:
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        {{-- Danger notice header --}}
        <div class="px-6 py-5 flex items-start gap-4"
             style="background-color: #fef2f2; border-bottom: 1px solid #fecaca;">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
            </div>
            <div>
                <h2 class="text-base font-bold text-red-700 font-jakarta mb-1">Konfirmasi Hard Delete Akun</h2>
                <p class="text-sm text-red-600 mb-3">Anda akan menghapus akun pegawai secara permanen. Tindakan ini memiliki konsekuensi sebagai berikut:</p>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    <li>Pegawai tidak bisa login lagi ke sistem</li>
                    <li>Tindakan ini <strong>tidak bisa dibatalkan</strong> (irreversible)</li>
                    <li>Data histori pegawai tetap tersimpan untuk keperluan audit trail</li>
                </ul>
            </div>
        </div>

        <div class="px-6 py-6 space-y-5">
            <h3 class="text-xs font-bold uppercase tracking-wider flex items-center gap-2" style="color: var(--dash-text-muted);">
                <span class="material-symbols-outlined text-[16px]">person</span> Data yang Akan Dihapus
            </h3>

            <div class="rounded-xl overflow-hidden"
                 style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-3" style="border-bottom: 1px solid var(--dash-card-border);">
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Nama Pegawai</span>
                    <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->full_name }}</span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-3" style="border-bottom: 1px solid var(--dash-card-border);">
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kode Pegawai</span>
                    <span class="text-sm font-mono font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->employee_code }}</span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-3" style="border-bottom: 1px solid var(--dash-card-border);">
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Email</span>
                    <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->email ?? 'Belum diatur' }}</span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-3">
                    <span class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status Pekerjaan</span>
                    <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $status->type_employment }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('contract-terminate.complete', $status) }}" method="POST" class="px-6 py-6" style="border-top: 1px solid var(--dash-card-border);">
            @csrf

            <label class="flex items-start gap-3 p-4 rounded-xl cursor-pointer transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <div class="relative flex items-center justify-center mt-0.5">
                    <input type="checkbox" name="confirm" value="on" required
                           class="h-5 w-5 rounded cursor-pointer"
                           style="accent-color: #dc2626;">
                </div>
                <span class="text-sm leading-relaxed" style="color: var(--dash-text-body);">
                    Saya memahami bahwa tindakan ini akan menghapus akun <strong>{{ $status->employee->full_name }}</strong> secara permanen dan tidak dapat dibatalkan. Saya siap melanjutkan.
                </span>
            </label>

            <div class="pt-6 mt-6 flex justify-end gap-3" style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('contract-terminate.checklist', $status) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: #dc2626;">
                    <span class="material-symbols-outlined text-[16px]">delete_forever</span>
                    Hard Delete Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
