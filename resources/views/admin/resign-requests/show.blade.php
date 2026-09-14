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
                <p class="mt-1 font-medium text-slate-900">{{ $resignRequest->effective_date?->format('d M Y') ?? '—' }}</p>
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
        <div class="flex gap-3">
            <form method="POST" action="{{ route('resign-requests.approve', $resignRequest) }}" onsubmit="return confirm('Setujui permintaan resign ini? Proses offboarding akan dimulai.')">
                @csrf
                <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-emerald-600 px-5 text-sm font-semibold text-white hover:bg-emerald-700">
                    <span class="material-symbols-outlined text-[18px]">check</span> Setujui & Mulai Offboarding
                </button>
            </form>
            <form method="POST" action="{{ route('resign-requests.reject', $resignRequest) }}" onsubmit="return confirm('Tolak permintaan resign ini?')">
                @csrf
                <button type="submit" class="inline-flex min-h-10 items-center border border-red-200 px-5 text-sm font-semibold text-red-700 hover:bg-red-50">Tolak</button>
            </form>
        </div>
    @endif
</div>
@endsection
