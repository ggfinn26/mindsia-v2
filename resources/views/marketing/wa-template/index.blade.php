@extends('layouts.dashboard')

@section('title', 'Template WhatsApp - MINDSIA')
@section('header_title', 'Template WhatsApp')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Pemasaran</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Template WhatsApp</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola template pesan cepat untuk follow-up dan komunikasi dengan calon anggota.
            </p>
        </div>
        <button x-data @click="$dispatch('open-add-template')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Buat Template
        </button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Template List Card --}}
    <div class="rounded-2xl"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        @forelse($templates as $template)
        <div x-data="{ editing: false, showDeleteModal: false }"
             class="{{ !$loop->last ? 'border-b' : '' }}"
             style="{{ !$loop->last ? 'border-color: var(--dash-card-border);' : '' }}">

            {{-- View mode --}}
            <div x-show="!editing" class="p-6 transition-colors"
                 onmouseover="if(!this.closest('[x-data]').__x.$data.editing) this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                 onmouseout="this.style.backgroundColor='transparent'">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold mb-2"
                           style="color: var(--dash-text-heading);">{{ $template->template_name }}</p>
                        <div class="rounded-xl px-4 py-3 text-[13px] font-mono leading-relaxed whitespace-pre-wrap"
                             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">{{ $template->template_body }}</div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 sm:pt-0.5">
                        <button type="button" @click="editing = true"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            <span class="material-symbols-outlined text-[14px]">edit</span>
                            Edit
                        </button>
                        <button type="button" @click="showDeleteModal = true"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-danger, #dc2626);">
                            <span class="material-symbols-outlined text-[14px]">delete</span>
                            Hapus
                        </button>

                        {{-- Modal Konfirmasi Hapus Template --}}
                        <template x-teleport="body">
                            <div x-show="showDeleteModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
                                <div x-show="showDeleteModal"
                                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                     class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                                     @click="showDeleteModal = false"></div>

                                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                                        <div x-show="showDeleteModal" @click.away="showDeleteModal = false"
                                             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                             class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                            <div class="rounded-t-2xl px-6 py-5"
                                                 style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                                <div class="flex items-start gap-3.5">
                                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                                        <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                                    </div>
                                                    <div>
                                                        <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Template?</h3>
                                                        <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                                            Apakah Anda yakin ingin menghapus template "<strong>{{ $template->template_name }}</strong>"? Tindakan ini tidak dapat dibatalkan.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                                <button type="button" @click="showDeleteModal = false"
                                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                    Batal
                                                </button>
                                                <form method="POST" action="{{ route('wa-templates.destroy', $template) }}" class="m-0">
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
                        </template>
                    </div>
                </div>
            </div>

            {{-- Edit mode --}}
            <div x-show="editing" x-cloak class="p-6">
                <div class="rounded-xl p-5"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <p class="text-sm font-bold mb-4" style="color: var(--dash-text-heading);">Edit Template</p>
                    <form method="POST" action="{{ route('wa-templates.update', $template) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Nama Template <span class="text-red-500">*</span></label>
                            <input type="text" name="template_name"
                                   value="{{ old('template_name', $template->template_name) }}"
                                   maxlength="100" required
                                   class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Isi Pesan <span class="text-red-500">*</span></label>
                            <textarea name="template_body" rows="4" required
                                      class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all font-mono resize-none"
                                      style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                      onfocus="this.style.borderColor='var(--dash-primary)'"
                                      onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('template_body', $template->template_body) }}</textarea>
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button type="button" @click="editing = false"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                                Batal
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[16px]">save</span>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        @empty
        <div class="px-5 py-16 text-center">
            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                  style="color: var(--dash-text-faint);">chat</span>
            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada template WhatsApp.</p>
            <p class="text-xs mt-1 mb-4" style="color: var(--dash-text-faint);">Buat template untuk mempercepat pengiriman pesan ke calon anggota.</p>
            <button x-data @click="$dispatch('open-add-template')"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Buat Template
            </button>
        </div>
        @endforelse
    </div>

</div>

{{-- Add Template Modal (Custom Dashboard Modal Dialog) --}}
<div x-data="{ open: false }"
     @open-add-template.window="open = true"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     @keydown.escape.window="open = false"
     style="display: none;">

    {{-- Backdrop --}}
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
         @click="open = false"></div>

    {{-- Panel (No overflow-hidden) --}}
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative w-full max-w-lg rounded-2xl shadow-2xl z-10"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
         @click.stop>

        {{-- Modal header --}}
        <div class="px-6 py-5 rounded-t-2xl flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">chat</span>
                <h2 class="text-base font-bold font-jakarta" style="color: var(--dash-text-heading);">Buat Template Baru</h2>
            </div>
            <button type="button" @click="open = false"
                    class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors"
                    style="color: var(--dash-text-muted);"
                    onmouseover="this.style.backgroundColor='var(--dash-card-border)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form method="POST" action="{{ route('wa-templates.store') }}">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Nama Template <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="template_name" value="{{ old('template_name') }}"
                           placeholder="cth: Sapaan Pagi"
                           maxlength="100" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                        Isi Pesan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="template_body" rows="5"
                              placeholder="Halo {nama}, kami dari Mindsia..."
                              required
                              class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all font-mono resize-none"
                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                              onfocus="this.style.borderColor='var(--dash-primary)'"
                              onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('template_body') }}</textarea>

                    {{-- Variable helper --}}
                    <div class="mt-3 rounded-xl p-4"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);"
                         x-data="{ copied: null }">
                        <p class="text-xs font-semibold mb-2" style="color: var(--dash-text-heading);">Variabel yang dapat digunakan:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['{nama}' => 'Nama calon member', '{wa}' => 'Nomor WhatsApp', '{institusi}' => 'Asal institusi', '{sosialisasi}' => 'Lokasi sosialisasi', '{status}' => 'Status prospek'] as $var => $desc)
                            <button type="button"
                                    title="{{ $desc }}"
                                    @click="navigator.clipboard.writeText('{{ $var }}'); copied = '{{ $var }}'; setTimeout(() => copied = null, 2000)"
                                    class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono transition-colors"
                                    :style="copied === '{{ $var }}' ? 'background-color: var(--dash-primary); color: white; border: 1px solid var(--dash-primary);' : 'background-color: var(--dash-card-bg); color: var(--dash-text-heading); border: 1px solid var(--dash-card-border);'">
                                <span x-show="copied !== '{{ $var }}'">{{ $var }}</span>
                                <span x-show="copied === '{{ $var }}'" x-cloak class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[12px]">check</span>Disalin
                                </span>
                            </button>
                            @endforeach
                        </div>
                        <p class="text-[11px] mt-2" style="color: var(--dash-text-faint);">Klik variabel untuk menyalin. Variabel akan diganti dengan data calon anggota saat dikirim.</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 rounded-b-2xl flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);">
                <button type="button" @click="open = false"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    Batal
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Simpan Template
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
