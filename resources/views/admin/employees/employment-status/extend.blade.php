@extends('layouts.dashboard')

@section('title', 'Perpanjang Kontrak — ' . $status->employee->full_name)
@section('header_title', 'Perpanjang Kontrak')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Perpanjang Kontrak</h2>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">{{ $status->employee->full_name }} &mdash; {{ $status->position?->position_name }}</p>
        </div>
        <a href="{{ route('employees.show', $status->employee) }}"
           class="inline-flex min-h-10 items-center gap-2 border border-[var(--dash-input-border)] px-4 text-sm font-semibold text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)]">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="flex gap-3 border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Info kontrak saat ini --}}
    <div class="overflow-hidden border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[var(--dash-card-border)] bg-[var(--dash-page-bg)] px-5 py-4">
            <h3 class="font-jakarta text-sm font-bold text-[var(--dash-text-heading)]">Kontrak Saat Ini</h3>
        </div>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-5 px-5 py-5 sm:grid-cols-4">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Nama</dt>
                <dd class="mt-1 text-sm font-medium text-[var(--dash-text-heading)]">{{ $status->employee->full_name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Posisi</dt>
                <dd class="mt-1 text-sm text-[var(--dash-text-heading)]">{{ $status->position?->position_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Mulai Kontrak</dt>
                <dd class="mt-1 text-sm text-[var(--dash-text-heading)]">{{ $status->contract_start_date?->format('d M Y') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Akhir Kontrak</dt>
                <dd class="mt-1 text-sm font-semibold {{ $status->contract_end_date?->lte(now()->addDays(30)) ? 'text-amber-700' : 'text-[var(--dash-text-heading)]' }}">
                    {{ $status->contract_end_date?->format('d M Y') ?? 'Tidak ada batas' }}
                </dd>
            </div>
        </dl>
    </div>

    {{-- Form perpanjangan --}}
    <div class="overflow-hidden border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)]">
        <div class="border-b border-[var(--dash-card-border)] bg-[var(--dash-page-bg)] px-5 py-4">
            <h3 class="font-jakarta text-sm font-bold text-[var(--dash-text-heading)]">Perpanjang Kontrak</h3>
        </div>
        <form action="{{ route('employment-statuses.extend.store', $status) }}" method="POST" class="px-5 py-5 space-y-5" novalidate>
            @csrf
            <div>
                <label for="new_end_date" class="block text-sm font-semibold text-[var(--dash-text-body)] mb-1.5">
                    Tanggal Akhir Kontrak Baru <span class="text-rose-500">*</span>
                </label>
                <input type="date"
                       id="new_end_date"
                       name="new_end_date"
                       value="{{ old('new_end_date') }}"
                       class="block w-full border-[var(--dash-input-border)] text-sm focus:border-[var(--dash-primary)] focus:ring-[var(--dash-primary)] @error('new_end_date') border-rose-500 @enderror">
                @error('new_end_date')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 border-t border-[var(--dash-card-border)] pt-5">
                <button type="submit"
                        class="inline-flex min-h-10 items-center gap-2 bg-[var(--dash-primary)] px-5 text-sm font-semibold text-white hover:opacity-90">
                    <span class="material-symbols-outlined text-[18px]">update</span>
                    Perpanjang Kontrak
                </button>
                <a href="{{ route('employees.show', $status->employee) }}"
                   class="inline-flex min-h-10 items-center gap-2 border border-[var(--dash-input-border)] px-5 text-sm font-semibold text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)]">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
