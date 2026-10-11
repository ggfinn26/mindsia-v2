@extends('layouts.dashboard')

@section('title', 'Ujian TOEFL')
@section('header_title', 'Ujian TOEFL')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">TOEFL</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Ujian TOEFL</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Pilih test yang tersedia, atau lihat riwayat hasil ujian kamu.</p>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Test Tersedia --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Test Tersedia</h2>
            <span class="text-xs" style="color: var(--dash-text-faint);">{{ $availableTests->count() }} test</span>
        </div>
        <div>
            @forelse ($availableTests as $test)
                <div x-data="{ showForm: false }"
                     style="border-bottom: 1px solid var(--dash-card-border);">
                    <div class="px-6 py-4 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-medium truncate" style="color: var(--dash-text-heading);">{{ $test->test_name }}</p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-faint);">
                                Listening {{ $test->listening_time_limit }}m
                                <span class="mx-1" style="color: var(--dash-card-border);">|</span>
                                Structure {{ $test->structure_time_limit }}m
                                <span class="mx-1" style="color: var(--dash-card-border);">|</span>
                                Reading {{ $test->reading_time_limit }}m
                            </p>
                        </div>
                        <div class="shrink-0">
                            <button type="button" @click="showForm = !showForm" :aria-expanded="showForm"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[16px]">play_arrow</span>Mulai Ujian
                            </button>
                        </div>
                    </div>

                    {{-- Inline start form --}}
                    <div x-show="showForm"
                         x-transition:enter="transition duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         style="display:none;"
                         class="px-6 pb-5 pt-3"
                         :style="'border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);'">
                        <form action="{{ route('toefl.session.start', $test) }}" method="POST"
                              class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
                            @csrf
                            <div class="flex-1 w-full sm:w-auto">
                                <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-muted);">
                                    Password Test
                                    @if (!$test->password)
                                        <span class="font-normal" style="color: var(--dash-text-faint);">(tidak diperlukan)</span>
                                    @endif
                                </label>
                                <div class="relative" x-data="{ showPw: false }">
                                    <input name="password" :type="showPw ? 'text' : 'password'"
                                           class="w-full px-4 py-2.5 pr-10 text-sm rounded-lg focus:outline-none transition-all"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                                           placeholder="{{ $test->password ? 'Masukkan password test' : 'Biarkan kosong jika tidak ada password' }}" />
                                    <button type="button" @click="showPw = !showPw"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 transition-colors"
                                            style="color: var(--dash-text-faint);">
                                        <svg x-show="!showPw" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <svg x-show="showPw" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">check</span>Konfirmasi &amp; Mulai
                                </button>
                                <button type="button" @click="showForm = false"
                                        class="inline-flex items-center px-4 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                          style="color: var(--dash-text-faint);">quiz</span>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada test yang tersedia</p>
                    <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Hubungi pengajar untuk mengaktifkan test TOEFL kamu.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Riwayat Ujian --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Riwayat Ujian</h2>
        </div>

        @if ($sessions->isEmpty())
            <div class="px-6 py-12 text-center">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">history</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada riwayat ujian</p>
                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Hasil ujian yang sudah diselesaikan akan tampil di sini.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Test</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">L</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">S</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">R</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Total</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Tanggal</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sessions as $session)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5 max-w-[160px] truncate font-medium"
                                style="color: var(--dash-text-heading);">{{ optional($session->test)->test_name ?? 'Test dihapus' }}</td>
                            <td class="px-5 py-3.5">
                                @php
                                    $badge = match($session->status) {
                                        'completed'   => 'bg-emerald-100 text-emerald-700',
                                        'expired'     => 'bg-red-100 text-red-700',
                                        default       => 'bg-amber-100 text-amber-700',
                                    };
                                    $label = match($session->status) {
                                        'completed'   => 'Selesai',
                                        'expired'     => 'Kedaluwarsa',
                                        'in_progress' => 'Berlangsung',
                                        default       => $session->status,
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $badge }}">{{ $label }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono" style="color: var(--dash-text-body);">{{ $session->score_listening ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-right font-mono" style="color: var(--dash-text-body);">{{ $session->score_structure ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-right font-mono" style="color: var(--dash-text-body);">{{ $session->score_reading ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold" style="color: var(--dash-primary);">{{ $session->score_total ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-right tabular-nums" style="color: var(--dash-text-faint);">{{ $session->created_at->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-right">
                                @if ($session->status === 'completed')
                                    <a href="{{ route('toefl.session.result', $session) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[13px]">visibility</span>Hasil
                                    </a>
                                @elseif ($session->status === 'in_progress')
                                    @php
                                        $nextRoute = $session->listening_submitted_at
                                            ? ($session->structure_submitted_at
                                                ? route('toefl.session.reading', $session)
                                                : route('toefl.session.structure', $session))
                                            : route('toefl.session.listening', $session);
                                    @endphp
                                    <a href="{{ $nextRoute }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                       style="background-color: #d97706;">
                                        <span class="material-symbols-outlined text-[13px]">play_arrow</span>Lanjutkan
                                    </a>
                                @else
                                    <span class="text-xs" style="color: var(--dash-text-faint);">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($sessions->hasPages())
                <div class="px-5 py-3" style="border-top: 1px solid var(--dash-card-border);">
                    {{ $sessions->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
