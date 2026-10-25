@extends('layouts.dashboard')

@section('title', 'Session Member TOEFL')
@section('header_title', 'Session Member TOEFL')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">TOEFL</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Session Member TOEFL</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Riwayat sesi ujian TOEFL semua member.</p>
    </div>

    <form method="GET" action="{{ route('toefl.member-session.index') }}"
          class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Status</label>
            <select name="status"
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'"
                    onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
        </div>
        @if (request('status'))
            <a href="{{ route('toefl.member-session.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">restart_alt</span>Reset
            </a>
        @endif
    </form>

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">No</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Nama Member</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Kontak / Akun</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Nama Test</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Status</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Skor Akhir</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Tanggal Tes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sessions as $session)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5" style="color: var(--dash-text-muted);">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                            {{ optional($session->member)->full_name ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-muted);">
                            {{ optional($session->member)->email ?? '-' }}<br>
                            <span class="text-xs">{{ optional($session->member)->whatsapp_number ?? '-' }}</span>
                        </td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-body);">
                            {{ optional($session->test)->test_name ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $statusColors = [
                                    'in_progress' => 'bg-amber-100 text-amber-700',
                                    'completed'   => 'bg-emerald-100 text-emerald-700',
                                    'expired'     => 'bg-red-100 text-red-700',
                                ];
                                $color = $statusColors[$session->status] ?? 'bg-slate-100 text-slate-600';
                            @endphp
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $color }}">
                                {{ \App\Support\EnumLabels::get('toefl_session_status', $session->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-bold tabular-nums" style="color: var(--dash-text-heading);">
                            {{ $session->score_total ?? '-' }}
                        </td>
                        <td class="px-5 py-3.5" style="color: var(--dash-text-muted);">
                            @if ($session->started_at)
                                Mulai: {{ $session->started_at->format('d M Y H:i') }}<br>
                            @endif
                            @if ($session->completed_at)
                                <span class="text-xs">Selesai: {{ $session->completed_at->format('d M Y H:i') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">manage_accounts</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data session member.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($sessions->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
