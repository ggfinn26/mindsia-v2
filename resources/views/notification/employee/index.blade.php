@extends('layouts.dashboard')

@section('header_title', 'Notifikasi Saya')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Personal</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Notifikasi Saya</h1>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Notifikasi</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($notifications as $notif)
                    <tr class="hover:bg-slate-50/50 transition-colors {{ $notif->status === 'unread' ? 'bg-blue-50/30' : '' }}">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-800">{{ $notif->subject }}</p>
                            <p class="mt-1 text-slate-500 text-xs">{{ Str::limit($notif->message, 120) }}</p>
                        </td>
                        <td class="px-6 py-4 text-slate-500 whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</td>
                        <td class="px-6 py-4">
                            @if($notif->status === 'unread')
                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">Belum Dibaca</span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Dibaca</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-10 text-center text-slate-400">Belum ada notifikasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notifications->hasPages())
        <div class="border-t border-slate-100 px-6 py-4">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
