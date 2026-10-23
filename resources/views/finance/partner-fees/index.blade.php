@extends('layouts.dashboard')

@section('title', 'Fee Partner & Sekolah')
@section('header_title', 'Fee Partner')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Keuangan</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Fee Partner & Sekolah</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Pencatatan dan pemantauan tagihan komisi kerja sama institusi sekolah serta partner.
            </p>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Institusi / Partner</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Total Fee</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Jatuh Tempo</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                            style="color: var(--dash-text-muted);">Status Pembayaran</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    <tr>
                        <td colspan="5" class="px-4 py-16 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">handshake</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada tagihan fee partner.</p>
                            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Data komisi kerja sama partner dan sekolah akan otomatis terdata di sini.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
