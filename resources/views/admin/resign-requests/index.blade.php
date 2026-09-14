@extends('layouts.dashboard')

@section('title', 'Permintaan Resign')
@section('header_title', 'Permintaan Resign')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-medium text-[#725c00]">Kelola Karyawan</p>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Permintaan Pengunduran Diri</h2>
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
                        <th class="px-5 py-3">Tanggal Efektif</th>
                        <th class="px-5 py-3">Diajukan</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($resignRequests as $req)
                        @php
                            $statusClass = match($req->status) {
                                'approved' => 'bg-emerald-100 text-emerald-800',
                                'rejected' => 'bg-red-100 text-red-800',
                                default     => 'bg-amber-100 text-amber-800',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $req->employee?->full_name }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $req->effective_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-4 text-slate-500">{{ $req->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($req->status) }}</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('resign-requests.show', $req) }}" class="inline-flex min-h-9 items-center border border-slate-300 px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-slate-300">exit_to_app</span>
                                <p class="mt-2 font-semibold text-slate-700">Tidak ada permintaan resign.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($resignRequests->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $resignRequests->links() }}</div>
        @endif
    </div>
</div>
@endsection
