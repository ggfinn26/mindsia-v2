@extends('layouts.dashboard')

@section('title', 'Tambah Webhook - MINDSIA')
@section('header_title', 'Tambah Webhook')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Sistem & Akses</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Tambah Webhook</h1>
    </div>

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('system.webhooks.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-5">

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Nama <span style="color: #dc2626;">*</span>
                    </label>
                    <input name="name" type="text" value="{{ old('name') }}"
                           placeholder="Contoh: Notifikasi Telegram HR" required>
                    @error('name') <p class="mt-1 text-xs" style="color: #dc2626;">{{ $message }}</p> @enderror
                </div>

                {{-- Bot --}}
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Bot <span class="text-xs font-normal ml-1" style="color: var(--dash-text-muted);">(opsional)</span>
                    </label>
                    <select name="bot_id">
                        <option value="">Tanpa Bot</option>
                        @foreach($bots as $bot)
                            <option value="{{ $bot->id }}"
                                    @if(old('bot_id') == $bot->id) selected @endif>
                                {{ $bot->name }} ({{ ucfirst($bot->type) }})
                            </option>
                        @endforeach
                    </select>
                    @error('bot_id') <p class="mt-1 text-xs" style="color: #dc2626;">{{ $message }}</p> @enderror
                </div>

                {{-- Endpoint URL --}}
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Endpoint URL <span style="color: #dc2626;">*</span>
                    </label>
                    <input name="endpoint_url" type="url" value="{{ old('endpoint_url') }}"
                           placeholder="https://api.example.com/webhook/..."
                           class="font-mono" required>
                    <p class="mt-1.5 text-xs" style="color: var(--dash-text-faint);">
                        Hanya http/https. IP lokal & cloud metadata diblokir.
                    </p>
                    @error('endpoint_url') <p class="mt-1 text-xs" style="color: #dc2626;">{{ $message }}</p> @enderror
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Catatan <span class="text-xs font-normal ml-1" style="color: var(--dash-text-muted);">(opsional)</span>
                    </label>
                    <textarea name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes') <p class="mt-1 text-xs" style="color: #dc2626;">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-6 py-4 flex justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('system.webhooks.index') }}"
                   class="px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
