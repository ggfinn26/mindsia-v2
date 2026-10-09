@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Aturan Penugasan Tiket</h1>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <button class="btn bg-[var(--dash-primary)] hover:opacity-90 text-white inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">add</span>Buat Aturan Baru</button>
        </div>
    </div>

    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)]">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-[color:var(--dash-text-muted)] bg-[var(--dash-nav-hover-bg)] border-t border-b border-[color:var(--dash-card-border)]">
                    <tr>
                        <th class="px-6 py-3 text-left">Kategori Tiket</th>
                        <th class="px-6 py-3 text-left">Cabang Member</th>
                        <th class="px-6 py-3 text-left">Tugaskan Ke (Posisi)</th>
                        <th class="px-6 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-[color:var(--dash-card-border)]">
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-[color:var(--dash-text-muted)] text-center">Belum ada aturan penugasan tiket.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
