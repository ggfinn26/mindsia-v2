@extends('layouts.dashboard')

@section('title', 'Edit Anggaran')
@section('header_title', 'Edit Anggaran')

@section('content')
@php $months = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp

<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('budget-estimates.show', $budgetEstimate) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $budgetEstimate->title }}
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Edit Anggaran</h2>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('budget-estimates.update', $budgetEstimate) }}" class="border border-slate-200 bg-white p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Judul anggaran</label>
            <input type="text" name="title" value="{{ old('title', $budgetEstimate->title) }}" required maxlength="255" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Bulan</label>
                <select name="period_month" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    @foreach (array_slice($months, 1, null, true) as $num => $label)
                        <option value="{{ $num }}" @selected(old('period_month', $budgetEstimate->period_month) == $num)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-slate-700">Tahun</label>
                <input type="number" name="period_year" value="{{ old('period_year', $budgetEstimate->period_year) }}" required min="2020" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            </div>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi</label>
            <textarea name="description" rows="3" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">{{ old('description', $budgetEstimate->description) }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">Simpan</button>
            <a href="{{ route('budget-estimates.show', $budgetEstimate) }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</div>
@endsection
