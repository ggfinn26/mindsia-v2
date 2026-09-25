@extends('layouts.dashboard')

@section('title', 'Perpanjang Kontrak — ' . $status->employee->full_name)
@section('header_title', 'Perpanjang Kontrak')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Perpanjang Kontrak</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $status->employee->full_name }} &mdash; {{ $status->position?->position_name }}</p>
        </div>
        <a href="{{ route('employees.show', $status->employee) }}"
           class="inline-flex min-h-10 items-center gap-2 border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">
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
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Kontrak Saat Ini</h3>
        </div>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-5 px-5 py-5 sm:grid-cols-4">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Nama</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $status->employee->full_name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Posisi</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $status->position?->position_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Mulai Kontrak</dt>
                <dd class="mt-1 text-sm text-slate-900">{{ $status->contract_start_date?->format('d M Y') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Akhir Kontrak</dt>
                <dd class="mt-1 text-sm font-semibold {{ $status->contract_end_date?->lte(now()->addDays(30)) ? 'text-amber-700' : 'text-slate-900' }}">
                    {{ $status->contract_end_date?->format('d M Y') ?? 'Tidak ada batas' }}
                </dd>
            </div>
        </dl>
    </div>

    {{-- Form perpanjangan --}}
    <div class="overflow-hidden border border-slate-200 bg-white">
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
            <h3 class="font-jakarta text-sm font-bold text-slate-900">Penawaran Perpanjangan Baru</h3>
        </div>
        <form action="{{ route('employment-statuses.extend.store', $status) }}" method="POST" class="px-5 py-5 space-y-5">
            @csrf
            <div>
                <label for="proposed_end_date" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Tanggal Akhir Kontrak Baru <span class="text-rose-500">*</span>
                </label>
                <input type="date"
                       id="proposed_end_date"
                       name="proposed_end_date"
                       value="{{ old('proposed_end_date') }}"
                       class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac] @error('proposed_end_date') border-rose-500 @enderror">
                @error('proposed_end_date')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Catatan <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <textarea id="notes"
                          name="notes"
                          rows="3"
                          placeholder="Alasan perpanjangan atau keterangan lainnya"
                          class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac] @error('notes') border-rose-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 border-t border-slate-100 pt-5">
                <button type="submit"
                        class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    Buat Penawaran
                </button>
                <a href="{{ route('employees.show', $status->employee) }}"
                   class="inline-flex min-h-10 items-center gap-2 border border-slate-300 px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
