@extends('layouts.dashboard')

@section('title', 'TOEFL - MINDSIA')

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Ujian TOEFL</h2>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded text-sm">{{ session('error') }}</div>
    @endif

    {{-- Available Tests --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Test Tersedia</h3>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse ($availableTests as $test)
                <div class="px-6 py-4 flex items-center justify-between gap-4" x-data="{ showForm: false }">
                    <div>
                        <p class="font-medium text-gray-900">{{ $test->test_name }}</p>
                        <p class="text-sm text-gray-500 mt-0.5">
                            Listening {{ $test->listening_time_limit }}m &bull;
                            Structure {{ $test->structure_time_limit }}m &bull;
                            Reading {{ $test->reading_time_limit }}m
                        </p>
                    </div>
                    <div class="shrink-0">
                        <button type="button" @click="showForm = !showForm"
                            class="btn bg-indigo-500 hover:bg-indigo-600 text-white text-sm">
                            Mulai Ujian
                        </button>
                    </div>
                </div>
                <div x-show="showForm" style="display:none" class="px-6 pb-4">
                    <form action="{{ route('toefl.session.start', $test) }}" method="POST" class="flex items-end gap-3">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Password Test
                                @if (!$test->password)
                                    <span class="text-gray-400">(tidak diperlukan)</span>
                                @endif
                            </label>
                            <input name="password" type="password" class="form-input w-full text-sm"
                                placeholder="{{ $test->password ? 'Masukkan password test' : 'Biarkan kosong' }}" />
                        </div>
                        <button type="submit" class="btn bg-green-500 hover:bg-green-600 text-white text-sm">Konfirmasi & Mulai</button>
                        <button type="button" @click="showForm = false" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 text-sm">Batal</button>
                    </form>
                </div>
            @empty
                <div class="px-6 py-10 text-center text-sm text-gray-400">
                    Belum ada test yang tersedia saat ini.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Session History --}}
    <div class="bg-white shadow-sm rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Riwayat Ujian</h3>
        </div>
        @if ($sessions->isEmpty())
            <div class="px-6 py-10 text-center text-sm text-gray-400">Belum ada riwayat ujian.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left">Test</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-right">Listening</th>
                            <th class="px-6 py-3 text-right">Structure</th>
                            <th class="px-6 py-3 text-right">Reading</th>
                            <th class="px-6 py-3 text-right">Total</th>
                            <th class="px-6 py-3 text-right">Tanggal</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($sessions as $session)
                            <tr>
                                <td class="px-6 py-3 font-medium text-gray-800">{{ optional($session->test)->test_name ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    @php
                                        $badge = match($session->status) {
                                            'completed' => 'bg-green-100 text-green-700',
                                            'expired'   => 'bg-red-100 text-red-700',
                                            default     => 'bg-yellow-100 text-yellow-700',
                                        };
                                        $label = match($session->status) {
                                            'completed'   => 'Selesai',
                                            'expired'     => 'Kedaluwarsa',
                                            'in_progress' => 'Berlangsung',
                                            default       => $session->status,
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 text-xs rounded-full {{ $badge }}">{{ $label }}</span>
                                </td>
                                <td class="px-6 py-3 text-right font-mono">{{ $session->score_listening ?? '—' }}</td>
                                <td class="px-6 py-3 text-right font-mono">{{ $session->score_structure ?? '—' }}</td>
                                <td class="px-6 py-3 text-right font-mono">{{ $session->score_reading ?? '—' }}</td>
                                <td class="px-6 py-3 text-right font-mono font-bold text-indigo-700">{{ $session->score_total ?? '—' }}</td>
                                <td class="px-6 py-3 text-right text-gray-500">{{ $session->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-right">
                                    @if ($session->status === 'completed')
                                        <a href="{{ route('toefl.session.result', $session) }}" class="text-indigo-600 hover:text-indigo-800 text-xs">Lihat Hasil</a>
                                    @elseif ($session->status === 'in_progress')
                                        @php
                                            $nextRoute = $session->listening_submitted_at
                                                ? ($session->structure_submitted_at
                                                    ? route('toefl.session.reading', $session)
                                                    : route('toefl.session.structure', $session))
                                                : route('toefl.session.listening', $session);
                                        @endphp
                                        <a href="{{ $nextRoute }}" class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">Lanjutkan</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-gray-100">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
