@extends('layouts.dashboard')

@section('title', 'Pengunduran Diri - MINDSIA')
@section('header_title', 'Pengunduran Diri')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-2xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('employee.profile.show') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Profile Saya
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Pengaturan Akun</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Pengunduran Diri</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Permintaan akan diteruskan ke HR untuk diproses.</p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    @if($pending)
    {{-- Permintaan sedang diproses --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: #fffbeb; border: 1px solid #fde68a;">
        <div class="px-5 py-4 flex items-center gap-3"
             style="border-bottom: 1px solid #fde68a; background-color: #fef3c7;">
            <span class="material-symbols-outlined text-[20px]" style="color: #92400e;">pending</span>
            <h2 class="text-sm font-bold font-jakarta" style="color: #92400e;">Permintaan Sedang Diproses</h2>
        </div>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 px-5 py-5 text-sm">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #92400e; opacity: 0.7;">Tanggal Efektif</dt>
                <dd class="font-semibold" style="color: #78350f;">{{ $pending->resign_date?->translatedFormat('d F Y') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #92400e; opacity: 0.7;">Status</dt>
                <dd>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                          style="background-color: #fde68a; color: #92400e;">Menunggu Review HR</span>
                </dd>
            </div>
            <div class="col-span-2">
                <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #92400e; opacity: 0.7;">Alasan</dt>
                <dd style="color: #78350f;">{{ $pending->reason ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: #92400e; opacity: 0.7;">Diajukan</dt>
                <dd style="color: #92400e; opacity: 0.8;">{{ $pending->created_at->translatedFormat('d F Y, H:i') }}</dd>
            </div>
        </dl>
    </div>

    @else
    {{-- Form pengajuan --}}
    @if($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Ajukan Pengunduran Diri</h2>
        </div>
        <form method="POST" action="{{ route('resign-requests.my-store') }}" class="px-6 py-5 space-y-5" novalidate>
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                    Tanggal efektif pengunduran <span class="text-red-500">*</span>
                </label>
                <input type="date" name="resign_date" value="{{ old('resign_date') }}"
                       min="{{ today()->addDay()->toDateString() }}"
                       class="block w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all @error('resign_date') border-red-500 @enderror"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='{{ $errors->has('resign_date') ? '#ef4444' : 'var(--dash-input-border)' }}'; this.style.boxShadow='none'">
                @error('resign_date')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                    Alasan pengunduran diri <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" rows="4"
                          placeholder="Jelaskan alasan pengunduran diri..."
                          class="block w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all @error('reason') border-red-500 @enderror"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='{{ $errors->has('reason') ? '#ef4444' : 'var(--dash-input-border)' }}'; this.style.boxShadow='none'">{{ old('reason') }}</textarea>
                @error('reason')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-2"
                 style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('employee.profile.show') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: #dc2626;">
                    <span class="material-symbols-outlined text-[16px]">exit_to_app</span>
                    Ajukan Pengunduran Diri
                </button>
            </div>
        </form>
    </div>
    @endif

</div>
@endsection
