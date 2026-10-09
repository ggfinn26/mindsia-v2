@extends('layouts.dashboard')

@section('title', 'TOEFL - MINDSIA')

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

    {{-- Page header --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-[color:var(--dash-text-heading)]">Ujian TOEFL</h2>
        <p class="mt-1 text-sm text-[color:var(--dash-text-muted)]">Pilih test yang tersedia, atau lihat riwayat hasil ujian kamu.</p>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    {{-- Available Tests --}}
    <section class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-2xl shadow-sm mb-6" aria-label="Test tersedia">
        <div class="px-6 py-4 border-b border-[color:var(--dash-card-border)] flex items-center justify-between">
            <h3 class="font-semibold text-[color:var(--dash-text-heading)] text-sm">Test Tersedia</h3>
            <span class="text-xs text-[color:var(--dash-text-faint)]">{{ $availableTests->count() }} test</span>
        </div>
        <div class="divide-y divide-[color:var(--dash-card-border)]">
            @forelse ($availableTests as $test)
                <div x-data="{ showForm: false }">
                    <div class="px-6 py-4 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-medium text-[color:var(--dash-text-heading)] truncate">{{ $test->test_name }}</p>
                            <p class="text-xs text-[color:var(--dash-text-faint)] mt-0.5">
                                Listening {{ $test->listening_time_limit }}m
                                <span class="mx-1 text-gray-200">|</span>
                                Structure {{ $test->structure_time_limit }}m
                                <span class="mx-1 text-gray-200">|</span>
                                Reading {{ $test->reading_time_limit }}m
                            </p>
                        </div>
                        <div class="shrink-0">
                            <button
                                type="button"
                                @click="showForm = !showForm"
                                :aria-expanded="showForm"
                                class="btn bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4"
                            >
                                Mulai Ujian
                            </button>
                        </div>
                    </div>

                    {{-- Inline start form --}}
                    <div
                        x-show="showForm"
                        x-transition:enter="transition duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        style="display:none"
                        class="px-6 pb-5 pt-1 bg-[var(--dash-nav-hover-bg)] border-t border-[color:var(--dash-card-border)]"
                    >
                        <form action="{{ route('toefl.session.start', $test) }}" method="POST" class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
                            @csrf
                            <div class="flex-1 w-full sm:w-auto">
                                <label class="block text-xs font-medium text-[color:var(--dash-text-body)] mb-1">
                                    Password Test
                                    @if (!$test->password)
                                        <span class="font-normal text-[color:var(--dash-text-faint)]">(tidak diperlukan)</span>
                                    @endif
                                </label>
                                <div class="relative" x-data="{ showPw: false }">
                                    <input
                                        name="password"
                                        :type="showPw ? 'text' : 'password'"
                                        class="form-input w-full text-sm pr-10"
                                        placeholder="{{ $test->password ? 'Masukkan password test' : 'Biarkan kosong jika tidak ada password' }}"
                                    />
                                    <button type="button" @click="showPw = !showPw"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-[color:var(--dash-text-faint)] hover:text-[color:var(--dash-text-body)]">
                                        <svg x-show="!showPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showPw" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <button type="submit" class="btn bg-indigo-600 hover:bg-indigo-700 text-white text-sm">Konfirmasi &amp; Mulai</button>
                                <button type="button" @click="showForm = false" class="btn bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] hover:border-[color:var(--dash-input-border)] text-[color:var(--dash-text-body)] text-sm">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <p class="text-sm font-medium text-[color:var(--dash-text-muted)]">Belum ada test yang tersedia</p>
                    <p class="text-xs text-[color:var(--dash-text-faint)] mt-1">Hubungi pengajar untuk mengaktifkan test TOEFL kamu.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Session History --}}
    <section class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-2xl shadow-sm" aria-label="Riwayat ujian">
        <div class="px-6 py-4 border-b border-[color:var(--dash-card-border)]">
            <h3 class="font-semibold text-[color:var(--dash-text-heading)] text-sm">Riwayat Ujian</h3>
        </div>

        @if ($sessions->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-sm font-medium text-[color:var(--dash-text-muted)]">Belum ada riwayat ujian</p>
                <p class="text-xs text-[color:var(--dash-text-faint)] mt-1">Hasil ujian yang sudah diselesaikan akan tampil di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[color:var(--dash-card-border)] text-xs text-[color:var(--dash-text-muted)] bg-[var(--dash-nav-hover-bg)]">
                            <th class="px-6 py-3 text-left font-medium">Test</th>
                            <th class="px-6 py-3 text-left font-medium">Status</th>
                            <th class="px-6 py-3 text-right font-medium">Listening</th>
                            <th class="px-6 py-3 text-right font-medium">Structure</th>
                            <th class="px-6 py-3 text-right font-medium">Reading</th>
                            <th class="px-6 py-3 text-right font-medium">Total</th>
                            <th class="px-6 py-3 text-right font-medium">Tanggal</th>
                            <th class="px-6 py-3 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[color:var(--dash-card-border)]">
                        @foreach ($sessions as $session)
                            <tr class="hover:bg-[var(--dash-nav-hover-bg)] transition-colors">
                                <td class="px-6 py-3 font-medium text-[color:var(--dash-text-heading)] max-w-[180px] truncate">{{ optional($session->test)->test_name ?? 'Test dihapus' }}</td>
                                <td class="px-6 py-3">
                                    @php
                                        $badge = match($session->status) {
                                            'completed'   => 'bg-green-50 text-green-700 ring-1 ring-green-200',
                                            'expired'     => 'bg-red-50 text-red-700 ring-1 ring-red-200',
                                            default       => 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
                                        };
                                        $label = match($session->status) {
                                            'completed'   => 'Selesai',
                                            'expired'     => 'Kedaluwarsa',
                                            'in_progress' => 'Berlangsung',
                                            default       => $session->status,
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded {{ $badge }}">{{ $label }}</span>
                                </td>
                                <td class="px-6 py-3 text-right font-mono text-[color:var(--dash-text-body)]">{{ $session->score_listening ?? 'n/a' }}</td>
                                <td class="px-6 py-3 text-right font-mono text-[color:var(--dash-text-body)]">{{ $session->score_structure ?? 'n/a' }}</td>
                                <td class="px-6 py-3 text-right font-mono text-[color:var(--dash-text-body)]">{{ $session->score_reading ?? 'n/a' }}</td>
                                <td class="px-6 py-3 text-right font-mono font-bold text-indigo-700">{{ $session->score_total ?? 'n/a' }}</td>
                                <td class="px-6 py-3 text-right text-[color:var(--dash-text-faint)] tabular-nums">{{ $session->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-3 text-right">
                                    @if ($session->status === 'completed')
                                        <a href="{{ route('toefl.session.result', $session) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">Lihat Hasil</a>
                                    @elseif ($session->status === 'in_progress')
                                        @php
                                            $nextRoute = $session->listening_submitted_at
                                                ? ($session->structure_submitted_at
                                                    ? route('toefl.session.reading', $session)
                                                    : route('toefl.session.structure', $session))
                                                : route('toefl.session.listening', $session);
                                        @endphp
                                        <a href="{{ $nextRoute }}" class="text-amber-600 hover:text-amber-800 text-xs font-medium">Lanjutkan</a>
                                    @else
                                        <span class="text-gray-300 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-3 border-t border-[color:var(--dash-card-border)]">
                {{ $sessions->links() }}
            </div>
        @endif
    </section>

</div>
@endsection
