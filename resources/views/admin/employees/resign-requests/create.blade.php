@extends('layouts.dashboard')

@section('title', 'Ajukan Pengunduran Diri')
@section('header_title', 'Ajukan Pengunduran Diri')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('employees.show', $employee->id) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $employee->full_name }}
        </a>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Ajukan Pengunduran Diri</h1>
        <p class="mt-1 text-sm text-[color:var(--dash-text-muted)]">Permintaan akan diteruskan ke HR untuk diproses.</p>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('resign-requests.store', $employee) }}" class="border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Tanggal efektif pengunduran</label>
            <input type="date" name="resign_date" value="{{ old('resign_date') }}" required min="{{ today()->addDays(30)->toDateString() }}" class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]">
            <p class="mt-1 text-xs text-[color:var(--dash-text-faint)]">Minimal 30 hari dari hari ini.</p>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-[color:var(--dash-text-body)]">Alasan pengunduran diri</label>
            <textarea name="reason" rows="4" required class="block w-full border-[color:var(--dash-input-border)] text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-[color:var(--dash-primary,#215aac)]" placeholder="Jelaskan alasan pengunduran diri...">{{ old('reason') }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" onclick="return confirm('Yakin ajukan pengunduran diri?')" class="inline-flex min-h-10 items-center gap-2 bg-red-600 px-5 text-sm font-semibold text-white hover:bg-red-700">Ajukan</button>
            <a href="{{ route('employees.show', $employee->id) }}" class="inline-flex min-h-10 items-center border border-[color:var(--dash-input-border)] px-4 text-sm font-semibold text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]">Batal</a>
        </div>
    </form>
</div>
@endsection
