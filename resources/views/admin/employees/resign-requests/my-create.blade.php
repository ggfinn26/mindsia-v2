@extends('layouts.dashboard')

@section('title', 'Pengunduran Diri')
@section('header_title', 'Pengunduran Diri')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Pengunduran Diri</h2>
        <p class="mt-1 text-sm text-slate-500">Permintaan akan diteruskan ke HR untuk diproses.</p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="flex gap-3 border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    @if ($pending)
        <div class="overflow-hidden border border-amber-200 bg-amber-50">
            <div class="flex items-center gap-3 border-b border-amber-200 bg-amber-100 px-5 py-4">
                <span class="material-symbols-outlined text-amber-700">pending</span>
                <h3 class="font-jakarta text-sm font-bold text-amber-900">Permintaan Sedang Diproses</h3>
            </div>
            <dl class="grid grid-cols-2 gap-4 px-5 py-5 text-sm">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tanggal Efektif</dt>
                    <dd class="mt-1 font-medium text-slate-900">{{ $pending->resign_date?->translatedFormat('d F Y') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Menunggu Review HR</span>
                    </dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Alasan</dt>
                    <dd class="mt-1 text-slate-700">{{ $pending->reason ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Diajukan</dt>
                    <dd class="mt-1 text-slate-500">{{ $pending->created_at->translatedFormat('d F Y, H:i') }}</dd>
                </div>
            </dl>
        </div>
    @else
        @if ($errors->any())
            <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="overflow-hidden border border-slate-200 bg-white">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h3 class="font-jakarta text-sm font-bold text-slate-900">Ajukan Pengunduran Diri</h3>
            </div>
            <form method="POST" action="{{ route('resign-requests.my-store') }}" class="px-5 py-5 space-y-5" novalidate>
                @csrf
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Tanggal efektif pengunduran <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="resign_date" value="{{ old('resign_date') }}"
                           min="{{ today()->addDay()->toDateString() }}"
                           class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac] @error('resign_date') border-rose-500 @enderror">
                    @error('resign_date')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-slate-700">
                        Alasan pengunduran diri <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="reason" rows="4"
                              class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac] @error('reason') border-rose-500 @enderror"
                              placeholder="Jelaskan alasan pengunduran diri...">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex gap-3 border-t border-slate-100 pt-4">
                    <button type="submit"
                            class="inline-flex min-h-10 items-center gap-2 bg-rose-600 px-5 text-sm font-semibold text-white hover:bg-rose-700">
                        <span class="material-symbols-outlined text-[18px]">exit_to_app</span>
                        Ajukan Pengunduran Diri
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
