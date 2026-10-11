@extends('layouts.dashboard')

@section('title', 'Detail Permintaan Resign')
@section('header_title', 'Detail Permintaan Resign')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('resign-requests.index') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Permintaan Resign
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">{{ $resignRequest->employee?->full_name }}</h2>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="border border-slate-200 bg-white divide-y divide-slate-100 text-sm">
        <div class="grid grid-cols-2 gap-4 p-5">
            <div>
                <p class="text-xs text-slate-500">Pegawai</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $resignRequest->employee?->full_name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Tanggal efektif</p>
                <p class="mt-1 font-medium text-slate-900">{{ $resignRequest->resign_date?->format('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Diajukan</p>
                <p class="mt-1 text-slate-700">{{ $resignRequest->created_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Status</p>
                <p class="mt-1 font-semibold {{ match($resignRequest->status) { 'approved' => 'text-emerald-700', 'rejected' => 'text-red-700', default => 'text-amber-700' } }}">
                    {{ ucfirst($resignRequest->status) }}
                </p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-slate-500">Alasan</p>
                <p class="mt-1 text-slate-700">{{ $resignRequest->reason }}</p>
            </div>
        </div>
    </div>

    @if ($resignRequest->status === 'pending')
        <div x-data="{ showApprove: false, showReject: false }" class="flex gap-3">

            {{-- Tombol Setujui --}}
            <button type="button" @click="showApprove = true"
                    class="inline-flex min-h-10 items-center gap-2 bg-emerald-600 px-5 text-sm font-semibold text-white hover:bg-emerald-700">
                <span class="material-symbols-outlined text-[18px]">check</span> Setujui & Mulai Offboarding
            </button>

            {{-- Tombol Tolak --}}
            <button type="button" @click="showReject = true"
                    class="inline-flex min-h-10 items-center border border-red-200 px-5 text-sm font-semibold text-red-700 hover:bg-red-50">Tolak</button>

            {{-- Modal Setujui --}}
            <template x-teleport="body">
                <div x-show="showApprove" class="fixed inset-0 z-[9999] flex items-center justify-center" style="display:none">
                    <div class="fixed inset-0 bg-slate-900/50" @click="showApprove = false"></div>
                    <div class="relative w-full max-w-md border border-slate-200 bg-white shadow-xl">
                        <div class="px-6 py-5">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold text-slate-900">Setujui Pengunduran Diri?</h3>
                                    <p class="mt-1 text-sm text-slate-600">Proses offboarding akan dimulai untuk <strong>{{ $resignRequest->employee?->full_name }}</strong>. Tindakan ini tidak dapat dibatalkan.</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                            <button type="button" @click="showApprove = false" class="inline-flex min-h-9 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                            <form method="POST" action="{{ route('resign-requests.approve', $resignRequest) }}" class="m-0">
                                @csrf
                                <button type="submit" class="inline-flex min-h-9 items-center gap-2 bg-emerald-600 px-4 text-sm font-semibold text-white hover:bg-emerald-700">Ya, Setujui</button>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Modal Tolak --}}
            <template x-teleport="body">
                <div x-show="showReject" class="fixed inset-0 z-[9999] flex items-center justify-center" style="display:none">
                    <div class="fixed inset-0 bg-slate-900/50" @click="showReject = false"></div>
                    <div class="relative w-full max-w-md border border-slate-200 bg-white shadow-xl">
                        <div class="px-6 py-5">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-red-600">cancel</span>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold text-slate-900">Tolak Pengunduran Diri?</h3>
                                    <p class="mt-1 text-sm text-slate-600">Permintaan dari <strong>{{ $resignRequest->employee?->full_name }}</strong> akan ditolak. Karyawan dapat mengajukan kembali.</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 border-t border-slate-100 px-6 py-4">
                            <button type="button" @click="showReject = false" class="inline-flex min-h-9 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                            <form method="POST" action="{{ route('resign-requests.reject', $resignRequest) }}" class="m-0">
                                @csrf
                                <button type="submit" class="inline-flex min-h-9 items-center gap-2 border border-red-200 px-4 text-sm font-semibold text-red-700 hover:bg-red-50">Ya, Tolak</button>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

        </div>
    @endif

    @if ($resignRequest->status === 'approved' && $resignRequest->employee)
        @can('update', $resignRequest->employee)
        <div>
            <a href="{{ route('off-boarding.create', $resignRequest->employee) }}"
               class="inline-flex min-h-10 items-center gap-2 bg-red-600 px-5 text-sm font-semibold text-white hover:bg-red-700">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Lanjut Proses Offboarding
            </a>
        </div>
        @endcan
    @endif
</div>
@endsection
