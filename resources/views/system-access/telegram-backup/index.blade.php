@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Backup Telegram</h1>
            <p class="text-sm text-[color:var(--dash-text-muted)] mt-1">Monitoring file yang di-backup ke Telegram sebagai penyimpanan cadangan.</p>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            @can('system.telegram_backup.retry')
                @if($failedCount > 0)
                    <form method="POST" action="{{ route('system.telegram-backup.retry-all') }}">
                        @csrf
                        <button type="submit" class="btn bg-amber-500 hover:bg-amber-600 text-white">
                            Retry Semua Gagal ({{ $failedCount }})
                        </button>
                    </form>
                @endif
            @endcan
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-[var(--dash-card-bg)] shadow-sm rounded-sm border border-[color:var(--dash-card-border)] p-4">
            <div class="text-sm font-medium text-[color:var(--dash-text-muted)]">Gagal</div>
            <div class="text-2xl font-bold text-red-600">{{ $failedCount }}</div>
        </div>
        <div class="bg-[var(--dash-card-bg)] shadow-sm rounded-sm border border-[color:var(--dash-card-border)] p-4">
            <div class="text-sm font-medium text-[color:var(--dash-text-muted)]">Menunggu</div>
            <div class="text-2xl font-bold text-amber-500">{{ $pendingCount }}</div>
        </div>
        <div class="bg-[var(--dash-card-bg)] shadow-sm rounded-sm border border-[color:var(--dash-card-border)] p-4">
            <div class="text-sm font-medium text-[color:var(--dash-text-muted)]">Total Record</div>
            <div class="text-2xl font-bold text-[color:var(--dash-text-heading)]">{{ $files->total() }}</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)]">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-[color:var(--dash-text-muted)] bg-[var(--dash-nav-hover-bg)] border-t border-b border-[color:var(--dash-card-border)]">
                    <tr>
                        <th class="px-4 py-3 text-left">File</th>
                        <th class="px-4 py-3 text-left">Tipe</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Retry</th>
                        <th class="px-4 py-3 text-left">Error Terakhir</th>
                        <th class="px-4 py-3 text-left">Terakhir Dicoba</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-[color:var(--dash-card-border)]">
                    @forelse($files as $file)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-medium text-[color:var(--dash-text-heading)]">{{ $file->original_filename ?? '-' }}</div>
                                <div class="text-xs text-[color:var(--dash-text-faint)]">{{ Str::limit($file->storage_path ?? $file->file_path ?? '-', 50) }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-[color:var(--dash-text-body)]">{{ $file->entity_type ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'uploaded' => 'bg-green-100 text-green-700',
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'failed' => 'bg-red-100 text-red-700',
                                    ];
                                    $color = $statusColors[$file->backup_status] ?? 'bg-[var(--dash-nav-hover-bg)] text-[color:var(--dash-text-body)]';
                                @endphp
                                <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $color }}">
                                    {{ ucfirst($file->backup_status ?? 'unknown') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-[color:var(--dash-text-body)]">{{ $file->retry_count }}</td>
                            <td class="px-4 py-3 text-[color:var(--dash-text-muted)] max-w-xs truncate" title="{{ $file->last_error }}">
                                {{ Str::limit($file->last_error, 40) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-[color:var(--dash-text-muted)]">
                                {{ $file->last_attempted_at?->format('d M H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right space-x-2">
                                @can('system.telegram_backup.retry')
                                    @if(!$file->isUploaded())
                                        <form method="POST" action="{{ route('system.telegram-backup.retry', $file) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-amber-500 hover:text-amber-700 text-xs font-medium">Retry</button>
                                        </form>
                                    @endif
                                @endcan
                                @can('system.telegram_backup.delete')
                                    <form method="POST" action="{{ route('system.telegram-backup.destroy', $file) }}" class="inline"
                                          onsubmit="return confirm('Hapus record backup ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-[color:var(--dash-text-muted)]">Belum ada record backup.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-[color:var(--dash-card-border)]">
            {{ $files->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
