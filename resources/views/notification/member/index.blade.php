@extends('layouts.dashboard')

@section('header_title', 'Notifikasi Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Personal</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Notifikasi Saya</h1>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-bold uppercase tracking-wider text-[var(--dash-text-muted)]">
                    <tr>
                        <th class="px-6 py-4">Notifikasi</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)]">
                    @forelse($notifications as $notif)
                    <tr class="hover:bg-[var(--dash-page-bg)] transition-colors {{ $notif->status === 'unread' ? 'bg-blue-50/30 [.mode-dark_&]:bg-blue-500/10' : '' }}">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-[var(--dash-text-heading)]">{{ $notif->subject }}</p>
                            <p class="mt-1 text-[var(--dash-text-muted)] text-xs">{{ Str::limit($notif->message, 120) }}</p>
                        </td>
                        <td class="px-6 py-4 text-[var(--dash-text-muted)] whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-4">
                            @if($notif->status === 'unread')
                            <span class="inline-flex items-center rounded-full bg-blue-100 [.mode-dark_&]:bg-blue-500/10 px-2.5 py-0.5 text-xs font-medium text-blue-800 [.mode-dark_&]:text-blue-400">Belum Dibaca</span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-[var(--dash-input-bg)] px-2.5 py-0.5 text-xs font-medium text-[var(--dash-text-muted)]">Dibaca</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-[var(--dash-text-faint)]">Belum ada notifikasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notifications->hasPages())
        <div class="border-t border-[var(--dash-card-border)] px-6 py-4">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
