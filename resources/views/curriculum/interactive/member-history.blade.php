@extends('layouts.dashboard')

@section('title', 'Riwayat Latihan - ' . $item->item_name)
@section('header_title', 'Riwayat Latihan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('member.curriculum.index') }}"
               class="dash-back flex items-center justify-center w-9 h-9 rounded-lg transition-colors cursor-pointer shrink-0"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
               title="Kembali ke Kurikulum">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5"
                   style="color: var(--dash-primary-text);">Riwayat Pengerjaan</p>
                <h1 class="font-jakarta text-xl sm:text-2xl font-bold tracking-tight"
                    style="color: var(--dash-text-heading);">Riwayat Latihan - {{ $item->item_name }}</h1>
            </div>
        </div>
        <a href="{{ route('member.curriculum.interactive.show', [$classroom, $item]) }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 font-semibold rounded-lg text-xs text-white transition-opacity hover:opacity-90 shadow-sm cursor-pointer self-start sm:self-auto"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[16px]">play_circle</span>
            Latihan Lagi
        </a>
    </div>

    @if($attempts->isEmpty())
        <div class="rounded-2xl p-12 text-center shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
                  style="color: var(--dash-text-faint);">history</span>
            <h3 class="text-base font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Belum Ada Riwayat</h3>
            <p class="text-sm" style="color: var(--dash-text-muted);">Anda belum pernah menyelesaikan latihan pada modul ini.</p>
        </div>
    @else
        <div class="rounded-2xl overflow-hidden shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">#</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Durasi</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Skor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                        @foreach($attempts as $i => $attempt)
                            <tr class="transition-colors"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-muted);">{{ $i + 1 }}</td>
                                <td class="px-5 py-3.5 font-semibold" style="color: var(--dash-text-heading);">{{ $attempt->completed_at->format('d M Y, H:i') }} WIB</td>
                                <td class="px-5 py-3.5" style="color: var(--dash-text-muted);">
                                    {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }} menit
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                        style="{{ $attempt->score >= 70 ? 'background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);' : ($attempt->score >= 50 ? 'background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);' : 'background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text);') }}">
                                        {{ $attempt->score }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
