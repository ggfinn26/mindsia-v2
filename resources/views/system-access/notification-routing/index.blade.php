@extends('layouts.dashboard')

@section('header_title', 'Routing Notifikasi')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Routing Notifikasi</h1>
            <p class="mt-1 text-sm text-slate-500">Konfigurasi event → posisi penerima → channel pengiriman.</p>
        </div>
        <a href="{{ route('system.notification-routings.create') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[#215aac] px-5 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Aturan
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Event Key</th>
                        <th class="px-6 py-4">Target Posisi</th>
                        <th class="px-6 py-4">Channel</th>
                        <th class="px-6 py-4">Scope</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($routings as $routing)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <code class="rounded bg-slate-100 px-2 py-0.5 text-xs font-mono text-slate-700">{{ $routing->event_key }}</code>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $routing->position?->name ?? 'Semua Posisi' }}</td>
                        <td class="px-6 py-4">
                            @php $chColors = ['email' => 'bg-blue-100 text-blue-800', 'telegram' => 'bg-sky-100 text-sky-800', 'in-app' => 'bg-violet-100 text-violet-800']; @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $chColors[$routing->channel] ?? 'bg-slate-100 text-slate-700' }}">{{ $routing->channel }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $routing->scope }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-500 max-w-[200px] truncate" title="{{ $routing->description }}">{{ $routing->description ?: '-' }}</td>
                        <td class="px-6 py-4">
                            @if($routing->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                            @else
                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('system.notification-routings.edit', $routing) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">Edit</a>
                                <form method="POST" action="{{ route('system.notification-routings.destroy', $routing) }}" onsubmit="return confirm('Hapus aturan routing ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-slate-400">Belum ada aturan routing.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
