@extends('layouts.dashboard')

@section('title', 'Pengunduran Diri - MINDSIA')
@section('header_title', 'Pengunduran Diri')

@section('content')
<div class="max-w-2xl mx-auto pt-8 sm:pt-16">

    @if (session('success'))
        <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    @if ($pending)
        {{-- Status permintaan pending --}}
        <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 sm:px-8 py-4 border-b flex items-center gap-3"
                 style="border-color: #fde68a; background-color: #fffbeb;">
                <span class="material-symbols-outlined text-[18px]" style="color: #b45309;">pending</span>
                <h3 class="text-sm font-bold font-jakarta" style="color: #92400e;">Permintaan Sedang Diproses</h3>
            </div>
            <div class="px-6 sm:px-8 py-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Tanggal Efektif</dt>
                        <dd class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                            {{ $pending->resign_date?->translatedFormat('d F Y') ?? '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Status</dt>
                        <dd class="mt-0.5">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold"
                                  style="background-color: #fef3c7; color: #b45309;">
                                Menunggu Review HR
                            </span>
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Alasan</dt>
                        <dd class="text-sm" style="color: var(--dash-text-body);">{{ $pending->reason ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Diajukan</dt>
                        <dd class="text-sm" style="color: var(--dash-text-muted);">
                            {{ $pending->created_at->translatedFormat('d F Y, H:i') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

    @else
        {{-- Form pengajuan --}}
        @if ($errors->any())
            <div class="mb-5 px-4 py-3 rounded-xl border text-sm"
                 style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
                <ul class="list-disc list-inside space-y-0.5 font-medium">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <form method="POST" action="{{ route('resign-requests.my-store') }}" novalidate>
                @csrf

                <div class="px-6 sm:px-8 py-6">
                    <div class="flex items-center gap-2.5 mb-5">
                        <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                        <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Ajukan Pengunduran Diri</h3>
                    </div>
                    <p class="text-sm mb-6 -mt-4" style="color: var(--dash-text-muted);">
                        Permintaan akan diteruskan ke HR untuk diproses.
                    </p>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                                Tanggal efektif pengunduran
                                <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="date" name="resign_date"
                                   value="{{ old('resign_date') }}"
                                   min="{{ today()->addDay()->toDateString() }}"
                                   @class(['border-rose-500' => $errors->has('resign_date')])>
                            @error('resign_date')
                                <p class="mt-1 text-xs" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                                Alasan pengunduran diri
                                <span style="color: #dc2626;">*</span>
                            </label>
                            <textarea name="reason" rows="4"
                                      placeholder="Jelaskan alasan pengunduran diri..."
                                      @class(['border-rose-500' => $errors->has('reason')])>{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="mt-1 text-xs" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="px-6 sm:px-8 py-4 border-t flex items-center justify-end"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-primary-bg);">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white transition-colors"
                            style="background-color: #dc2626;">
                        <span class="material-symbols-outlined text-[17px]">exit_to_app</span>
                        Ajukan Pengunduran Diri
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
