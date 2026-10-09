@extends('layouts.dashboard')

@section('title', 'Riwayat Latihan')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('member.curriculum.index') }}" class="text-sm text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">← Kurikulum</a>
            <h1 class="text-xl font-bold text-[color:var(--dash-text-heading)] font-jakarta mt-1">Riwayat Latihan — {{ $item->item_name }}</h1>
        </div>
        <a href="{{ route('member.curriculum.interactive.show', [$classroom, $item]) }}"
            class="px-4 py-2 font-medium rounded-lg transition-colors bg-[#5586DB] hover:bg-[#4370BC] text-white text-sm">Latihan Lagi</a>
    </div>

    @if($attempts->isEmpty())
        <div class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl p-8 text-center text-[color:var(--dash-text-faint)]">
            Belum ada riwayat latihan.
        </div>
    @else
        <div class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-[var(--dash-nav-hover-bg)] border-b border-[color:var(--dash-card-border)]">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase">#</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase">Tanggal</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase">Durasi</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-[color:var(--dash-text-muted)] uppercase">Skor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[color:var(--dash-card-border)]">
                    @foreach($attempts as $i => $attempt)
                        <tr class="hover:bg-[var(--dash-nav-hover-bg)]">
                            <td class="px-4 py-3 text-[color:var(--dash-text-muted)]">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 text-[color:var(--dash-text-body)]">{{ $attempt->completed_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 text-[color:var(--dash-text-muted)]">
                                {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }} menit
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $attempt->score >= 70 ? 'bg-green-100 text-green-700' : ($attempt->score >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                                    {{ $attempt->score }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
