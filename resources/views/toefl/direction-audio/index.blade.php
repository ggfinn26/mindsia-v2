@extends('layouts.dashboard')

@section('title', 'Audio Instruksi TOEFL')
@section('header_title', 'Audio Instruksi TOEFL')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    <div>
        <a href="{{ route('toefl-tests.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Bank Test
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">TOEFL</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Audio Instruksi TOEFL</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Path file audio instruksi yang diputar sebelum tiap bagian Listening.</p>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @foreach (['A' => 'Part A - Short Conversations', 'B' => 'Part B - Longer Conversations', 'C' => 'Part C - Talks'] as $part => $label)
            @php $audio = $audios[$part] ?? null; @endphp
            <div class="px-6 py-5" x-data="{ editing: false }"
                 style="{{ !$loop->last ? 'border-bottom: 1px solid var(--dash-card-border);' : '' }}">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider mb-0.5"
                           style="color: var(--dash-primary-text);">Part {{ $part }}</p>
                        <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $label }}</p>
                        @if ($audio)
                            <p class="text-xs font-mono break-all mt-0.5" style="color: var(--dash-text-faint);">{{ $audio->file_path }}</p>
                        @else
                            <p class="text-xs mt-0.5" style="color: #d97706;">Belum diatur - pakai fallback hardcoded.</p>
                        @endif
                    </div>
                    @can('toefl.direction-audio.update')
                        <button type="button" @click="editing = !editing"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors shrink-0"
                                :style="editing
                                    ? 'color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);'
                                    : 'color: var(--dash-text-body); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);'"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                                x-text="editing ? 'Batal' : 'Ubah'">
                        </button>
                    @endcan
                </div>

                @if ($audio)
                    <audio controls class="w-full h-8 mb-3" preload="none">
                        <source src="{{ asset($audio->file_path) }}" type="audio/mpeg">
                    </audio>
                @endif

                @can('toefl.direction-audio.update')
                    <form x-show="editing" style="display:none"
                          action="{{ route('toefl.direction-audio.update', $part) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="flex gap-2 mt-2">
                            <input type="text" name="file_path" required
                                   value="{{ $audio?->file_path ?? 'toefl-audio/SECTION-' . $part . '-INSTRUCTIONS.mp3' }}"
                                   placeholder="toefl-audio/SECTION-A-INSTRUCTIONS.mp3"
                                   class="flex-1 px-4 py-2.5 text-sm font-mono rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[16px]">save</span>Simpan
                            </button>
                        </div>
                        <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Path relatif dari folder <code class="font-mono">public/</code>, contoh: <code class="font-mono">toefl-audio/SECTION-A-INSTRUCTIONS.mp3</code></p>
                    </form>
                @endcan
            </div>
        @endforeach
    </div>

</div>
@endsection
