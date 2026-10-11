@extends('layouts.dashboard')

@section('title', 'Routing Notifikasi')
@section('header_title', 'Routing Notifikasi')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{
    tab: '{{ request('tab') === 'rules' ? 'rules' : 'positions' }}',
    deleteModalOpen: false,
    deleteAction: '',
    deleteTitle: '',
    confirmDelete(actionUrl, title) {
        this.deleteAction = actionUrl;
        this.deleteTitle = title;
        this.deleteModalOpen = true;
    }
}">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider font-jakarta" style="color: var(--dash-nav-section);">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight mt-1" style="color: var(--dash-text-heading);">Routing Notifikasi</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Konfigurasi pengiriman notifikasi otomatis berdasarkan posisi, channel, dan scope.</p>
        </div>
        <a href="{{ route('system.notification-routings.create') }}"
           class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg px-5 text-sm font-bold text-white shadow-sm transition-opacity hover:opacity-90"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span> Tambah Aturan Kustom
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/80 px-4 py-3 text-sm text-emerald-800 shadow-sm">
            <span class="material-symbols-outlined text-[20px] text-emerald-600">check_circle</span>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex border-b" style="border-color: var(--dash-border-subtle);">
        <button @click="tab = 'positions'"
            :class="tab === 'positions' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80'"
            :style="tab === 'positions' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);'"
            class="px-5 py-3 text-sm transition-colors">
            Routing per Posisi
        </button>
        <button @click="tab = 'rules'"
            :class="tab === 'rules' ? 'border-b-2 font-bold' : 'font-medium hover:opacity-80'"
            :style="tab === 'rules' ? 'border-color: var(--dash-primary); color: var(--dash-primary);' : 'color: var(--dash-text-muted);'"
            class="px-5 py-3 text-sm transition-colors">
            Semua Aturan (Kustom)
            @if ($routings->isNotEmpty())
                <span class="ml-1.5 inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $routings->count() }}</span>
            @endif
        </button>
    </div>

    {{-- Tab 1: Routing per Posisi (Identik Manajemen Permission) --}}
    <div x-show="tab === 'positions'">
        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);">
            <div class="border-b px-5 py-3.5" style="border-color: var(--dash-border-subtle); background-color: var(--dash-page-bg);">
                <p class="text-xs font-semibold" style="color: var(--dash-text-muted);">Pilih posisi di bawah dan klik "Kelola" untuk mengatur event notifikasi yang diterima serta channel pengirimannya.</p>
            </div>
            <div class="divide-y" style="border-color: var(--dash-border-subtle);">
                @forelse ($positions as $position)
                    @php
                        $rulesCount = $position->notificationRoutings->count();
                    @endphp
                    <div class="flex items-center justify-between gap-4 px-5 py-4 transition-colors hover:bg-slate-50/50">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $position->position_name }}</p>
                            <div class="mt-0.5 flex items-center gap-2">
                                @if ($position->role)
                                    <span class="font-mono text-xs" style="color: var(--dash-text-muted);">role: {{ $position->role->name }}</span>
                                    <span class="text-xs text-slate-300">·</span>
                                @endif
                                @if ($rulesCount > 0)
                                    <span class="inline-flex rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $rulesCount }} aturan aktif</span>
                                @else
                                    <span class="text-xs italic" style="color: var(--dash-text-muted);">Belum ada routing aktif</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('system.notification-routings.position.edit', $position) }}"
                            class="inline-flex shrink-0 min-h-9 items-center rounded-lg border px-3 text-xs font-semibold transition-colors hover:bg-slate-100/70"
                            style="border-color: var(--dash-border-subtle); color: var(--dash-text-heading);">
                            <span class="material-symbols-outlined text-[15px] mr-1" style="color: var(--dash-primary);">tune</span> Kelola
                        </a>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center text-sm" style="color: var(--dash-text-muted);">Belum ada posisi.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Tab 2: Semua Aturan (Tabel Flat CRUD) --}}
    <div x-show="tab === 'rules'" style="display:none;">
        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--dash-card-bg); border-color: var(--dash-border-subtle);">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y text-sm" style="border-color: var(--dash-border-subtle);">
                    <thead style="background-color: var(--dash-page-bg);">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Event Key</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Target Posisi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Channel</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Scope</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Deskripsi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-border-subtle);">
                        @forelse($routings as $routing)
                        <tr class="transition-colors hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <code class="rounded px-2 py-0.5 text-xs font-mono" style="background-color: var(--dash-page-bg); color: var(--dash-text-heading);">{{ $routing->event_key }}</code>
                            </td>
                            <td class="px-6 py-4 font-medium" style="color: var(--dash-text-heading);">{{ $routing->position?->position_name ?? 'Semua Posisi' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $chColors = [
                                        'email' => 'bg-blue-100 text-blue-800',
                                        'telegram' => 'bg-sky-100 text-sky-800',
                                        'in-app' => 'bg-violet-100 text-violet-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $chColors[$routing->channel] ?? 'bg-slate-100 text-slate-700' }}">{{ $routing->channel }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">{{ $routing->scope }}</span>
                            </td>
                            <td class="px-6 py-4 max-w-[200px] truncate" style="color: var(--dash-text-muted);" title="{{ $routing->description }}">{{ $routing->description ?: '-' }}</td>
                            <td class="px-6 py-4">
                                @if($routing->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                                @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('system.notification-routings.edit', $routing) }}"
                                       class="rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors hover:bg-slate-100/70"
                                       style="border-color: var(--dash-border-subtle); color: var(--dash-text-heading);">Edit</a>
                                    <button type="button"
                                        @click="confirmDelete('{{ route('system.notification-routings.destroy', $routing) }}', '{{ $routing->event_key }}')"
                                        class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm" style="color: var(--dash-text-muted);">Belum ada aturan routing kustom.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus (Custom Alpine.js, No native confirm) --}}
    <div x-show="deleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="deleteModalOpen = false"></div>
        <div class="relative w-full max-w-md rounded-2xl shadow-xl transition-all" style="background-color: var(--dash-card-bg);">
            <div class="p-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                        <span class="material-symbols-outlined text-[20px]">delete</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold font-jakarta" style="color: var(--dash-text-heading);">Hapus Aturan Routing</h3>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <p class="mt-4 text-sm" style="color: var(--dash-text-body);">
                    Apakah Anda yakin ingin menghapus aturan routing untuk event <strong x-text="deleteTitle" class="font-mono text-xs"></strong>?
                </p>
            </div>
            <div class="flex items-center justify-end gap-3 rounded-b-2xl px-6 py-4 border-t" style="border-color: var(--dash-border-subtle); background-color: var(--dash-page-bg);">
                <button type="button" @click="deleteModalOpen = false"
                    class="rounded-lg border px-4 py-2 text-xs font-semibold transition-colors hover:bg-slate-100/70"
                    style="border-color: var(--dash-border-subtle); color: var(--dash-text-heading);">Batal</button>
                <form :action="deleteAction" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-700 transition-colors">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
