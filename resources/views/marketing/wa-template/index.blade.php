@extends('layouts.dashboard')

@section('title', 'Template Pesan WhatsApp')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-end mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl text-gray-900 font-bold tracking-tight">Template WhatsApp</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola template pesan untuk mempermudah Anda menghubungi calon member.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <button
                x-data
                @click="$dispatch('open-add-template')"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Buat Template
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-md bg-emerald-50 border border-emerald-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-md bg-rose-50 border border-rose-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-rose-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-rose-800">Terdapat kesalahan pada input Anda:</h3>
                    <div class="mt-2 text-sm text-rose-700">
                        <ul role="list" class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Template list --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @forelse ($templates as $template)
            <div
                x-data="{ editing: false }"
                class="border-b border-gray-200 last:border-b-0 p-6 hover:bg-gray-50/50 transition-colors">

                {{-- View mode --}}
                <div x-show="!editing">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-semibold text-gray-900">{{ $template->template_name }}</h3>
                            <div class="mt-2 text-sm text-gray-600 bg-gray-50 rounded-md p-4 border border-gray-200 whitespace-pre-wrap font-mono text-[13px] leading-relaxed">{{ $template->template_body }}</div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0 sm:pt-1">
                            <button
                                @click="editing = true"
                                class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-900 transition-colors">
                                Edit
                            </button>
                            <span class="text-gray-300">|</span>
                            <form method="POST" action="{{ route('wa-templates.destroy', $template) }}"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus template \'{{ addslashes($template->template_name) }}\'?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center text-sm font-medium text-rose-600 hover:text-rose-900 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Edit mode --}}
                <div x-show="editing" x-cloak class="bg-white border border-indigo-200 rounded-lg p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 mb-4">Edit Template</h3>
                    <form method="POST" action="{{ route('wa-templates.update', $template) }}" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Template</label>
                            <input type="text" name="template_name"
                                value="{{ old('template_name', $template->template_name) }}"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                maxlength="100" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Isi Pesan</label>
                            <textarea name="template_body" rows="4"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono text-[13px]"
                                required>{{ old('template_body', $template->template_body) }}</textarea>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="editing = false"
                                class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="inline-flex justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada template</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum membuat template WhatsApp. Mulai buat sekarang.</p>
                <div class="mt-6">
                    <button @click="$dispatch('open-add-template')" type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        Buat Template
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Add Template Modal --}}
    <div
        x-data="{ open: false }"
        @open-add-template.window="open = true"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <div x-show="open" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="open = false" aria-hidden="true"></div>

        <div x-show="open" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="relative bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden" @click.stop>
            
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900" id="modal-title">Buat Template Baru</h2>
                <button @click="open = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('wa-templates.store') }}" class="px-6 py-5">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Template <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="template_name" value="{{ old('template_name') }}"
                            placeholder="cth: Sapaan Pagi"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            maxlength="100" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Isi Pesan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="template_body" rows="5"
                            placeholder="Halo {nama}, kami dari Mindsia..."
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono text-[13px] resize-none"
                            required>{{ old('template_body') }}</textarea>
                        
                        <div class="mt-3 p-4 bg-indigo-50/50 rounded-md border border-indigo-100">
                            <p class="text-xs font-semibold text-indigo-900 mb-2">Variabel yang dapat digunakan:</p>
                            <div class="flex flex-wrap gap-2" x-data="{ copied: null }">
                                @foreach (['{nama}' => 'Nama calon member', '{wa}' => 'Nomor WhatsApp', '{institusi}' => 'Asal institusi', '{sosialisasi}' => 'Lokasi sosialisasi', '{status}' => 'Status prospek'] as $var => $desc)
                                    <button type="button"
                                        title="{{ $desc }}"
                                        @click="navigator.clipboard.writeText('{{ $var }}'); copied = '{{ $var }}'; setTimeout(() => copied = null, 2000)"
                                        :class="copied === '{{ $var }}' ? 'bg-indigo-600 text-white border-transparent' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'"
                                        class="inline-flex items-center px-2.5 py-1 border rounded shadow-sm text-xs font-mono transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500">
                                        <span x-show="copied !== '{{ $var }}'">{{ $var }}</span>
                                        <span x-show="copied === '{{ $var }}'" x-cloak class="flex items-center">
                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                            </svg>
                                            Disalin
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                            <p class="text-xs text-indigo-700/70 mt-2">Klik variabel untuk menyalin otomatis. Variabel akan diganti dengan data member saat dikirim.</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="open = false"
                        class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="inline-flex justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
