@extends('layouts.dashboard')

@section('title', 'Hasil TOEFL')
@section('header_title', 'Hasil Ujian TOEFL')

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

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-2xl mx-auto space-y-6">

    <div>
        <a href="{{ route('toefl.session.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Ujian
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">TOEFL</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Hasil Ujian</h1>
    </div>

    {{-- Result card --}}
    <div id="result-card" class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        {{-- Header strip --}}
        <div class="px-6 py-4 flex items-center justify-between"
             style="background-color: var(--dash-primary);">
            <img src="/img/logo/mindsia-logo.webp" alt="MINDSIA" class="h-10 w-auto brightness-0 invert opacity-90">
            <span class="text-[11px] font-bold text-gray-900 px-3 py-1 rounded tracking-widest uppercase"
                  style="background-color: #fbbf24;">{{ $jokes['label'] }}</span>
        </div>

        {{-- Score header --}}
        <div class="px-6 pt-6 pb-4 flex items-start justify-between"
             style="border-bottom: 1px dashed var(--dash-card-border);">
            <div>
                <p class="text-[10px] font-bold tracking-widest uppercase mb-1" style="color: var(--dash-text-faint);">Test</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ optional($session->test)->test_name }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">Ujian Selesai</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold tracking-widest uppercase mb-1" style="color: var(--dash-text-faint);">Total ITP</p>
                <p class="font-bold text-[52px] leading-none tabular-nums" style="color: #f59e0b;">{{ $score ?? 'n/a' }}</p>
                <p class="text-[10px] mt-1" style="color: var(--dash-text-faint);">Skala 310 hingga 677</p>
            </div>
        </div>

        {{-- Section breakdown --}}
        <div class="grid grid-cols-3" style="border-bottom: 1px solid var(--dash-card-border);">
            @foreach ([
                ['label' => 'Listening', 'score' => $session->score_listening],
                ['label' => 'Structure', 'score' => $session->score_structure],
                ['label' => 'Reading',   'score' => $session->score_reading],
            ] as $s)
                <div class="text-center py-5 px-3"
                     style="{{ !$loop->last ? 'border-right: 1px solid var(--dash-card-border);' : '' }}">
                    <p class="text-[10px] font-bold tracking-widest uppercase mb-1.5" style="color: var(--dash-text-faint);">{{ $s['label'] }}</p>
                    <p class="text-2xl font-semibold tabular-nums" style="color: var(--dash-text-heading);">{{ $s['score'] ?? 'n/a' }}</p>
                </div>
            @endforeach
        </div>

        {{-- Body: quote + meta --}}
        <div class="px-6 py-6 space-y-4">
            <div>
                <p class="text-[10px] font-bold tracking-widest uppercase mb-2" style="color: var(--dash-text-faint);">Teacher's Note</p>
                <p class="text-sm font-semibold leading-snug" style="color: var(--dash-text-heading);">{{ $jokes['id'] }}</p>
            </div>

            <div class="px-4 py-3.5 rounded-xl"
                 style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-[10px] font-bold tracking-widest uppercase mb-1.5" style="color: var(--dash-text-faint);">In English</p>
                <p class="text-sm italic leading-relaxed" style="color: var(--dash-text-muted);">{{ $jokes['en'] }}</p>
            </div>

            @if ($session->completed_at)
                @php
                    $secs = abs($session->started_at?->diffInSeconds($session->completed_at) ?? 0);
                    $h = intdiv($secs, 3600);
                    $m = intdiv($secs % 3600, 60);
                    $durationFormatted = $h > 0 ? "{$h} jam {$m} menit" : "{$m} menit";
                @endphp
                <p class="text-xs text-center" style="color: var(--dash-text-faint);">
                    Selesai {{ $session->completed_at->format('d M Y, H:i') }}
                    @if ($session->started_at)
                        &middot; Durasi {{ $durationFormatted }}
                    @endif
                </p>
            @endif

            <div class="pt-2 flex flex-wrap gap-3 justify-end"
                 style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('toefl.session.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">list</span>Kembali ke Daftar
                </a>
                <a href="{{ route('member.dashboard') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-gray-900 hover:opacity-90 transition-opacity"
                   style="background-color: #fbbf24;">
                    <span class="material-symbols-outlined text-[16px]">dashboard</span>Dashboard
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
