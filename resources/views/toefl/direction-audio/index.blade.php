@extends('layouts.dashboard')

@section('title', 'Audio Instruksi TOEFL')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">

    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Audio Instruksi TOEFL</h1>
        <p class="text-sm text-[color:var(--dash-text-muted)] mt-0.5">Path file audio instruksi yang diputar sebelum tiap bagian Listening.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] divide-y divide-[color:var(--dash-card-border)]">

        @foreach (['A' => 'Part A — Short Conversations', 'B' => 'Part B — Longer Conversations', 'C' => 'Part C — Talks'] as $part => $label)
            @php $audio = $audios[$part] ?? null; @endphp
            <div class="px-6 py-5" x-data="{ editing: false }">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-indigo-600 mb-0.5">Part {{ $part }}</p>
                        <p class="text-sm font-medium text-[color:var(--dash-text-heading)]">{{ $label }}</p>
                        @if ($audio)
                            <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5 font-mono break-all">{{ $audio->file_path }}</p>
                        @else
                            <p class="text-xs text-amber-600 mt-0.5">Belum diatur — pakai fallback hardcoded.</p>
                        @endif
                    </div>
                    @can('toefl.direction-audio.update')
                        <button type="button" @click="editing = !editing"
                            class="inline-flex items-center justify-center px-3 py-2 min-h-[44px] min-w-[44px] rounded-md text-sm font-medium transition-colors shrink-0"
                            :class="editing ? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]' : 'text-indigo-600 bg-indigo-50 hover:bg-indigo-100'"
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
                                class="form-input flex-1 text-sm font-mono min-h-[44px]"
                                placeholder="toefl-audio/SECTION-A-INSTRUCTIONS.mp3" />
                            <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-lg bg-[var(--dash-primary)] hover:opacity-90 text-white text-sm font-medium transition-colors shrink-0 gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">save</span>
                                Simpan
                            </button>
                        </div>
                        <p class="text-xs text-[color:var(--dash-text-faint)] mt-1">Path relatif dari folder <code class="font-mono">public/</code>, contoh: <code class="font-mono">toefl-audio/SECTION-A-INSTRUCTIONS.mp3</code></p>
                    </form>
                @endcan
            </div>
        @endforeach

    </div>

    <div class="mt-4">
        <a href="{{ route('toefl-tests.index') }}"
            class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-[var(--dash-card-bg)] border border-[color:var(--dash-input-border)] hover:bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)] text-sm font-medium transition-colors gap-1.5">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Daftar Test
        </a>
    </div>

</div>
@endsection
