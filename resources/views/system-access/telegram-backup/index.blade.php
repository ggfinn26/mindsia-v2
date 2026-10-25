@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Backup Telegram</h1>
            <p class="text-sm text-gray-500 mt-1">Monitoring file yang di-backup ke Telegram sebagai penyimpanan cadangan.</p>
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
        <div class="bg-white shadow-sm rounded-sm border border-gray-200 p-4">
            <div class="text-sm font-medium text-gray-500">Gagal</div>
            <div class="text-2xl font-bold text-red-600">{{ $failedCount }}</div>
        </div>
        <div class="bg-white shadow-sm rounded-sm border border-gray-200 p-4">
            <div class="text-sm font-medium text-gray-500">Menunggu</div>
            <div class="text-2xl font-bold text-amber-500">{{ $pendingCount }}</div>
        </div>
        <div class="bg-white shadow-sm rounded-sm border border-gray-200 p-4">
            <div class="text-sm font-medium text-gray-500">Total Record</div>
            <div class="text-2xl font-bold text-gray-800">{{ $files->total() }}</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
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
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($files as $file)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-medium text-gray-800">{{ $file->original_filename ?? '-' }}</div>
                                <div class="text-xs text-gray-400">{{ Str::limit($file->storage_path ?? $file->file_path ?? '-', 50) }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ $file->entity_type ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'uploaded' => 'bg-green-100 text-green-700',
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'failed' => 'bg-red-100 text-red-700',
                                    ];
                                    $color = $statusColors[$file->backup_status] ?? 'bg-gray-100 text-gray-700';
                                @endphp
                                <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $color }}">
                                    {{ \App\Support\EnumLabels::get('backup_status', $file->backup_status ?? 'unknown') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ $file->retry_count }}</td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate" title="{{ $file->last_error }}">
                                {{ Str::limit($file->last_error, 40) }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-500">
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
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada record backup.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $files->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
