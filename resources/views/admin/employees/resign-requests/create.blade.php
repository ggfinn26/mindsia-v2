@extends('layouts.dashboard')

@section('title', 'Ajukan Pengunduran Diri - MINDSIA')
@section('header_title', 'Ajukan Pengunduran Diri')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('employees.show', $employee->id) }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke {{ $employee->full_name }}
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Kelola Karyawan</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Ajukan Pengunduran Diri</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Permintaan akan diteruskan ke HR untuk diproses.
        </p>
    </div>

    {{-- Error Flash Messages --}}
    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form Card --}}
    <div class="rounded-2xl"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg); border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Formulir Pengunduran Diri</h2>
        </div>
        <form method="POST" action="{{ route('resign-requests.store', $employee) }}" class="p-6 space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Tanggal Efektif Pengunduran <span class="text-red-500">*</span>
                </label>
                <input type="date" name="resign_date" value="{{ old('resign_date') }}" required
                       min="{{ today()->addDays(30)->toDateString() }}"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                <p class="mt-1 text-xs" style="color: var(--dash-text-muted);">Minimal 30 hari dari hari ini.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Alasan Pengunduran Diri <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" rows="4" required
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          placeholder="Jelaskan alasan pengunduran diri..."
                          onfocus="this.style.borderColor='var(--dash-primary)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('reason') }}</textarea>
            </div>
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <button type="submit" onclick="return confirm('Yakin ajukan pengunduran diri?')"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-danger, #dc2626);">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    Ajukan Pengunduran Diri
                </button>
                <a href="{{ route('employees.show', $employee->id) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
