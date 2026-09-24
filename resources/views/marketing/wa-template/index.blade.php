@extends('layouts.dashboard')

@section('title', 'Template Pesan WhatsApp')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Manajemen Template Pesan Saya</h1>
            <p class="text-sm text-gray-400 mt-0.5">Template digunakan saat menghubungi calon member via WhatsApp.</p>
        </div>
        <button
            x-data
            @click="$dispatch('open-add-template')"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
            <span class="material-symbols-outlined text-base">add</span>
            Buat Template
        </button>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Template list --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @forelse ($templates as $template)
            <div
                x-data="{ editing: false }"
                class="border-b border-gray-50 last:border-b-0 px-5 py-4 hover:bg-gray-50/60 transition-colors">

                {{-- View mode --}}
                <div x-show="!editing">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900">{{ $template->template_name }}</p>
                            <p class="text-sm text-gray-500 mt-1 whitespace-pre-wrap break-words">{{ $template->template_body }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button
                                @click="editing = true"
                                class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none">
                                Edit
                            </button>
                            <form method="POST" action="{{ route('wa-templates.destroy', $template) }}"
                                onsubmit="return confirm('Hapus template \'{{ addslashes($template->template_name) }}\'?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors focus:outline-none">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Edit mode --}}
                <div x-show="editing" x-cloak>
                    <form method="POST" action="{{ route('wa-templates.update', $template) }}" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Nama Template</label>
                            <input type="text" name="template_name"
                                value="{{ old('template_name', $template->template_name) }}"
                                class="w-full border border-gray-200 rounded-lg text-sm px-3 py-2 bg-gray-50 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 outline-none transition-colors"
                                maxlength="100" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Isi Pesan</label>
                            <textarea name="template_body" rows="4"
                                class="w-full border border-gray-200 rounded-lg text-sm px-3 py-2 bg-gray-50 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 outline-none transition-colors resize-none"
                                required>{{ old('template_body', $template->template_body) }}</textarea>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit"
                                class="px-4 py-1.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none transition-colors">
                                Simpan
                            </button>
                            <button type="button" @click="editing = false"
                                class="px-4 py-1.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-5 py-12 text-center">
                <span class="material-symbols-outlined text-4xl text-gray-300 block mb-2">chat_bubble_outline</span>
                <p class="text-sm text-gray-500">Belum ada template. Buat template pertama Anda.</p>
            </div>
        @endforelse
    </div>

    {{-- Add Template Modal --}}
    <div
        x-data="{ open: false }"
        @open-add-template.window="open = true"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40"
        @click.self="open = false"
        @keydown.escape.window="open = false">

        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg" @click.stop>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Buat Template Baru</h2>
                <button @click="open = false" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form method="POST" action="{{ route('wa-templates.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Nama Template <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="template_name" value="{{ old('template_name') }}"
                        placeholder="cth: Sapaan Pagi"
                        class="w-full border border-gray-200 rounded-lg text-sm px-3 py-2 bg-gray-50 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 outline-none transition-colors"
                        maxlength="100" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                        Isi Pesan <span class="text-red-500">*</span>
                    </label>
                    <textarea name="template_body" rows="5"
                        placeholder="Halo {nama}, kami dari Mindsia..."
                        class="w-full border border-gray-200 rounded-lg text-sm px-3 py-2 bg-gray-50 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 outline-none transition-colors resize-none"
                        required>{{ old('template_body') }}</textarea>
                    <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-2">Variabel tersedia:</p>
                        <div class="flex flex-wrap gap-1.5" x-data="{ copied: null }">
                            @foreach (['{nama}' => 'Nama calon member', '{wa}' => 'Nomor WhatsApp', '{institusi}' => 'Asal institusi', '{sosialisasi}' => 'Lokasi sosialisasi', '{status}' => 'Status prospek'] as $var => $desc)
                                <button type="button"
                                    title="{{ $desc }}"
                                    @click="navigator.clipboard.writeText('{{ $var }}'); copied = '{{ $var }}'; setTimeout(() => copied = null, 1500)"
                                    :class="copied === '{{ $var }}' ? 'bg-indigo-50 border-indigo-300 text-indigo-700' : 'bg-white border-gray-200 text-indigo-600 hover:bg-indigo-50 hover:border-indigo-300'"
                                    class="inline-flex items-center px-2 py-0.5 border rounded text-[11px] font-mono transition-colors">
                                    <span x-show="copied !== '{{ $var }}'">{{ $var }}</span>
                                    <span x-show="copied === '{{ $var }}'" x-cloak class="text-indigo-600">✓ disalin</span>
                                </button>
                            @endforeach
                        </div>
                        <p class="text-[10px] text-gray-400 mt-2">Klik variabel untuk menyalin ke clipboard. Variabel otomatis diganti saat menghubungi calon member.</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="open = false"
                        class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none">
                        Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
