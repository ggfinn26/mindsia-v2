@extends('layouts.dashboard')

@section('header_title', 'Log Notifikasi')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-[var(--dash-text-muted)]">Notifikasi</p>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight text-[var(--dash-text-heading)]">Log & Monitor Notifikasi</h1>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 border border-emerald-200 [.mode-dark_&]:border-emerald-500/20 px-4 py-3 text-sm text-emerald-700 [.mode-dark_&]:text-emerald-400">{{ session('success') }}</div>
    @endif

    <!-- Filter -->
    <div class="overflow-hidden rounded-xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm px-6 py-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-[var(--dash-text-muted)] mb-1">Status</label>
                <select name="status" class="rounded-lg border border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)]">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-[var(--dash-text-muted)] mb-1">Tipe</label>
                <select name="type" class="rounded-lg border border-[var(--dash-input-border)] px-3 py-2 text-sm focus:border-[var(--dash-primary)] focus:ring-1 focus:ring-[var(--dash-primary)]">
                    <option value="">Semua Tipe</option>
                    <option value="in-app" {{ request('type') === 'in-app' ? 'selected' : '' }}>In-App</option>
                    <option value="email" {{ request('type') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="telegram" {{ request('type') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-[var(--dash-primary)] px-4 py-2 text-sm font-bold text-white hover:opacity-90 transition-colors">Filter</button>
            @if(request()->hasAny(['status', 'type', 'employee_id', 'member_id']))
            <a href="{{ route('notification-logs.index') }}" class="rounded-lg border border-[var(--dash-input-border)] px-4 py-2 text-sm font-medium text-[var(--dash-text-muted)] hover:bg-[var(--dash-page-bg)] transition-colors">Reset</a>
            @endif
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-page-bg)] text-left text-xs font-bold uppercase tracking-wider text-[var(--dash-text-muted)]">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Penerima</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Template</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Percobaan</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--dash-card-border)]">
                    @forelse($logs as $log)
                    <tr class="hover:bg-[var(--dash-page-bg)] transition-colors">
                        <td class="px-6 py-4 text-[var(--dash-text-muted)] whitespace-nowrap text-xs">
                            {{ $log->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($log->employee)
                            <span class="text-[var(--dash-text-body)]">{{ $log->employee->full_name }}</span>
                            <span class="text-xs text-[var(--dash-text-faint)] block">Pegawai</span>
                            @elseif($log->member)
                            <span class="text-[var(--dash-text-body)]">{{ $log->member->nama_lengkap }}</span>
                            <span class="text-xs text-[var(--dash-text-faint)] block">Member</span>
                            @else
                            <span class="text-[var(--dash-text-faint)]">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php $typeColors = ['email' => 'bg-blue-100 [.mode-dark_&]:bg-blue-500/10 text-blue-800 [.mode-dark_&]:text-blue-400', 'telegram' => 'bg-sky-100 [.mode-dark_&]:bg-sky-500/10 text-sky-800 [.mode-dark_&]:text-sky-400', 'in-app' => 'bg-violet-100 [.mode-dark_&]:bg-violet-500/10 text-violet-800 [.mode-dark_&]:text-violet-400']; @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColors[$log->notification_type] ?? 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]' }}">{{ $log->notification_type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->template)
                            <code class="rounded bg-[var(--dash-input-bg)] px-2 py-0.5 text-xs font-mono text-[var(--dash-text-muted)]">{{ $log->template->template_key }}</code>
                            @else
                            <span class="text-[var(--dash-text-faint)] text-xs">Manual</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php $statusColors = ['sent' => 'bg-emerald-100 [.mode-dark_&]:bg-emerald-500/10 text-emerald-800 [.mode-dark_&]:text-emerald-400', 'failed' => 'bg-red-100 [.mode-dark_&]:bg-red-500/10 text-red-800 [.mode-dark_&]:text-red-400', 'pending' => 'bg-yellow-100 [.mode-dark_&]:bg-yellow-500/10 text-yellow-800 [.mode-dark_&]:text-yellow-400']; @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$log->notification_status] ?? 'bg-[var(--dash-input-bg)] text-[var(--dash-text-body)]' }}">{{ $log->notification_status }}</span>
                            @if($log->notification_error)
                            <p class="mt-0.5 text-xs text-red-500 [.mode-dark_&]:text-red-400 truncate max-w-[200px]" title="{{ $log->notification_error }}">{{ Str::limit($log->notification_error, 60) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-[var(--dash-text-muted)]">{{ $log->attempts }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($log->notification_status === 'failed')
                            @can('notification.log.resend')
                            <form method="POST" action="{{ route('notification-logs.resend', $log->id) }}">
                                @csrf
                                <button type="submit" class="rounded-lg border border-[var(--dash-input-border)] px-3 py-1.5 text-xs font-medium text-[var(--dash-text-muted)] hover:bg-[var(--dash-page-bg)] transition-colors">
                                    Kirim Ulang
                                </button>
                            </form>
                            @endcan
                            @else
                            <span class="text-xs text-[var(--dash-text-faint)]">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-[var(--dash-text-faint)]">Belum ada log notifikasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="border-t border-[var(--dash-card-border)] px-6 py-4">
            {{ $logs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
