@extends('layouts.dashboard')

@section('title', 'Notifikasi Saya - MINDSIA')
@section('header_title', 'Notifikasi Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6">
    {{-- Page Header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Personal Dashboard</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Notifikasi Saya</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Daftar seluruh notifikasi dan pembaruan untuk akun Anda.
        </p>
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

    {{-- Table Card --}}
    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Notifikasi</th>
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider whitespace-nowrap" style="color: var(--dash-text-faint);">Waktu</th>
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-right" style="color: var(--dash-text-faint);">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($notifications as $notif)
                        <tr class="transition-colors"
                            style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                         style="background-color: var(--dash-nav-hover-bg); color: var(--dash-primary-text);">
                                        <span class="material-symbols-outlined text-[18px]">notifications</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                                            {{ $notif->subject }}
                                        </p>
                                        <p class="mt-1 text-xs leading-relaxed" style="color: var(--dash-text-muted);">
                                            {{ $notif->message }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs whitespace-nowrap" style="color: var(--dash-text-faint);">
                                {{ $notif->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if($notif->status === 'unread')
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md text-white"
                                          style="background-color: var(--dash-primary);">
                                        Belum Dibaca
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md"
                                          style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                                        Dibaca
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <span class="material-symbols-outlined text-4xl block mb-2" style="color: var(--dash-text-faint);">notifications_none</span>
                                <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">Belum ada notifikasi</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Notifikasi baru akan muncul di sini jika ada aktivitas akun atau pengumuman.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notifications->hasPages())
            <div class="px-6 py-4" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
