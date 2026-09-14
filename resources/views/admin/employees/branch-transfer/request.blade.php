@extends('layouts.dashboard')

@section('title', 'Ajukan Pindah Cabang')
@section('header_title', 'Ajukan Pindah Cabang')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('employees.show', $employee) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $employee->full_name }}
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Ajukan Pindah Cabang</h2>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="border border-slate-200 bg-slate-50 px-5 py-4 text-sm">
        <p class="text-slate-500">Cabang saat ini</p>
        <p class="mt-1 font-semibold text-slate-900">{{ $currentBranch?->branch_name ?? '—' }}</p>
    </div>

    <form method="POST" action="{{ route('employees.branch-transfer.request', $employee) }}" class="border border-slate-200 bg-white p-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Cabang tujuan</label>
            <select name="to_branch_id" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                <option value="">— Pilih cabang tujuan —</option>
                @foreach ($otherBranches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('to_branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                @endforeach
            </select>
            @if ($otherBranches->isEmpty())
                <p class="mt-1 text-xs text-amber-600">Tidak ada cabang lain dalam region yang sama.</p>
            @endif
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Ajukan Permintaan</button>
            <a href="{{ route('employees.show', $employee) }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</div>
@endsection
