@extends('layouts.dashboard')

@section('title', 'Routing Notifikasi - MINDSIA')
@section('header_title', 'Routing Notifikasi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto space-y-6">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Sistem & Akses</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Routing Notifikasi</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Konfigurasi event → posisi penerima → channel pengiriman.
            </p>
        </div>
        <a href="{{ route('system.notification-routings.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity self-start sm:self-auto"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Aturan
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Event Key</th>
                        <th class="hidden md:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Target Posisi</th>
                        <th class="hidden sm:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Channel</th>
                        <th class="hidden lg:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Scope</th>
                        <th class="hidden xl:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Deskripsi</th>
                        <th class="hidden sm:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routings as $routing)
                        @php
                            $chStyle = match($routing->channel) {
                                'email'    => 'background-color:var(--dash-primary-bg);color:var(--dash-primary);',
                                'telegram' => 'background-color:#e0f7fb;color:#007a99;',
                                'in-app'   => 'background-color:#ede9fe;color:#7c3aed;',
                                default    => 'background-color:var(--dash-nav-hover-bg);color:var(--dash-text-muted);',
                            };
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">

                            {{-- Event Key --}}
                            <td class="px-5 py-4">
                                <code class="rounded-lg px-2 py-0.5 text-xs font-mono"
                                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);">
                                    {{ $routing->event_key }}
                                </code>
                                {{-- Inline badges on mobile --}}
                                <div class="sm:hidden flex items-center gap-1.5 mt-1.5 flex-wrap">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                          style="{{ $chStyle }}">{{ $routing->channel }}</span>
                                    @if($routing->is_active)
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                              style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                                    @else
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                              style="background-color: #fee2e2; color: #dc2626;">Nonaktif</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Target Posisi --}}
                            <td class="hidden md:table-cell px-5 py-4 text-sm"
                                style="color: var(--dash-text-body);">
                                {{ $routing->position?->name ?? 'Semua Posisi' }}
                            </td>

                            {{-- Channel --}}
                            <td class="hidden sm:table-cell px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                      style="{{ $chStyle }}">{{ $routing->channel }}</span>
                            </td>

                            {{-- Scope --}}
                            <td class="hidden lg:table-cell px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                      style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">
                                    {{ $routing->scope }}
                                </span>
                            </td>

                            {{-- Deskripsi --}}
                            <td class="hidden xl:table-cell px-5 py-4 text-sm max-w-[180px] truncate"
                                style="color: var(--dash-text-muted);"
                                title="{{ $routing->description }}">
                                {{ $routing->description ?: '—' }}
                            </td>

                            {{-- Status --}}
                            <td class="hidden sm:table-cell px-5 py-4">
                                @if($routing->is_active)
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                          style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium"
                                          style="background-color: #fee2e2; color: #dc2626;">Nonaktif</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('system.notification-routings.edit', $routing) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>
                                        <span class="hidden sm:inline">Edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('system.notification-routings.destroy', $routing) }}"
                                          onsubmit="return confirm('Hapus aturan routing ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                                                style="background-color: #dc2626;">
                                            <span class="material-symbols-outlined text-[14px]">delete</span>
                                            <span class="hidden sm:inline">Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">notifications_off</span>
                                <p class="text-sm" style="color: var(--dash-text-muted);">Belum ada aturan routing.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
