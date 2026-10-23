@extends('layouts.dashboard')

@section('title', 'Log & Monitor Notifikasi - MINDSIA')
@section('header_title', 'Log Notifikasi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Notifikasi</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Log & Monitor Notifikasi</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Pantau riwayat pengiriman notifikasi, status pengantaran, dan kirim ulang antrean yang gagal.</p>
        </div>
        @can('notification.send_manual')
        <div class="flex items-center gap-3">
            <a href="{{ route('notification-manual-send.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">send</span>
                Kirim Manual
            </a>
        </div>
        @endcan
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

    {{-- Filter Card --}}
    <form method="GET" action="{{ route('notification-logs.index') }}"
          class="flex flex-wrap items-end gap-3 p-4 sm:p-5 rounded-2xl shadow-sm"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div>
            <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Status</label>
            <select name="status"
                    class="h-[38px] px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>

        <div>
            <label class="block text-[11px] font-semibold uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Tipe</label>
            <select name="type"
                    class="h-[38px] px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Tipe</option>
                <option value="in-app" {{ request('type') === 'in-app' ? 'selected' : '' }}>In-App</option>
                <option value="email" {{ request('type') === 'email' ? 'selected' : '' }}>Email</option>
                <option value="telegram" {{ request('type') === 'telegram' ? 'selected' : '' }}>Telegram</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 h-[38px] px-4 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 cursor-pointer"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                Filter
            </button>

            @if(request()->hasAny(['status', 'type', 'employee_id', 'member_id']))
                <a href="{{ route('notification-logs.index') }}"
                   class="inline-flex items-center gap-1.5 h-[38px] px-3.5 rounded-lg text-sm font-semibold transition-colors"
                   style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
                   onmouseover="this.style.color='var(--dash-text-heading)'"
                   onmouseout="this.style.color='var(--dash-text-muted)'">
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    Reset
                </a>
            @endif
        </div>
    </form>

    {{-- Table Card --}}
    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider whitespace-nowrap" style="color: var(--dash-text-faint);">Waktu</th>
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Penerima</th>
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Tipe</th>
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Template</th>
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Status</th>
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-center" style="color: var(--dash-text-faint);">Percobaan</th>
                        <th class="px-6 py-3.5 text-xs font-semibold uppercase tracking-wider text-right" style="color: var(--dash-text-faint);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($logs as $log)
                        <tr class="transition-colors"
                            style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-6 py-4 whitespace-nowrap text-xs" style="color: var(--dash-text-muted);">
                                {{ $log->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($log->employee)
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-medium text-sm" style="color: var(--dash-text-heading);">{{ $log->employee->full_name }}</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold rounded"
                                              style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-primary-text);">
                                            Pegawai
                                        </span>
                                    </div>
                                    @if($log->channel)
                                        <span class="text-xs block mt-0.5" style="color: var(--dash-text-faint);">{{ $log->channel }}</span>
                                    @endif
                                @elseif($log->member)
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-medium text-sm" style="color: var(--dash-text-heading);">{{ $log->member->nama_lengkap }}</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold rounded"
                                              style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                                            Member
                                        </span>
                                    </div>
                                    @if($log->channel)
                                        <span class="text-xs block mt-0.5" style="color: var(--dash-text-faint);">{{ $log->channel }}</span>
                                    @endif
                                @else
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-medium text-sm" style="color: var(--dash-text-heading);">{{ $log->channel ?: '-' }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($log->notification_type === 'in-app')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md"
                                          style="background-color: #f3e8ff; color: #7e22ce;">
                                        <span class="material-symbols-outlined text-[14px]">notifications</span>
                                        In-App
                                    </span>
                                @elseif($log->notification_type === 'email')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md"
                                          style="background-color: #e0f7fb; color: #007a99;">
                                        <span class="material-symbols-outlined text-[14px]">mail</span>
                                        Email
                                    </span>
                                @elseif($log->notification_type === 'telegram')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md"
                                          style="background-color: #e0f2fe; color: #0284c7;">
                                        <span class="material-symbols-outlined text-[14px]">send</span>
                                        Telegram
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md"
                                          style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                                        {{ ucfirst($log->notification_type) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($log->template)
                                    <code class="text-xs font-mono px-2 py-0.5 rounded"
                                          style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                                        {{ $log->template->template_key }}
                                    </code>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-md"
                                          style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                                        Manual
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($log->notification_status === 'sent')
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md"
                                          style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
                                        Sent
                                    </span>
                                @elseif($log->notification_status === 'failed')
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md"
                                          style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
                                        Failed
                                    </span>
                                @elseif($log->notification_status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md"
                                          style="background-color: #fef3c7; border: 1px solid #fde68a; color: #d97706;">
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md"
                                          style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                                        {{ ucfirst($log->notification_status) }}
                                    </span>
                                @endif

                                @if($log->notification_error)
                                    <p class="mt-1 text-xs truncate max-w-[220px]"
                                       style="color: #dc2626;"
                                       title="{{ $log->notification_error }}">
                                        {{ Str::limit($log->notification_error, 60) }}
                                    </p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center font-medium" style="color: var(--dash-text-body);">
                                {{ $log->attempts }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @if($log->notification_status === 'failed')
                                    @can('notification.log.resend')
                                        <form method="POST" action="{{ route('notification-logs.resend', $log->id) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                                                    style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);"
                                                    onmouseover="this.style.backgroundColor='var(--dash-input-border)'"
                                                    onmouseout="this.style.backgroundColor='var(--dash-nav-hover-bg)'">
                                                <span class="material-symbols-outlined text-[15px]">replay</span>
                                                Kirim Ulang
                                            </button>
                                        </form>
                                    @endcan
                                @else
                                    <span class="text-xs" style="color: var(--dash-text-faint);">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mb-2" style="color: var(--dash-text-faint);">notifications_off</span>
                                <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">Belum ada log notifikasi</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Riwayat pengiriman notifikasi sistem akan dicatat dan ditampilkan di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
