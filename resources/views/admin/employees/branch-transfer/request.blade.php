@extends('layouts.dashboard')

@section('title', 'Ajukan Pindah Cabang')
@section('header_title', 'Ajukan Pindah Cabang')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    @php $selfService = auth()->id() === $employee->user_id; @endphp
    <div>
        @if ($selfService)
        <a href="{{ route('employee.profile.show') }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Profil Saya
        </a>
        @else
        <a href="{{ route('employees.show', $employee->id) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $employee->full_name }}
        </a>
        @endif
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Ajukan Pindah Cabang</h1>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="border border-[color:var(--dash-card-border)] bg-[var(--dash-nav-hover-bg)] px-5 py-4 text-sm">
        <p class="text-[color:var(--dash-text-muted)]">Cabang saat ini</p>
        <p class="mt-1 font-semibold text-[color:var(--dash-text-heading)]">{{ $currentBranch?->branch_name ?? '—' }}</p>
    </div>

    <form method="POST" action="{{ $selfService ? route('branch-transfers.my-request.store') : route('branch-transfers.request.store', $employee) }}" class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 space-y-4" novalidate>
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Cabang tujuan</label>
            <select name="to_branch_id" required class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <option value="">Pilih Cabang Tujuan</option>
                @foreach ($otherBranches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('to_branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                @endforeach
            </select>
            @if ($otherBranches->isEmpty())
                <p class="mt-1 text-xs text-amber-600">Tidak ada cabang aktif lain yang tersedia.</p>
            @endif
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary,#215aac)] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Ajukan Permintaan</button>
            <a href="{{ $selfService ? route('employee.profile.show') : route('employees.show', $employee->id) }}" class="inline-flex min-h-10 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Batal</a>
        </div>
    </form>
</div>
@endsection
