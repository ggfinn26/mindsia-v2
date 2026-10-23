@extends('layouts.dashboard')

@section('title', 'Detail Template Notifikasi')
@section('header_title', 'Detail Template Notifikasi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{
         showVarForm: false,
         deleteVarModalOpen: false,
         deleteVarAction: '',
         deleteVarName: '',
         confirmDeleteVar(actionUrl, name) {
             this.deleteVarAction = actionUrl;
             this.deleteVarName = name;
             this.deleteVarModalOpen = true;
         }
     }">

    {{-- Page Header (Pola Detail B3) --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('notification-templates.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Template Notifikasi
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Notifikasi</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Detail Template Notifikasi</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Rincian format pesan dan dokumentasi variabel payload notifikasi.
            </p>
        </div>
        @can('notification.template.update')
        <a href="{{ route('notification-templates.edit', $template) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors shrink-0 shadow-sm"
           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-heading);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[16px]">edit</span>
            Edit Template
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

    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <div class="flex items-center gap-2 font-semibold mb-1">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <span>Terdapat kesalahan:</span>
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Card Info Template --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="rounded-t-2xl px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Informasi Template</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Konfigurasi identitas channel dan template pesan.</p>
        </div>
        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1"
                           style="color: var(--dash-text-muted);">Template Key</label>
                    <code class="inline-block px-3 py-1.5 rounded-lg text-sm font-mono font-medium"
                          style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                        {{ $template->template_key }}
                    </code>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1"
                           style="color: var(--dash-text-muted);">Tipe Channel</label>
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
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                          style="{{ $channelStyles[$template->type] ?? 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);' }}">
                        {{ $channelLabels[$template->type] ?? ucfirst($template->type) }}
                    </span>
                </div>
            </div>

            @if ($template->subject)
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1"
                       style="color: var(--dash-text-muted);">Subject (Email)</label>
                <p class="text-sm font-medium px-4 py-2.5 rounded-lg"
                   style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                    {{ $template->subject }}
                </p>
            </div>
            @endif

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1"
                       style="color: var(--dash-text-muted);">Isi Pesan (Body)</label>
                <pre class="p-4 rounded-xl text-sm whitespace-pre-wrap font-mono leading-relaxed"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">{{ $template->body }}</pre>
            </div>
        </div>
    </div>

    {{-- Card Dokumentasi Variabel --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="rounded-t-2xl px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div>
                <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Dokumentasi Variabel</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                    Variabel placeholder yang dapat disisipkan saat mengirim notifikasi.
                </p>
            </div>
            @can('notification.variable.create')
            <button type="button"
                    @click="showVarForm = !showVarForm"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90 self-start sm:self-auto shadow-sm"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]" x-text="showVarForm ? 'close' : 'add'">add</span>
                <span x-text="showVarForm ? 'Tutup Form' : 'Tambah Variabel'">Tambah Variabel</span>
            </button>
            @endcan
        </div>

        {{-- Form Tambah Variabel (Collapse Alpine.js) --}}
        @can('notification.variable.create')
        <div x-show="showVarForm" style="display: none;"
             class="p-6 border-b"
             style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
            <form action="{{ route('notification-templates.variables.store', $template) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">
                            Nama Variabel <span class="text-red-500">*</span>
                        </label>
                        <input name="variable_name" type="text" placeholder="nama_variabel" value="{{ old('variable_name') }}"
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none font-mono transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                               required />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">
                            Tipe Data <span class="text-red-500">*</span>
                        </label>
                        <input name="variable_type" type="text" placeholder="string / date / number" value="{{ old('variable_type') }}"
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                               required />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-body);">
                            Deskripsi Variabel
                        </label>
                        <input name="variable_description" type="text" placeholder="Keterangan isi variabel" value="{{ old('variable_description') }}"
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="showVarForm = false"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity shadow-sm"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        Simpan Variabel
                    </button>
                </div>
            </form>
        </div>
        @endcan

        {{-- Tabel Variabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Nama Variabel</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Tipe</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Deskripsi</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($template->variables as $var)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3.5">
                            <code class="px-2.5 py-1 rounded-md text-xs font-mono font-medium"
                                  style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">&#123;&#123; {{ $var->variable_name }} &#125;&#125;</code>
                        </td>
                        <td class="px-5 py-3.5 text-sm" style="color: var(--dash-text-body);">
                            <span class="px-2 py-0.5 rounded text-xs font-mono"
                                  style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">
                                {{ $var->variable_type }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-sm" style="color: var(--dash-text-body);">
                            {{ $var->variable_description ?: '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @can('notification.variable.delete')
                            <button type="button"
                                    @click="confirmDeleteVar('{{ route('notification-templates.variables.destroy', [$template, $var]) }}', '{{ $var->variable_name }}')"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                    style="background-color: var(--dash-danger, #dc2626);">
                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                Hapus
                            </button>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-2"
                                  style="color: var(--dash-text-faint);">data_object</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada variabel terdokumentasi.</p>
                            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Klik "Tambah Variabel" untuk menambahkan dokumentasi placeholder.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus Variabel (Custom Alpine.js, Zero native confirm) --}}
    <div x-show="deleteVarModalOpen" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
        <div x-show="deleteVarModalOpen"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
             @click="deleteVarModalOpen = false"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="deleteVarModalOpen" @click.away="deleteVarModalOpen = false"
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
                                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Variabel?</h3>
                                <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                    Apakah Anda yakin ingin menghapus variabel <code class="font-mono text-xs font-semibold" style="color: var(--dash-text-heading);" x-text="'{{' + deleteVarName + '}}'"></code>? Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Footer modal (rounded-b-2xl) --}}
                    <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                         style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                        <button type="button" @click="deleteVarModalOpen = false"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                        <form :action="deleteVarAction" method="POST" class="m-0">
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
