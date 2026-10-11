@extends('layouts.dashboard')

@section('title', 'Audio Instruksi TOEFL')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">

    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Audio Instruksi TOEFL</h1>
        <p class="text-sm text-gray-500 mt-0.5">Path file audio instruksi yang diputar sebelum tiap bagian Listening.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 divide-y divide-gray-100">

        @foreach (['A' => 'Part A — Short Conversations', 'B' => 'Part B — Longer Conversations', 'C' => 'Part C — Talks'] as $part => $label)
            @php $audio = $audios[$part] ?? null; @endphp
            <div class="px-6 py-5" x-data="{ editing: false }">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-indigo-600 mb-0.5">Part {{ $part }}</p>
                        <p class="text-sm font-medium text-gray-800">{{ $label }}</p>
                        @if ($audio)
                            <p class="text-xs text-gray-400 mt-0.5 font-mono break-all">{{ $audio->file_path }}</p>
                        @else
                            <p class="text-xs text-amber-600 mt-0.5">Belum diatur — pakai fallback hardcoded.</p>
                        @endif
                    </div>
                    @can('toefl.direction-audio.update')
                        <button type="button" @click="editing = !editing"
                            class="inline-flex items-center justify-center px-3 py-2 min-h-[44px] min-w-[44px] rounded-md text-sm font-medium transition-colors shrink-0"
                            :class="editing ? 'bg-gray-100 text-gray-600' : 'text-indigo-600 bg-indigo-50 hover:bg-indigo-100'"
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
                                class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition-colors shrink-0">
                                Simpan
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Path relatif dari folder <code class="font-mono">public/</code>, contoh: <code class="font-mono">toefl-audio/SECTION-A-INSTRUCTIONS.mp3</code></p>
                    </form>
                @endcan
            </div>
        @endforeach

    </div>

    <div class="mt-4">
        <a href="{{ route('toefl-tests.index') }}"
            class="inline-flex items-center justify-center px-4 py-2 min-h-[44px] min-w-[44px] rounded-md bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium transition-colors">
            &larr; Kembali ke Daftar Test
        </a>
    </div>

</div>
@endsection
