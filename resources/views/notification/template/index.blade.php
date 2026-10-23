@extends('layouts.dashboard')

@section('title', 'Template Notifikasi')
@section('header_title', 'Template Notifikasi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
         deleteModalOpen: false,
         deleteAction: '',
         deleteKey: '',
         confirmDelete(actionUrl, key) {
             this.deleteAction = actionUrl;
             this.deleteKey = key;
             this.deleteModalOpen = true;
         }
     }">

    {{-- Page Header (Pola A) --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Notifikasi</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Template Notifikasi</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola template pesan, tipe channel, dan variabel notifikasi sistem.
            </p>
        </div>
        @can('notification.template.create')
        <a href="{{ route('notification-templates.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0 shadow-sm"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Buat Template
        </a>
        @endcan
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- Table Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Template Key</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Tipe Channel</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Subject</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Variabel</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $tpl)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5">
                            <code class="px-2.5 py-1 rounded-md text-xs font-mono font-medium"
                                  style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                                {{ $tpl->template_key }}
                            </code>
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $channelStyles = [
                                    'email' => 'background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe;',
                                    'telegram' => 'background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;',
                                    'in-app' => 'background-color: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe;',
                                ];
                                $channelLabels = [
                                    'email' => 'Email',
                                    'telegram' => 'Telegram',
                                    'in-app' => 'In-App',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="{{ $channelStyles[$tpl->type] ?? 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);' }}">
                                {{ $channelLabels[$tpl->type] ?? ucfirst($tpl->type) }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-sm" style="color: var(--dash-text-body);">
                            {{ $tpl->subject ?: '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-sm" style="color: var(--dash-text-muted);">
                            <span class="inline-flex items-center gap-1 font-medium">
                                <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-faint);">data_object</span>
                                {{ $tpl->variables->count() }} variabel
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('notification-templates.show', $tpl) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    Lihat
                                </a>
                                @can('notification.template.update')
                                <a href="{{ route('notification-templates.edit', $tpl) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                    Edit
                                </a>
                                @endcan
                                @can('notification.template.delete')
                                <button type="button"
                                        @click="confirmDelete('{{ route('notification-templates.destroy', $tpl) }}', '{{ $tpl->template_key }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[14px]">delete</span>
                                    Hapus
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">drafts</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada template notifikasi.</p>
                            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Klik tombol "Buat Template" untuk menambahkan template baru.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus Template (Custom Alpine.js, No browser confirm) --}}
    <div x-show="deleteModalOpen" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
        <div x-show="deleteModalOpen"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
             @click="deleteModalOpen = false"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="deleteModalOpen" @click.away="deleteModalOpen = false"
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                    {{-- Header modal (rounded-t-2xl) TANPA overflow-hidden --}}
                    <div class="rounded-t-2xl px-6 py-5"
                         style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                            </div>
                            <div>
                                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Template Notifikasi?</h3>
                                <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                    Apakah Anda yakin ingin menghapus template <strong x-text="deleteKey" class="font-mono text-xs"></strong>? Data yang sudah dihapus tidak dapat dikembalikan.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Footer modal (rounded-b-2xl) --}}
                    <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                         style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                        <button type="button" @click="deleteModalOpen = false"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                        <form :action="deleteAction" method="POST" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-danger, #dc2626);">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
