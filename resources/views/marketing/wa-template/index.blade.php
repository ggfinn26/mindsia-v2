@extends('layouts.dashboard')

@section('title', 'Template WhatsApp - MINDSIA')
@section('header_title', 'Template WhatsApp')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Template WhatsApp</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Kelola template pesan untuk menghubungi calon member.
            </p>
        </div>
        <button x-data @click="$dispatch('open-add-template')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity focus:outline-none focus:ring-2 focus:ring-offset-2"
                style="background-color: var(--dash-primary); --tw-ring-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Buat Template
        </button>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium mb-6"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm mb-6"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Template list --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @forelse($templates as $template)
            <div x-data="{ editing: false }"
                 style="border-bottom: 1px solid var(--dash-card-border);"
                 class="last:border-b-0 p-6 transition-colors">

                {{-- View mode --}}
                <div x-show="!editing">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold mb-2" style="color: var(--dash-text-heading);">
                                {{ $template->template_name }}
                            </h3>
                            <div class="text-[13px] font-mono leading-relaxed px-4 py-3 rounded-xl whitespace-pre-wrap"
                                 style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">{{ $template->template_body }}</div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0 sm:pt-1">
                            <button @click="editing = true"
                                    class="text-sm font-medium hover:opacity-75 transition-opacity"
                                    style="color: var(--dash-primary);">
                                Edit
                            </button>
                            <span style="color: var(--dash-card-border);">|</span>
                            <form method="POST" action="{{ route('wa-templates.destroy', $template) }}"
                                  onsubmit="return confirm('Hapus template \'{{ addslashes($template->template_name) }}\'?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-sm font-medium hover:opacity-75 transition-opacity"
                                        style="color: #dc2626;">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Edit mode --}}
                <div x-show="editing" x-cloak class="rounded-xl p-5"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <h3 class="text-sm font-bold mb-4" style="color: var(--dash-text-heading);">Edit Template</h3>
                    <form method="POST" action="{{ route('wa-templates.update', $template) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                                Nama Template
                            </label>
                            <input type="text" name="template_name"
                                   value="{{ old('template_name', $template->template_name) }}"
                                   maxlength="100" required
                                   class="block w-full rounded-xl text-sm px-3 py-2.5 focus:outline-none focus:ring-2"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); --tw-ring-color: var(--dash-primary);">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                                Isi Pesan
                            </label>
                            <textarea name="template_body" rows="4" required
                                      class="block w-full rounded-xl text-sm font-mono text-[13px] px-3 py-2.5 focus:outline-none focus:ring-2"
                                      style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); --tw-ring-color: var(--dash-primary);">{{ old('template_body', $template->template_body) }}</textarea>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="editing = false"
                                    class="px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                                    style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-primary);">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        @empty
            <div class="px-6 py-16 text-center">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">chat_bubble_outline</span>
                <h3 class="text-sm font-semibold mb-1" style="color: var(--dash-text-heading);">
                    Belum ada template
                </h3>
                <p class="text-sm mb-6" style="color: var(--dash-text-muted);">
                    Buat template pesan pertama untuk mempercepat pengiriman ke calon member.
                </p>
                <button @click="$dispatch('open-add-template')" type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Buat Template
                </button>
            </div>
        @endforelse
    </div>

    {{-- Add Template Modal --}}
    <div x-data="{ open: false }"
         @open-add-template.window="open = true"
         x-show="open"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         role="dialog" aria-modal="true">

        {{-- Backdrop --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-black/50"
             @click="open = false"></div>

        {{-- Panel --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative rounded-2xl shadow-xl w-full max-w-lg overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             @click.stop>

            <div class="flex items-center justify-between px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border);">
                <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                    Buat Template Baru
                </h2>
                <button @click="open = false"
                        class="flex items-center justify-center w-8 h-8 rounded-lg hover:opacity-75 transition-opacity"
                        style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form method="POST" action="{{ route('wa-templates.store') }}" class="px-6 py-5">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Nama Template <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" name="template_name" value="{{ old('template_name') }}"
                               placeholder="cth: Sapaan Pagi"
                               maxlength="100" required
                               class="block w-full rounded-xl text-sm px-3 py-2.5 focus:outline-none focus:ring-2"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); --tw-ring-color: var(--dash-primary);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Isi Pesan <span style="color: #dc2626;">*</span>
                        </label>
                        <textarea name="template_body" rows="5"
                                  placeholder="Halo {nama}, kami dari Mindsia..."
                                  required
                                  class="block w-full rounded-xl text-sm font-mono text-[13px] px-3 py-2.5 focus:outline-none focus:ring-2 resize-none"
                                  style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); --tw-ring-color: var(--dash-primary);">{{ old('template_body') }}</textarea>

                        {{-- Variable hints --}}
                        <div class="mt-3 px-4 py-3 rounded-xl"
                             style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-primary);"
                             x-data="{ copied: null }">
                            <p class="text-xs font-semibold mb-2" style="color: var(--dash-primary);">
                                Variabel yang dapat digunakan:
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['{nama}' => 'Nama calon member', '{wa}' => 'Nomor WhatsApp', '{institusi}' => 'Asal institusi', '{sosialisasi}' => 'Lokasi sosialisasi', '{status}' => 'Status prospek'] as $var => $desc)
                                    <button type="button"
                                            title="{{ $desc }}"
                                            @click="navigator.clipboard.writeText('{{ $var }}'); copied = '{{ $var }}'; setTimeout(() => copied = null, 2000)"
                                            :class="copied === '{{ $var }}' ? 'text-white' : ''"
                                            :style="copied === '{{ $var }}' ? 'background-color: var(--dash-primary); border-color: var(--dash-primary); color: white;' : 'background-color: var(--dash-card-bg); border-color: var(--dash-card-border); color: var(--dash-text-body);'"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border text-xs font-mono transition-colors focus:outline-none focus:ring-2"
                                            style="--tw-ring-color: var(--dash-primary);">
                                        <span x-show="copied !== '{{ $var }}'">{{ $var }}</span>
                                        <span x-show="copied === '{{ $var }}'" x-cloak class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[12px]">check</span>
                                            Disalin
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                            <p class="text-xs mt-2" style="color: var(--dash-primary); opacity: 0.75;">
                                Klik variabel untuk menyalin. Variabel diganti data member saat dikirim.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4"
                     style="border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="open = false"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                            style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
