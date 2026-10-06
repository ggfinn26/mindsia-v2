@extends('layouts.dashboard')

@section('header_title', 'Log Notifikasi')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Notifikasi</p>
        <h1 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Log & Monitor Notifikasi</h1>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <!-- Filter -->
    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm px-6 py-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                <select name="status" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Tipe</label>
                <select name="type" class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                    <option value="">Semua Tipe</option>
                    <option value="in-app" {{ request('type') === 'in-app' ? 'selected' : '' }}>In-App</option>
                    <option value="email" {{ request('type') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="telegram" {{ request('type') === 'telegram' ? 'selected' : '' }}>Telegram</option>
                </select>
            </div>
            <button type="submit" class="rounded-lg bg-[#215aac] px-4 py-2 text-sm font-bold text-white hover:bg-[#194a91] transition-colors">Filter</button>
            @if(request()->hasAny(['status', 'type', 'employee_id', 'member_id']))
            <a href="{{ route('notification-logs.index') }}" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">Reset</a>
            @endif
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
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
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-slate-500 whitespace-nowrap text-xs">
                            {{ $log->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($log->employee)
                            <span class="text-slate-700">{{ $log->employee->full_name }}</span>
                            <span class="text-xs text-slate-400 block">Pegawai</span>
                            @elseif($log->member)
                            <span class="text-slate-700">{{ $log->member->nama_lengkap }}</span>
                            <span class="text-xs text-slate-400 block">Member</span>
                            @else
                            <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php $typeColors = ['email' => 'bg-blue-100 text-blue-800', 'telegram' => 'bg-sky-100 text-sky-800', 'in-app' => 'bg-violet-100 text-violet-800']; @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColors[$log->notification_type] ?? 'bg-slate-100 text-slate-700' }}">{{ $log->notification_type }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($log->template)
                            <code class="rounded bg-slate-100 px-2 py-0.5 text-xs font-mono text-slate-600">{{ $log->template->template_key }}</code>
                            @else
                            <span class="text-slate-400 text-xs">Manual</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php $statusColors = ['sent' => 'bg-green-100 text-green-800', 'failed' => 'bg-red-100 text-red-800', 'pending' => 'bg-yellow-100 text-yellow-800']; @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColors[$log->notification_status] ?? 'bg-slate-100 text-slate-700' }}">{{ $log->notification_status }}</span>
                            @if($log->notification_error)
                            <p class="mt-0.5 text-xs text-red-500 truncate max-w-[200px]" title="{{ $log->notification_error }}">{{ Str::limit($log->notification_error, 60) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-slate-500">{{ $log->attempts }}</td>
                        <td class="px-6 py-4 text-right">
                            @if($log->notification_status === 'failed')
                            @can('notification.log.resend')
                            <form method="POST" action="{{ route('notification-logs.resend', $log->id) }}">
                                @csrf
                                <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                                    Kirim Ulang
                                </button>
                            </form>
                            @endcan
                            @else
                            <span class="text-xs text-slate-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-slate-400">Belum ada log notifikasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="border-t border-slate-100 px-6 py-4">
            {{ $logs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
