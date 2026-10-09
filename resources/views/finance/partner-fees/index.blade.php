@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Fee Partner / Sekolah</h1>
    </div>

    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)]">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-[color:var(--dash-text-muted)] bg-[var(--dash-nav-hover-bg)] border-t border-b border-[color:var(--dash-card-border)]">
                    <tr>
                        <th class="px-6 py-3 text-left">Institusi / Partner</th>
                        <th class="px-6 py-3 text-left">Total Fee</th>
                        <th class="px-6 py-3 text-left">Jatuh Tempo</th>
                        <th class="px-6 py-3 text-left">Status Pembayaran</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-[color:var(--dash-card-border)]">
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-[color:var(--dash-text-muted)] text-center">Belum ada tagihan fee partner.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
