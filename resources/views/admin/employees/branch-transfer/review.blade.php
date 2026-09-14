@extends('layouts.dashboard')

@section('title', 'Review Pindah Cabang')
@section('header_title', 'Review Pindah Cabang')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Kelola Karyawan</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Permintaan Pindah Cabang</h2>
        <p class="mt-1 text-sm text-slate-600">Daftar permintaan perpindahan cabang yang menunggu persetujuan.</p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-semibold text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Pegawai</th>
                        <th class="px-5 py-3">Dari Cabang</th>
                        <th class="px-5 py-3">Ke Cabang</th>
                        <th class="px-5 py-3">Diajukan</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transfers as $transfer)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $transfer->employee?->full_name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $transfer->fromBranch?->branch_name }}</td>
                            <td class="px-5 py-4 font-medium text-[#215aac]">{{ $transfer->toBranch?->branch_name }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $transfer->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('branch-transfers.show', $transfer) }}" class="inline-flex min-h-9 items-center gap-1 border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">swap_horiz</span>
                                <p class="mt-2 font-semibold text-slate-700">Tidak ada permintaan yang menunggu review.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transfers->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $transfers->links() }}</div>
        @endif
    </div>
</div>
@endsection
