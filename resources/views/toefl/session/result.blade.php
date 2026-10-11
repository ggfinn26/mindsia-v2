@extends('layouts.dashboard')

@section('title', 'Hasil TOEFL')

@section('content')
@php
    $score = $session->score_total;

    $jokes = match(true) {
        $score === null => [
            'id' => '"Skormu belum keluar. Mungkin jawabannya masih dalam perjalanan... naik ojek."',
            'en' => '"Your score has not arrived yet. Perhaps your answers took the scenic route."',
            'label' => 'Score Pending',
        ],
        $score >= 600 => [
            'id' => '"Skormu tinggi banget. Jangan-jangan kamu diam-diam native speaker?"',
            'en' => '"At this score, airports stop giving you the English menu."',
            'label' => 'Excellent',
        ],
        $score >= 550 => [
            'id' => '"Hampir sempurna. Kurang dikit lagi buat jadi guru MINDSIA-mu sendiri."',
            'en' => '"Almost there. One more session and you could proofread our textbooks."',
            'label' => 'Good',
        ],
        $score >= 500 => [
            'id' => '"Solid. Cukup buat ngerti film Hollywood tanpa subtitle... sebagian besar."',
            'en' => '"Solid result. You can survive small talk with a native speaker. Probably."',
            'label' => 'Competent',
        ],
        $score >= 450 => [
            'id' => '"Lumayan. Kalau ada yang tanya, bilang aja skornya sedang dalam proses optimasi."',
            'en' => '"Not bad. Call it a work in progress and nobody will argue."',
            'label' => 'Fair',
        ],
        $score >= 400 => [
            'id' => '"Grammar-mu masih sering minta izin keluar kelas. Ajak balik yuk."',
            'en' => '"Your grammar keeps sneaking out of class. Time to bring it back."',
            'label' => 'Developing',
        ],
        default => [
            'id' => '"Ini bukan akhir. Ini cuma chapter 1 yang jujur."',
            'en' => '"This is not the end. It is just an honest chapter 1."',
            'label' => 'Keep Going',
        ],
    };

@endphp

<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6">

    {{-- Result card (also the capture target) --}}
    <div id="result-card" class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">

        {{-- Header strip --}}
        <div class="bg-[#5586DB] px-6 py-4 flex items-center justify-between">
            <img src="/img/logo/mindsia-logo.webp" alt="MINDSIA" class="h-10 w-auto brightness-0 invert opacity-90">
            <span class="text-[11px] font-bold text-[#1a1a1a] bg-[#fbbf24] px-3 py-1 rounded tracking-widest uppercase">{{ $jokes['label'] }}</span>
        </div>

        {{-- Score header --}}
        <div class="px-6 pt-6 pb-4 flex items-start justify-between border-b border-dashed border-gray-200">
            <div>
                <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase mb-1">Test</p>
                <p class="text-sm font-semibold text-gray-900">{{ optional($session->test)->test_name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Ujian Selesai</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase mb-1">Total ITP</p>
                <p class="font-bold text-[52px] leading-none text-[#f59e0b] tabular-nums">{{ $score ?? 'n/a' }}</p>
                <p class="text-[10px] text-gray-400 mt-1">Skala 310 hingga 677</p>
            </div>
        </div>

        {{-- Section breakdown --}}
        <div class="grid grid-cols-3 divide-x divide-gray-100 border-b border-gray-100">
            @foreach ([
                ['label' => 'Listening', 'score' => $session->score_listening],
                ['label' => 'Structure', 'score' => $session->score_structure],
                ['label' => 'Reading',   'score' => $session->score_reading],
            ] as $s)
                <div class="text-center py-5 px-3">
                    <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase mb-1.5">{{ $s['label'] }}</p>
                    <p class="text-2xl font-semibold text-gray-800 tabular-nums">{{ $s['score'] ?? 'n/a' }}</p>
                </div>
            @endforeach
        </div>

        {{-- Body: joke + meta --}}
        <div class="px-6 py-6 space-y-4">

            <div>
                <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase mb-2">Teacher's Note</p>
                <p class="text-sm font-semibold text-gray-900 leading-snug">{{ $jokes['id'] }}</p>
            </div>

            <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3.5">
                <p class="text-[10px] font-bold text-gray-400 tracking-widest uppercase mb-1.5">In English</p>
                <p class="text-sm text-gray-600 italic leading-relaxed">{{ $jokes['en'] }}</p>
            </div>

            @if ($session->completed_at)
                @php
                    $secs = abs($session->started_at?->diffInSeconds($session->completed_at) ?? 0);
                    $h = intdiv($secs, 3600);
                    $m = intdiv($secs % 3600, 60);
                    $durationFormatted = $h > 0 ? "{$h} jam {$m} menit" : "{$m} menit";
                @endphp
                <p class="text-xs text-gray-400 text-center">
                    Selesai {{ $session->completed_at->format('d M Y, H:i') }}
                    @if ($session->started_at)
                        &middot; Durasi {{ $durationFormatted }}
                    @endif
                </p>
            @endif

            {{-- Actions --}}
            <div class="pt-2 flex flex-wrap gap-3 justify-end border-t border-gray-100">
                <a href="{{ route('toefl.session.index') }}"
                    class="btn bg-white border border-gray-200 hover:border-gray-300 text-gray-700">
                    Kembali ke Daftar
                </a>
                <a href="{{ route('member.dashboard') }}"
                    class="btn bg-[#fbbf24] hover:bg-[#f59e0b] text-gray-900 font-semibold">
                    Dashboard
                </a>
            </div>
        </div>
    </div>

</div>

@endsection


