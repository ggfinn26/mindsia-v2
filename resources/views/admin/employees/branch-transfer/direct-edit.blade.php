@extends('layouts.dashboard')

@section('title', 'Pindah Cabang Langsung')
@section('header_title', 'Pindah Cabang Langsung')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('employees.show', $employee->id) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $employee->full_name }}
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Pindah Cabang Langsung</h1>
        <p class="mt-1 text-sm text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2 rounded">
            Perubahan ini langsung berlaku tanpa proses review.
        </p>
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

    <form method="POST" action="{{ route('branch-transfers.direct.update', $employee) }}" class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 space-y-4" novalidate>
        @csrf @method('PUT')
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Cabang tujuan</label>
            <select name="to_branch_id" required class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
                <option value="">Pilih Cabang Tujuan</option>
                @foreach ($otherBranches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('to_branch_id') == $branch->id)>{{ $branch->branch_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" onclick="return confirm('Pindahkan cabang langsung? Tindakan ini tidak perlu review.')" class="inline-flex min-h-10 items-center gap-2 bg-amber-600 px-5 text-sm font-semibold text-white hover:bg-amber-700">Pindah Sekarang</button>
            <a href="{{ route('employees.show', $employee->id) }}" class="inline-flex min-h-10 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Batal</a>
        </div>
    </form>
</div>
@endsection
