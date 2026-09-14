@extends('layouts.dashboard')

@section('title', 'Ajukan Pengunduran Diri')
@section('header_title', 'Ajukan Pengunduran Diri')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('employees.show', $employee) }}" class="mb-1 flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-700">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> {{ $employee->full_name }}
        </a>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Ajukan Pengunduran Diri</h2>
        <p class="mt-1 text-sm text-slate-500">Permintaan akan diteruskan ke HR untuk diproses.</p>
    </div>

    @if ($errors->any())
        <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('employees.resign-requests.store', $employee) }}" class="border border-slate-200 bg-white p-6 space-y-4">
        @csrf
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Tanggal efektif pengunduran</label>
            <input type="date" name="effective_date" value="{{ old('effective_date') }}" required min="{{ today()->addDays(30)->toDateString() }}" class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
            <p class="mt-1 text-xs text-slate-400">Minimal 30 hari dari hari ini.</p>
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-slate-700">Alasan pengunduran diri</label>
            <textarea name="reason" rows="4" required class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]" placeholder="Jelaskan alasan pengunduran diri...">{{ old('reason') }}</textarea>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" onclick="return confirm('Yakin ajukan pengunduran diri?')" class="inline-flex min-h-10 items-center gap-2 bg-red-600 px-5 text-sm font-semibold text-white hover:bg-red-700">Ajukan</button>
            <a href="{{ route('employees.show', $employee) }}" class="inline-flex min-h-10 items-center border border-slate-300 px-4 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        </div>
    </form>
</div>
@endsection
