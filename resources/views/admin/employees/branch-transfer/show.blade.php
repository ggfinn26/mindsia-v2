@extends('layouts.dashboard')

@section('title', 'Detail Permintaan Pindah Cabang')
@section('header_title', 'Review Permintaan Pindah Cabang')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('branch-transfers.review') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Daftar Permintaan
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Permintaan Pindah Cabang</h2>
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
                <p class="mt-1 font-semibold text-slate-900">{{ $transfer->employee?->full_name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Diajukan</p>
                <p class="mt-1 text-slate-700">{{ $transfer->created_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Dari cabang</p>
                <p class="mt-1 font-medium text-slate-900">{{ $transfer->fromBranch?->branch_name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Ke cabang</p>
                <p class="mt-1 font-medium text-[#215aac]">{{ $transfer->toBranch?->branch_name }}</p>
            </div>
        </div>
    </div>

    @if ($transfer->status === 'review')
        <form method="POST" action="{{ route('branch-transfers.update', $transfer) }}" class="border border-slate-200 bg-white p-5 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Catatan (opsional)</label>
                <textarea name="notes" rows="3" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" name="status" value="approved" class="inline-flex min-h-10 items-center gap-2 bg-emerald-600 px-5 text-sm font-semibold text-white hover:bg-emerald-700">
                    <span class="material-symbols-outlined text-[18px]">check</span> Setujui
                </button>
                <button type="submit" name="status" value="rejected" class="inline-flex min-h-10 items-center gap-2 border border-red-200 px-5 text-sm font-semibold text-red-700 hover:bg-red-50">
                    Tolak
                </button>
            </div>
        </form>
    @else
        <div class="border border-slate-200 bg-slate-50 px-5 py-4 text-sm">
            <p class="text-xs text-slate-500">Status</p>
            <p class="mt-1 font-semibold {{ $transfer->status === 'approved' ? 'text-emerald-700' : 'text-red-700' }}">{{ ucfirst($transfer->status) }}</p>
        </div>
    @endif
</div>
@endsection
