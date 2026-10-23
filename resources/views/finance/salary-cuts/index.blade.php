@extends('layouts.dashboard')

@section('title', 'Potongan Gaji & Penggajian')
@section('header_title', 'Penggajian')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Keuangan</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Potongan Gaji & Penggajian (Payroll)</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Manajemen rekap gaji, potongan keterlambatan, insentif, dan alur persetujuan slip gaji pegawai.
            </p>
        </div>
    </div>

    {{-- Content Card --}}
    <div class="p-16 rounded-2xl text-center shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
              style="color: var(--dash-text-faint);">payments</span>
        <h2 class="text-base font-bold" style="color: var(--dash-text-heading);">Manajemen Penggajian</h2>
        <p class="mt-1 text-sm max-w-md mx-auto" style="color: var(--dash-text-muted);">
            Pengelolaan slip gaji karyawan dan tutor, rekap absensi pengajar, dan persetujuan payroll bulanan akan dikelola terintegrasi di modul ini.
        </p>
    </div>
</div>
@endsection
