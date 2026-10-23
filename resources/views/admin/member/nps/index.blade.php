@extends('layouts.dashboard')

@section('title', 'NPS & Kepuasan Member - MINDSIA')
@section('header_title', 'NPS & Kepuasan Member')

@section('content')
@php
    $totalCount = $responses->total();
    $avgScore = $responses->count() > 0 ? round($responses->avg('score'), 1) : 0;
    
    // NPS Calculation from current collection
    $promoters = $responses->where('score', '>=', 9)->count();
    $detractors = $responses->where('score', '<=', 6)->count();
    $currentCount = $responses->count();
    $npsScore = $currentCount > 0 ? round((($promoters - $detractors) / $currentCount) * 100) : 0;
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">NPS & Kepuasan Member</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Pantau Net Promoter Score, rata-rata penilaian, dan umpan balik dari seluruh siswa bimbingan.</p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        {{-- Card 1: Rata-rata Skor --}}
        <div class="rounded-2xl p-5 shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Rata-rata Skor</p>
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">star</span>
            </div>
            <div class="flex items-baseline gap-2 mt-3">
                <span class="font-jakarta text-3xl font-bold font-mono" style="color: var(--dash-text-heading);">{{ $avgScore }}</span>
                <span class="text-xs font-semibold" style="color: var(--dash-text-muted);">/ 10</span>
            </div>
            <p class="text-[11px] mt-1" style="color: var(--dash-text-faint);">Skala penilaian kepuasan 1 hingga 10</p>
        </div>

        {{-- Card 2: Net Promoter Score --}}
        <div class="rounded-2xl p-5 shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Net Promoter Score</p>
                <span class="material-symbols-outlined text-[20px]" style="color: {{ $npsScore >= 0 ? '#059669' : '#dc2626' }};">trending_up</span>
            </div>
            <div class="flex items-baseline gap-2 mt-3">
                <span class="font-jakarta text-3xl font-bold font-mono"
                      style="color: {{ $npsScore >= 50 ? '#059669' : ($npsScore >= 0 ? 'var(--dash-text-heading)' : '#dc2626') }};">
                    {{ $npsScore > 0 ? '+' : '' }}{{ $npsScore }}
                </span>
                <span class="text-xs font-semibold" style="color: var(--dash-text-muted);">Poin NPS</span>
            </div>
            <p class="text-[11px] mt-1" style="color: var(--dash-text-faint);">% Promotor dikurangi % Detraktor</p>
        </div>

        {{-- Card 3: Total Responden --}}
        <div class="rounded-2xl p-5 shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Total Respon</p>
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-text-muted);">group</span>
            </div>
            <div class="flex items-baseline gap-2 mt-3">
                <span class="font-jakarta text-3xl font-bold font-mono" style="color: var(--dash-text-heading);">{{ $totalCount }}</span>
                <span class="text-xs font-semibold" style="color: var(--dash-text-muted);">Member</span>
            </div>
            <p class="text-[11px] mt-1" style="color: var(--dash-text-faint);">Penilaian yang masuk ke sistem</p>
        </div>
    </div>

    {{-- Data Table (No horizontal scroll, fully fitted) --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full text-sm text-left">
            <thead>
                <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Member & Cabang</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Skor NPS</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Ulasan & Komentar</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right" style="color: var(--dash-text-muted);">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($responses as $response)
                    @php
                        $score = $response->score;
                        $cat = match(true) {
                            $score >= 9 => ['label' => 'Promotor', 'class' => 'bg-emerald-100 text-emerald-700'],
                            $score >= 7 => ['label' => 'Pasif', 'class' => 'bg-amber-100 text-amber-700'],
                            default     => ['label' => 'Detraktor', 'class' => 'bg-red-100 text-red-700'],
                        };
                    @endphp
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                                {{ $response->member?->full_name ?? 'Member Anonymous' }}
                            </p>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                {{ $response->branch?->branch_name ?? 'Semua Cabang' }}
                            </p>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-base" style="color: var(--dash-text-heading);">{{ $score }}/10</span>
                                <span class="inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $cat['class'] }}">
                                    {{ $cat['label'] }}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($response->comment)
                                <p class="text-xs leading-relaxed max-w-lg break-words" style="color: var(--dash-text-body);">
                                    "{{ $response->comment }}"
                                </p>
                            @else
                                <span class="text-xs italic" style="color: var(--dash-text-faint);">Tidak ada ulasan tertulis.</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-xs text-right whitespace-nowrap" style="color: var(--dash-text-muted);">
                            {{ $response->created_at?->format('d M Y, H:i') ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">reviews</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada respon review NPS dari member.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($responses->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $responses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
