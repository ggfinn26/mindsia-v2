@extends('layouts.dashboard')

@section('title', 'Template Slip Gaji')
@section('header_title', 'Template Slip Gaji')

@section('content')
<div class="max-w-4xl mx-auto space-y-6"
     x-data="{
         activeTab: '{{ $activeTab }}',
         selectedTestType: '{{ $testType }}',
         showDeleteModal: false,
         deleteActionUrl: '',
         deleteLabel: '',
         showApplyModal: false,
         applyActionUrl: '',
         applyRawToken: '',
         applyLabel: '',
         showAddForm: false,
         showVarGuide: false,
         copiedVar: null,
         copyToClipboard(text) {
             navigator.clipboard.writeText(text);
             this.copiedVar = text;
             setTimeout(() => { if (this.copiedVar === text) this.copiedVar = null; }, 2000);
         },
         switchTab(tabName) {
             this.activeTab = tabName;
             const url = new URL(window.location.href);
             url.searchParams.set('tab', tabName);
             window.history.replaceState({}, '', url.toString());
         },
         switchTestType(typeKey) {
             this.selectedTestType = typeKey;
             window.location.href = '{{ route('payroll.slip-templates.index') }}?tab=test&type=' + typeKey;
         }
     }">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Gaji & Bonus</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Template Slip Gaji</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola file template Word (.docx), uji coba variabel sebelum upload, dan konfigurasi assignment posisi.
            </p>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: var(--dash-status-success-bg); border-color: rgba(5, 150, 105, 0.25); color: var(--dash-status-success-text);">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: var(--dash-status-danger-bg); border-color: rgba(220, 38, 38, 0.25); color: var(--dash-status-danger-text);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- ═══ TAB NAVIGATION BAR ═══════════════════════════════════════════ --}}
    <div class="flex items-center gap-2 p-1.5 rounded-2xl border"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        {{-- Tab 1: Daftar Template --}}
        <button type="button"
                @click="switchTab('templates')"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all"
                :style="activeTab === 'templates'
                    ? 'background-color: var(--dash-primary); color: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);'
                    : 'color: var(--dash-text-muted); background-color: transparent;'">
            <span class="material-symbols-outlined text-[18px]">description</span>
            <span>Daftar Template</span>
            <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold"
                  :style="activeTab === 'templates'
                      ? 'background-color: rgba(255,255,255,0.25); color: #ffffff;'
                      : 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);'">
                {{ count($templates) }}
            </span>
        </button>

        {{-- Tab 2: Uji Coba Template --}}
        <button type="button"
                @click="switchTab('test')"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all"
                :style="activeTab === 'test'
                    ? 'background-color: var(--dash-primary); color: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);'
                    : 'color: var(--dash-text-muted); background-color: transparent;'">
            <span class="material-symbols-outlined text-[18px]">science</span>
            <span>Uji Coba Template</span>
            @if($testResult)
                <span class="flex h-2 w-2 rounded-full" style="background-color: var(--dash-status-success-text);"></span>
            @endif
        </button>

        {{-- Tab 3: Assignment Posisi --}}
        <button type="button"
                @click="switchTab('assignments')"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all"
                :style="activeTab === 'assignments'
                    ? 'background-color: var(--dash-primary); color: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);'
                    : 'color: var(--dash-text-muted); background-color: transparent;'">
            <span class="material-symbols-outlined text-[18px]">badge</span>
            <span>Assignment Posisi</span>
            <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-bold hidden sm:inline-block"
                  :style="activeTab === 'assignments'
                      ? 'background-color: rgba(255,255,255,0.25); color: #ffffff;'
                      : 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);'">
                {{ count($positions) }}
            </span>
        </button>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         TAB 1: DAFTAR TEMPLATE
    ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'templates'" x-transition.opacity.duration.150ms class="space-y-6">
        {{-- Header Info --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                    Template Aktif di Sistem ({{ count($templates) }})
                </h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                    Daftar template .docx yang saat ini digunakan saat meng-generate slip gaji.
                </p>
            </div>
            <button type="button"
                    @click="showAddForm = !showAddForm"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold transition-colors"
                    style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]" x-text="showAddForm ? 'close' : 'add'">add</span>
                <span x-text="showAddForm ? 'Tutup Form' : 'Tambah Template Baru'">Tambah Template Baru</span>
            </button>
        </div>

        {{-- Form Tambah Template Baru (Collapsible) --}}
        <div x-show="showAddForm" x-transition.opacity.duration.200ms
             class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b pb-3" style="border-color: var(--dash-card-border);">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">add_circle</span>
                    <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">
                        Tambah Template Baru
                    </h3>
                </div>
                <button type="button"
                        @click="switchTab('test')"
                        class="text-xs font-semibold hover:underline inline-flex items-center gap-1"
                        style="color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[14px]">science</span>
                    Uji coba file sebelum upload
                </button>
            </div>

            <form action="{{ route('payroll.slip-templates.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2 items-end">
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-heading);">
                            Nama Template <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="label" value="{{ old('label') }}" placeholder="Contoh: Board & Executive"
                               class="w-full rounded-lg text-sm px-3 py-2 transition-colors"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); outline: none;"
                               onfocus="this.style.borderColor='var(--dash-primary)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'"
                               required>
                        <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Nama ini akan menjadi label pilihan pada dropdown assignment posisi.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-heading);">
                            File .docx <span class="text-red-500">*</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer rounded-lg px-3 py-2 transition-colors"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-muted);">
                            <span class="material-symbols-outlined text-[16px]">attach_file</span>
                            <span class="text-sm truncate" id="add-file-name">Pilih file .docx...</span>
                            <input type="file" name="template" accept=".docx" class="hidden" required
                                   onchange="document.getElementById('add-file-name').textContent = this.files[0]?.name || 'Pilih file .docx...'">
                        </label>
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Template
                    </button>
                </div>
            </form>
        </div>

        {{-- Cards List Template --}}
        <div class="space-y-3">
            @forelse($templates as $tpl)
                <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 px-5 py-4">
                        {{-- Icon --}}
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                             style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[22px]">description</span>
                        </div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">{{ $tpl['label'] }}</p>
                                @if(!$tpl['is_base'])
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold"
                                          style="background-color: var(--dash-status-info-bg); color: var(--dash-status-info-text);">
                                        CUSTOM
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs mt-0.5 font-mono" style="color: var(--dash-text-muted);">
                                slip_{{ $tpl['type'] }}.docx - {{ $tpl['size'] }} - Diperbarui {{ $tpl['modified'] }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 shrink-0 flex-wrap">
                            {{-- Button Uji Coba: Langsung switch ke Tab Uji Coba dengan type terpilih --}}
                            <button type="button"
                                    @click="switchTestType('{{ $tpl['type'] }}')"
                                    class="inline-flex items-center gap-1.5 rounded-lg px-3.5 py-1.5 text-xs font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[16px]">science</span>
                                Uji Coba
                            </button>

                            {{-- Download file aktif --}}
                            <a href="{{ route('payroll.slip-templates.download', $tpl['type']) }}"
                               class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium transition-colors"
                               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">download</span> Unduh
                            </a>

                            {{-- Ganti Cepat File --}}
                            <label class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium cursor-pointer transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">drive_file_rename_outline</span> Ganti
                                <input type="file" accept=".docx" class="hidden"
                                       onchange="uploadTemplate('{{ $tpl['type'] }}', '{{ route('payroll.slip-templates.upload', $tpl['type']) }}', this)">
                            </label>

                            {{-- Hapus (Hanya custom) --}}
                            @if(!$tpl['is_base'])
                                <button type="button"
                                        @click="deleteActionUrl = '{{ route('payroll.slip-templates.destroy', $tpl['type']) }}'; deleteLabel = '{{ addslashes($tpl['label']) }}'; showDeleteModal = true;"
                                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold text-white transition-opacity hover:opacity-90"
                                        style="background-color: var(--dash-danger);">
                                    <span class="material-symbols-outlined text-[14px]">delete</span> Hapus
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed p-8 text-center text-sm"
                     style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);">
                    Belum ada template slip gaji. Tambahkan template baru di atas.
                </div>
            @endforelse
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         TAB 2: UJI COBA TEMPLATE (TESTING LAB)
    ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'test'" x-transition.opacity.duration.150ms class="space-y-6">
        {{-- Section Subheader & Template Selector --}}
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-5 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                        Uji Coba Template Word (.docx)
                    </h2>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                        Pilih tipe slip, upload dokumen Word untuk menganalisis placeholder, dan lihat hasilnya secara aman.
                    </p>
                </div>

                {{-- Download active template of current type --}}
                <a href="{{ route('payroll.slip-templates.download', $testType) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg px-3.5 py-2 text-xs font-semibold transition-colors shrink-0"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    Unduh Template {{ $testLabel }} Saat Ini
                </a>
            </div>

            {{-- Type Pills Selector --}}
            <div>
                <label class="block text-xs font-semibold mb-2" style="color: var(--dash-text-muted);">
                    PILIH TIPE SLIP YANG INGIN DIUJI:
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach($typeLabels as $tKey => $tLabel)
                        <button type="button"
                                @click="switchTestType('{{ $tKey }}')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all"
                                :style="selectedTestType === '{{ $tKey }}'
                                    ? 'background-color: var(--dash-primary); color: #ffffff;'
                                    : 'border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);'">
                            <span class="material-symbols-outlined text-[14px]"
                                  x-text="selectedTestType === '{{ $tKey }}' ? 'check_circle' : 'radio_button_unchecked'">
                                {{ $testType === $tKey ? 'check_circle' : 'radio_button_unchecked' }}
                            </span>
                            {{ $tLabel }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Data Source Notice --}}
            @if($testSampleInfo)
                <div class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border text-xs font-medium"
                     style="background-color: var(--dash-status-success-bg); border-color: rgba(5, 150, 105, 0.25); color: var(--dash-status-success-text);">
                    <span class="material-symbols-outlined text-[16px] shrink-0">database</span>
                    <span>Data riil database digunakan: <strong>{{ $testSampleInfo }}</strong></span>
                </div>
            @else
                <div class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border text-xs font-medium"
                     style="background-color: var(--dash-status-warning-bg); border-color: rgba(217, 119, 6, 0.25); color: var(--dash-status-warning-text);">
                    <span class="material-symbols-outlined text-[16px] shrink-0">science</span>
                    <span>Belum ada payroll tipe ini di database - pengujian menggunakan contoh data simulasi otomatis.</span>
                </div>
            @endif
        </div>

        {{-- Form Upload Dokumen untuk Diuji --}}
        <form action="{{ route('payroll.slip-templates.test.process', $testType) }}" method="POST"
              enctype="multipart/form-data"
              class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
            @csrf
            <div class="px-6 py-4 rounded-t-2xl flex items-center justify-between"
                 style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">upload_file</span>
                    <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">
                        Upload File Template (.docx) untuk Diuji
                    </h3>
                </div>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold"
                      style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                    Target: {{ $testLabel }}
                </span>
            </div>

            <div class="p-6 space-y-4">
                @error('template')
                    <div class="flex items-center gap-2 px-4 py-3 rounded-xl border text-sm font-medium"
                         style="background-color: var(--dash-status-danger-bg); border-color: rgba(220, 38, 38, 0.25); color: var(--dash-status-danger-text);">
                        <span class="material-symbols-outlined text-[18px]">error</span>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-heading);">
                        Pilih File Word (.docx) <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="template" accept=".docx" required
                           class="dash-file-input"
                           onchange="document.getElementById('test-file-info').textContent = this.files[0] ? 'File siap diuji: ' + this.files[0].name : '';" />
                    <p id="test-file-info" class="text-xs font-semibold mt-1.5" style="color: var(--dash-primary-text);"></p>
                    <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                        File template hanya diproses di memori dan tidak akan mengubah template aktif sebelum Anda menekan tombol terapkan.
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 rounded-b-2xl flex items-center justify-between gap-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <span class="text-xs" style="color: var(--dash-text-muted);">Format: Microsoft Word (.docx)</span>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg px-6 py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">science</span>
                    Proses & Uji Template
                </button>
            </div>
        </form>

        {{-- HASIL PENGUJIAN --}}
        @if($testResult)
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                        Hasil Analisis Pengujian Template: {{ $testLabel }}
                    </h3>
                    @if(isset($testResult['dataSource']) && $testResult['dataSource'])
                        <span class="text-xs font-medium" style="color: var(--dash-status-success-text);">
                            Sumber: {{ $testResult['dataSource'] }}
                        </span>
                    @endif
                </div>

                {{-- Error State --}}
                @if($testResult['error'])
                    <div class="rounded-2xl border p-5 space-y-3 shadow-sm"
                         style="background-color: var(--dash-status-danger-bg); border-color: rgba(220, 38, 38, 0.25);">
                        <div class="flex items-center gap-2 font-bold text-sm" style="color: var(--dash-status-danger-text);">
                            <span class="material-symbols-outlined text-[20px]">error</span>
                            Error Saat Memproses Dokumen Word (.docx)
                        </div>
                        <div class="p-3 rounded-lg font-mono text-xs overflow-x-auto"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
                            {{ $testResult['error'] }}
                        </div>
                        <p class="text-xs leading-relaxed" style="color: var(--dash-status-danger-text);">
                            XML dalam dokumen Word rusak atau terpotong. Pastikan Anda mengetik tag variabel sebagai teks polos tanpa AutoCorrect.
                        </p>
                    </div>
                @else
                    {{-- KPI Stat Badges --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-2xl border p-5 text-center shadow-sm"
                             style="background-color: var(--dash-card-bg); border-color: rgba(5, 150, 105, 0.35);">
                            <div class="text-3xl font-bold font-jakarta" style="color: var(--dash-status-success-text);">
                                {{ count($testResult['foundVars']) }}
                            </div>
                            <div class="text-xs font-semibold mt-1" style="color: var(--dash-text-heading);">
                                Variabel Ditemukan
                            </div>
                            <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Cocok dengan sistem</p>
                        </div>

                        <div class="rounded-2xl border p-5 text-center shadow-sm"
                             style="background-color: var(--dash-card-bg); border-color: {{ count($testResult['missingVars']) ? 'rgba(217, 119, 6, 0.35)' : 'var(--dash-card-border)' }};">
                            <div class="text-3xl font-bold font-jakarta"
                                 style="color: {{ count($testResult['missingVars']) ? 'var(--dash-status-warning-text)' : 'var(--dash-text-faint)' }};">
                                {{ count($testResult['missingVars']) }}
                            </div>
                            <div class="text-xs font-semibold mt-1" style="color: var(--dash-text-heading);">
                                Variabel Belum Ada
                            </div>
                            <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Didukung tapi tidak dipakai</p>
                        </div>

                        <div class="rounded-2xl border p-5 text-center shadow-sm"
                             style="background-color: var(--dash-card-bg); border-color: {{ count($testResult['extraVars']) ? 'rgba(0, 170, 204, 0.35)' : 'var(--dash-card-border)' }};">
                            <div class="text-3xl font-bold font-jakarta"
                                 style="color: {{ count($testResult['extraVars']) ? 'var(--dash-status-info-text)' : 'var(--dash-text-faint)' }};">
                                {{ count($testResult['extraVars']) }}
                            </div>
                            <div class="text-xs font-semibold mt-1" style="color: var(--dash-text-heading);">
                                Variabel Tidak Dikenal
                            </div>
                            <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Tidak akan diisi data</p>
                        </div>
                    </div>

                    {{-- Action Card: Download Preview & Terapkan Template --}}
                    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-5">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-status-success-text);">verified</span>
                                <h4 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                                    Hasil Pengujian Siap Diverifikasi
                                </h4>
                            </div>
                            <p class="text-xs leading-relaxed" style="color: var(--dash-text-muted);">
                                Unduh hasil untuk memeriksa format dan tata letak di Word. Jika sudah sesuai, Anda bisa langsung menerapkannya sebagai template aktif.
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 shrink-0">
                            @if($testResult['resultToken'])
                                <a href="{{ route('payroll.slip-templates.test.download', ['type' => $testType, 'token' => $testResult['resultToken']]) }}"
                                   class="inline-flex items-center justify-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[18px]">download</span>
                                    Unduh Hasil Uji (.docx)
                                </a>
                            @endif

                            @if(isset($testResult['rawToken']) && $testResult['rawToken'])
                                <button type="button"
                                        @click="applyRawToken = '{{ $testResult['rawToken'] }}'; applyLabel = '{{ addslashes($testLabel) }}'; applyActionUrl = '{{ route('payroll.slip-templates.apply-tested', $testType) }}'; showApplyModal = true;"
                                        class="inline-flex items-center justify-center gap-1.5 rounded-lg px-5 py-2 text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[18px]">task_alt</span>
                                    Terapkan Sebagai Template Aktif
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Warning Missing Variables --}}
                    @if(count($testResult['missingVars']))
                        <div class="rounded-2xl border p-5 space-y-2.5 shadow-sm"
                             style="background-color: var(--dash-status-warning-bg); border-color: rgba(217, 119, 6, 0.25);">
                            <div class="flex items-center gap-2 font-bold text-sm" style="color: var(--dash-status-warning-text);">
                                <span class="material-symbols-outlined text-[18px]">warning</span>
                                <span>Variabel yang Belum Digunakan di File Ini ({{ count($testResult['missingVars']) }})</span>
                            </div>
                            <p class="text-xs leading-relaxed" style="color: var(--dash-status-warning-text);">
                                Variabel ini didukung oleh tipe slip {{ $testLabel }}, tetapi tidak ditemukan di file template Anda. Jika memang tidak dibutuhkan pada layout slip Anda, Anda dapat mengabaikannya.
                            </p>
                            <div class="flex flex-wrap gap-2 pt-1">
                                @foreach($testResult['missingVars'] as $var)
                                    <button type="button"
                                            @click="copyToClipboard('${{ '{' . $var . '}' }}')"
                                            title="Klik untuk menyalin"
                                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-mono font-semibold transition-transform active:scale-95"
                                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-warning-text);">
                                        <span>${{ '{' }}{{ $var }}{{ '}' }}</span>
                                        <span class="material-symbols-outlined text-[12px] opacity-60">content_copy</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Extra Variables Info --}}
                    @if(count($testResult['extraVars']))
                        <div class="rounded-2xl border p-5 space-y-2.5 shadow-sm"
                             style="background-color: var(--dash-status-info-bg); border-color: rgba(0, 170, 204, 0.25);">
                            <div class="flex items-center gap-2 font-bold text-sm" style="color: var(--dash-status-info-text);">
                                <span class="material-symbols-outlined text-[18px]">help</span>
                                <span>Variabel di Dokumen yang Tidak Dikenal Sistem ({{ count($testResult['extraVars']) }})</span>
                            </div>
                            <p class="text-xs leading-relaxed" style="color: var(--dash-status-info-text);">
                                Variabel ini ada di file .docx Anda tetapi tidak dikenali oleh sistem payroll dan tidak akan terisi nilai otomatis:
                            </p>
                            <div class="flex flex-wrap gap-2 pt-1">
                                @foreach($testResult['extraVars'] as $var)
                                    <span class="rounded-lg px-2.5 py-1 text-xs font-mono font-semibold"
                                          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-info-text);">
                                        ${{ '{' }}{{ $var }}{{ '}' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Found Variables List --}}
                    @if(count($testResult['foundVars']))
                        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
                            <div class="px-5 py-3 rounded-t-2xl flex items-center justify-between"
                                 style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-status-success-text);">check_circle</span>
                                    <h4 class="text-sm font-bold" style="color: var(--dash-text-heading);">
                                        Variabel Cocok Terisi ({{ count($testResult['foundVars']) }})
                                    </h4>
                                </div>
                                <span class="text-xs" style="color: var(--dash-text-muted);">Klik variabel untuk menyalin</span>
                            </div>
                            <div class="p-5 flex flex-wrap gap-2">
                                @foreach($testResult['foundVars'] as $var)
                                    <button type="button"
                                            @click="copyToClipboard('${{ '{' . $var . '}' }}')"
                                            title="Klik untuk menyalin"
                                            class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-mono font-semibold transition-transform active:scale-95"
                                            style="background-color: var(--dash-status-success-bg); border: 1px solid rgba(5, 150, 105, 0.25); color: var(--dash-status-success-text);">
                                        <span>${{ '{' }}{{ $var }}{{ '}' }}</span>
                                        <span class="material-symbols-outlined text-[12px] opacity-60">content_copy</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        @endif

        {{-- Collapsible Variable Dictionary Guide --}}
        <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
            <button type="button"
                    @click="showVarGuide = !showVarGuide"
                    class="w-full px-6 py-4 rounded-2xl flex items-center justify-between text-left transition-colors"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">menu_book</span>
                    <div>
                        <h4 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">
                            Kamus Variabel Template: {{ $testLabel }} ({{ count($allKnownVars[$testType] ?? []) }} Variabel)
                        </h4>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                            Klik untuk membuka atau menyembunyikan daftar placeholder yang didukung
                        </p>
                    </div>
                </div>
                <span class="material-symbols-outlined text-[20px] transition-transform duration-200"
                      :class="showVarGuide ? 'rotate-180' : ''"
                      style="color: var(--dash-text-muted);">expand_more</span>
            </button>

            <div x-show="showVarGuide" x-transition.opacity.duration.200ms
                 class="p-6 pt-0 space-y-6 border-t" style="border-color: var(--dash-card-border);">
                @php
                    $currentKnownVars = $allKnownVars[$testType] ?? [];
                    $varDict = [
                        'nama_pegawai' => 'Nama lengkap pegawai',
                        'kode_pegawai' => 'Nomor ID / kode unik pegawai',
                        'posisi_pegawai' => 'Nama posisi atau jabatan saat ini',
                        'role_pegawai' => 'Role akses akun pegawai',
                        'periode_gaji' => 'Bulan dan tahun periode (contoh: Oktober 2026)',
                        'nama_hari' => 'Nama hari saat generate slip (contoh: Rabu)',
                        'tanggal_bulan_tahun' => 'Tanggal lengkap pencetakan (contoh: 08 Oktober 2026)',
                        'net_salary' => 'Gaji bersih final yang dibayarkan (Take Home Pay)',
                        'total_nominal_seluruh_komponen_pendapatan' => 'Total penerimaan kotor sebelum potongan',
                        'total_nominal_seluruh_komponen_pengurangan' => 'Total seluruh potongan gaji',
                        'nama_komponen_pendapatan' => 'Nama komponen penambah gaji (trigger clone baris)',
                        'nominal_komponen_pendapatan' => 'Nominal rupiah komponen penambah',
                        'nama_komponen_pengurangan' => 'Nama komponen pengurang/potongan',
                        'nominal_komponen_pengurangan' => 'Nominal rupiah komponen pengurang',
                        'omzet' => 'Omzet kotor yang dikumpulkan',
                        'ex_adm' => 'Omzet bersih setelah dipotong administrasi',
                        'percentage' => 'Persentase reward bonus (misal: 10%)',
                        'net_bonus' => 'Total bonus yang diterima',
                        'total_meeting_active_program_attended_this_month' => 'Jumlah sesi mengajar yang dihadiri bulan ini',
                        'monthly_meetings' => 'Target/basis sesi pertemuan bulanan',
                        'main_salary_sesi' => 'Tarif basis kompensasi sesi',
                        'gross_salary_sesi' => 'Total nominal pendapatan dari sesi',
                        'days_attended_this_month' => 'Jumlah hari kerja hadir bulan ini (Official Tutor)',
                        'workdays' => 'Jumlah jadwal hari kerja bulan ini (Official Tutor)',
                        'main_salary_work_schedule' => 'Gaji pokok basis jadwal harian (Official Tutor)',
                        'gross_salary_work_schedule' => 'Total nominal pendapatan jadwal harian (Official Tutor)',
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs pt-4">
                    @foreach($currentKnownVars as $vKey)
                        <div class="flex items-center justify-between p-2.5 rounded-lg transition-colors"
                             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                            <button type="button" @click="copyToClipboard('${{ '{' . $vKey . '}' }}')"
                                    title="Klik untuk menyalin"
                                    class="font-mono font-semibold text-left transition-opacity hover:opacity-80"
                                    style="color: var(--dash-primary);">
                                ${{ '{' }}{{ $vKey }}{{ '}' }}
                            </button>
                            <span class="text-[11px] truncate max-w-[50%]" style="color: var(--dash-text-muted);"
                                  title="{{ $varDict[$vKey] ?? '' }}">
                                {{ $varDict[$vKey] ?? 'Variabel sistem' }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="p-3.5 rounded-xl text-xs leading-relaxed"
                     style="background-color: var(--dash-nav-hover-bg); border-left: 3px solid var(--dash-primary); color: var(--dash-text-body);">
                    <strong>Panduan Tabel Word:</strong> Letakkan <code>${'{nama_komponen_pendapatan}'}</code> dan <code>${'{nama_komponen_pengurangan}'}</code> pada satu baris tabel di Word. Sistem akan menggandakan (clone row) baris tersebut otomatis sesuai jumlah komponen slip gaji.
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         TAB 3: ASSIGNMENT POSISI
    ════════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'assignments'" x-transition.opacity.duration.150ms class="space-y-4">
        <div>
            <h2 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                Assignment Template per Posisi ({{ count($positions) }})
            </h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                <strong>Auto-detect:</strong> Jika tidak ditentukan secara spesifik, Official Tutor dan Regular Tutor otomatis diarahkan ke template tutor, posisi lainnya ke template marketing.
            </p>
        </div>

        <form action="{{ route('payroll.slip-templates.assign.update') }}" method="POST"
              class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
            @csrf
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Posisi</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Template Khusus (Override)</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider hidden sm:table-cell" style="color: var(--dash-text-muted);">Auto-detect Default</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($positions as $position)
                        @php
                            $autoType = match(true) {
                                str_contains(strtolower($position->position_name), 'official tutor') => 'official_tutor',
                                str_contains(strtolower($position->position_name), 'regular tutor')  => 'regular_tutor',
                                default => 'marketing',
                            };
                            $current = $position->slip_template_type ?? '';
                        @endphp
                        <tr class="transition-colors"
                            style="border-bottom: 1px solid var(--dash-card-border); {{ $current ? 'background-color: var(--dash-primary-bg);' : '' }}"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='{{ $current ? 'var(--dash-primary-bg)' : 'transparent' }}'">
                            <td class="px-5 py-3 font-medium" style="color: var(--dash-text-heading);">
                                {{ $position->position_name }}
                                @if($current)
                                    <span class="ml-1 text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded"
                                          style="background-color: var(--dash-primary); color: #ffffff;">override</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <select name="assignments[{{ $position->id }}]"
                                        class="rounded-lg text-sm px-3 py-1.5 transition-colors"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); outline: none;"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <option value="" {{ !$current ? 'selected' : '' }}>Pilih Auto-detect</option>
                                    @foreach($typeLabels as $type => $label)
                                        <option value="{{ $type }}" {{ $current === $type ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-5 py-3 text-xs hidden sm:table-cell" style="color: var(--dash-text-muted);">
                                {{ $typeLabels[$autoType] ?? $autoType }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3.5 flex justify-end"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg px-5 py-2 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span> Simpan Assignment
                </button>
            </div>
        </form>
    </div>

    {{-- Toast feedback copy variabel --}}
    <div x-show="copiedVar" x-transition.opacity.duration.200ms
         class="fixed bottom-6 right-6 z-50 flex items-center gap-2 rounded-xl px-4 py-3 shadow-xl text-xs font-semibold text-white"
         style="background-color: var(--dash-primary); display: none;">
        <span class="material-symbols-outlined text-[16px]">content_copy</span>
        <span>Variabel disalin ke clipboard!</span>
    </div>

    {{-- Modal Konfirmasi Terapkan Template --}}
    <div x-show="showApplyModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showApplyModal" @click="showApplyModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="showApplyModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[24px]">task_alt</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">
                                Terapkan Template Aktif
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                    Apakah Anda yakin ingin menerapkan file yang telah diuji ini sebagai template aktif untuk <strong style="color: var(--dash-text-heading);" x-text="applyLabel"></strong>?
                                </p>
                                <p class="text-xs mt-2" style="color: var(--dash-text-muted);">
                                    Template aktif yang saat ini berjalan di sistem akan digantikan dengan file ini.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showApplyModal = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <form :action="applyActionUrl" method="POST">
                        @csrf
                        <input type="hidden" name="raw_token" :value="applyRawToken">
                        <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                            Ya, Terapkan Template
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus Template --}}
    <div x-show="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showDeleteModal" @click="showDeleteModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="showDeleteModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: var(--dash-status-danger-bg); color: var(--dash-danger);">
                            <span class="material-symbols-outlined text-[24px]">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Hapus Template Slip</h3>
                            <div class="mt-2">
                                <p class="text-sm" style="color: var(--dash-text-muted);">
                                    Hapus template <strong style="color: var(--dash-text-heading);" x-text="deleteLabel"></strong>? Assignment posisi ke template ini akan direset.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showDeleteModal = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="button" @click="$refs.deleteForm.submit()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-danger);">
                        Hapus Template
                    </button>
                </div>
                <form x-ref="deleteForm" :action="deleteActionUrl" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Upload ganti template via hidden form --}}
<form id="upload-form" method="POST" enctype="multipart/form-data" style="display:none">
    @csrf
    <input type="file" id="upload-input" name="template" accept=".docx">
</form>

<script>
function uploadTemplate(type, url, input) {
    if (!input.files[0]) return;
    const form = document.getElementById('upload-form');
    form.action = url;
    const dt = new DataTransfer();
    dt.items.add(input.files[0]);
    document.getElementById('upload-input').files = dt.files;
    form.submit();
}
</script>
@endsection
