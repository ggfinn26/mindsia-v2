@extends('layouts.dashboard')

@section('title', 'Hasil TOEFL')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        {{-- Top banner --}}
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-8 py-10 text-center text-white">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white bg-opacity-20 mb-4">
                <span class="material-symbols-outlined text-4xl">workspace_premium</span>
            </div>
            <h1 class="text-2xl font-bold mb-1">Ujian Selesai</h1>
            <p class="text-indigo-100 text-sm">{{ optional($session->test)->test_name }}</p>
        </div>

        {{-- Total Score --}}
        <div class="text-center py-8 border-b border-gray-100">
            <p class="text-sm text-gray-400 uppercase font-semibold tracking-wider mb-2">Total Skor ITP</p>
            <p class="text-6xl font-extrabold text-gray-900">{{ $session->score_total ?? '—' }}</p>
            @if ($session->score_total)
                <p class="text-sm text-gray-400 mt-2">Range 310–677</p>
            @endif
        </div>

        {{-- Section breakdown --}}
        <div class="grid grid-cols-3 divide-x divide-gray-100 py-6">
            @foreach ([
                ['label' => 'Listening',  'score' => $session->score_listening,  'color' => 'text-indigo-600'],
                ['label' => 'Structure',  'score' => $session->score_structure,  'color' => 'text-purple-600'],
                ['label' => 'Reading',    'score' => $session->score_reading,    'color' => 'text-green-600'],
            ] as $s)
                <div class="text-center px-4">
                    <p class="text-xs text-gray-400 uppercase font-semibold mb-2">{{ $s['label'] }}</p>
                    <p class="text-3xl font-bold {{ $s['color'] }}">{{ $s['score'] ?? '—' }}</p>
                </div>
            @endforeach
        </div>

        {{-- Meta --}}
        <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 text-sm text-gray-500 space-y-1">
            @if ($session->completed_at)
                <p>Selesai: {{ $session->completed_at->format('d M Y, H:i') }}</p>
            @endif
            @if ($session->started_at && $session->completed_at)
                @php
                    $duration = $session->started_at->diffInMinutes($session->completed_at);
                @endphp
                <p>Durasi pengerjaan: {{ $duration }} menit</p>
            @endif
        </div>

        {{-- Actions --}}
        <div class="px-8 py-6 flex flex-wrap gap-3 justify-center border-t border-gray-100">
            <a href="{{ route('toefl.session.index') }}"
                class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-700">
                Kembali ke Daftar
            </a>
            <a href="{{ route('member.dashboard') }}"
                class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
