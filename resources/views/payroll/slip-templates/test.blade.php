@extends('layouts.dashboard')

@section('title', 'Tes Template Slip - ' . $label)
@section('header_title', 'Template Slip Gaji')

@section('content')
<div class="max-w-4xl mx-auto space-y-6"
     x-data="{
         showApplyModal: false,
         copiedVar: null,
         copyToClipboard(text) {
             navigator.clipboard.writeText(text);
             this.copiedVar = text;
             setTimeout(() => { if (this.copiedVar === text) this.copiedVar = null; }, 2000);
         }
     }">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('payroll.slip-templates.index') }}" class="dash-back mb-2">
                <span class="material-symbols-outlined">arrow_back</span>
                Kembali ke Template Slip Gaji
            </a>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Gaji & Bonus</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Uji Coba Template: {{ $label }}</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Validasi placeholder dan preview hasil render dokumen .docx sebelum dijadikan template aktif di sistem.
            </p>
        </div>

        {{-- Top Actions: Switch Type & Download Active Template --}}
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            @if(isset($allTypes) && count($allTypes) > 1)
                <div class="flex items-center gap-1.5 rounded-lg px-2.5 py-1.5"
                     style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);">
                    <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-muted);">tune</span>
                    <label for="type-switcher" class="text-xs font-semibold" style="color: var(--dash-text-muted);">Tipe:</label>
                    <select id="type-switcher"
                            class="bg-transparent text-xs font-semibold focus:outline-none cursor-pointer"
                            style="color: var(--dash-text-heading);"
                            onchange="window.location.href = '{{ url('payroll-slip-templates') }}/' + this.value + '/test'">
                        @foreach($allTypes as $tKey => $tLabel)
                            <option value="{{ $tKey }}" {{ $tKey === $type ? 'selected' : '' }}>{{ $tLabel }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <a href="{{ route('payroll.slip-templates.download', $type) }}"
               class="inline-flex items-center gap-1.5 rounded-lg px-3.5 py-2 text-xs font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">download</span>
                Unduh Template Aktif
            </a>
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

    {{-- Data Source Banner --}}
    @if($sampleInfo)
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: var(--dash-status-success-bg); border-color: rgba(5, 150, 105, 0.25); color: var(--dash-status-success-text);">
            <span class="material-symbols-outlined text-[18px] shrink-0">database</span>
            <div>
                <span class="font-bold">Data nyata dari database digunakan:</span>
                <span class="ml-1">{{ $sampleInfo }}</span>
            </div>
        </div>
    @else
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: var(--dash-status-warning-bg); border-color: rgba(217, 119, 6, 0.25); color: var(--dash-status-warning-text);">
            <span class="material-symbols-outlined text-[18px] shrink-0">science</span>
            <div>
                <span class="font-bold">Simulasi data contoh:</span>
                <span class="ml-1">Belum ada riwayat payroll tipe ini di database - sistem menggunakan contoh data simulasi otomatis.</span>
            </div>
        </div>
    @endif

    {{-- ═══ UPLOAD & TEST FORM CARD ═════════════════════════════════════ --}}
    <form action="{{ route('payroll.slip-templates.test.process', $type) }}" method="POST"
          enctype="multipart/form-data"
          class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        @csrf
        <div class="px-6 py-4 rounded-t-2xl flex items-center justify-between gap-4"
             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">upload_file</span>
                <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                    Upload File Template (.docx) untuk Diuji
                </h2>
            </div>
            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold"
                  style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                {{ $label }}
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
                    Pilih Dokumen Word (.docx) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="file" name="template" id="test-template-input" accept=".docx" required
                           class="dash-file-input"
                           onchange="document.getElementById('file-chosen-notice').textContent = this.files[0] ? 'File terpilih: ' + this.files[0].name : '';" />
                </div>
                <p id="file-chosen-notice" class="text-xs font-semibold mt-1.5" style="color: var(--dash-primary-text);"></p>
                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                    File hanya akan diuji di memori dan penyimpanan sementara. Template aktif sistem <strong>tidak akan berubah</strong> sebelum Anda memilih untuk menerapkannya.
                </p>
            </div>
        </div>

        <div class="px-6 py-4 rounded-b-2xl flex items-center justify-between gap-4"
             style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <p class="text-xs" style="color: var(--dash-text-muted);">
                Format file wajib .docx (Microsoft Word 2007+)
            </p>
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-lg px-6 py-2.5 text-sm font-semibold text-white transition-opacity hover:opacity-90"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">science</span>
                Proses & Uji Template
            </button>
        </div>
    </form>

    {{-- ═══ HASIL PENGUJIAN (JIKA ADA RESULT) ════════════════════════════ --}}
    @if($result)
        <section class="space-y-4 pt-2">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">
                    Hasil Analisis Pengujian Template
                </h2>
                @if(isset($result['dataSource']) && $result['dataSource'])
                    <span class="text-xs font-medium" style="color: var(--dash-status-success-text);">
                        Sumber: {{ $result['dataSource'] }}
                    </span>
                @endif
            </div>

            {{-- ERROR STATE --}}
            @if($result['error'])
                <div class="rounded-2xl border p-5 space-y-3 shadow-sm"
                     style="background-color: var(--dash-status-danger-bg); border-color: rgba(220, 38, 38, 0.25);">
                    <div class="flex items-center gap-2 font-bold text-sm" style="color: var(--dash-status-danger-text);">
                        <span class="material-symbols-outlined text-[20px]">error</span>
                        Terjadi Kesalahan Saat Membaca Template Word (.docx)
                    </div>
                    <div class="p-3 rounded-lg font-mono text-xs overflow-x-auto"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
                        {{ $result['error'] }}
                    </div>
                    <p class="text-xs leading-relaxed" style="color: var(--dash-status-danger-text);">
                        Kemungkinan XML di dalam dokumen Word terpotong atau rusak. Buka file di Microsoft Word, hapus karakter variabel yang bermasalah, nonaktifkan fitur AutoCorrect/SpellCheck, lalu ketik ulang variabel <code>${'{variabel}'}</code> sebagai plain text.
                    </p>
                </div>
            @else
                {{-- KPI SUMMARY BADGES --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- 1: Ditemukan --}}
                    <div class="rounded-2xl border p-5 text-center shadow-sm"
                         style="background-color: var(--dash-card-bg); border-color: rgba(5, 150, 105, 0.35);">
                        <div class="text-3xl font-bold font-jakarta" style="color: var(--dash-status-success-text);">
                            {{ count($result['foundVars']) }}
                        </div>
                        <div class="text-xs font-semibold mt-1" style="color: var(--dash-text-heading);">
                            Variabel Ditemukan
                        </div>
                        <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Cocok dengan sistem payroll</p>
                    </div>

                    {{-- 2: Tidak Ditemukan --}}
                    <div class="rounded-2xl border p-5 text-center shadow-sm"
                         style="background-color: var(--dash-card-bg); border-color: {{ count($result['missingVars']) ? 'rgba(217, 119, 6, 0.35)' : 'var(--dash-card-border)' }};">
                        <div class="text-3xl font-bold font-jakarta"
                             style="color: {{ count($result['missingVars']) ? 'var(--dash-status-warning-text)' : 'var(--dash-text-faint)' }};">
                            {{ count($result['missingVars']) }}
                        </div>
                        <div class="text-xs font-semibold mt-1" style="color: var(--dash-text-heading);">
                            Variabel Belum Ada
                        </div>
                        <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Didukung tapi tidak dipakai</p>
                    </div>

                    {{-- 3: Ekstra / Tidak Dikenal --}}
                    <div class="rounded-2xl border p-5 text-center shadow-sm"
                         style="background-color: var(--dash-card-bg); border-color: {{ count($result['extraVars']) ? 'rgba(0, 170, 204, 0.35)' : 'var(--dash-card-border)' }};">
                        <div class="text-3xl font-bold font-jakarta"
                             style="color: {{ count($result['extraVars']) ? 'var(--dash-status-info-text)' : 'var(--dash-text-faint)' }};">
                            {{ count($result['extraVars']) }}
                        </div>
                        <div class="text-xs font-semibold mt-1" style="color: var(--dash-text-heading);">
                            Variabel Tidak Dikenal
                        </div>
                        <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">Ada di template tapi tidak diisi</p>
                    </div>
                </div>

                {{-- ACTION CARD: DOWNLOAD PREVIEW & TERAPKAN SEBAGAI TEMPLATE --}}
                <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-5">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-status-success-text);">verified</span>
                            <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                                Dokumen Uji Siap Diverifikasi
                            </h3>
                        </div>
                        <p class="text-xs leading-relaxed" style="color: var(--dash-text-muted);">
                            Unduh hasil untuk cek kerapian tata letak tabel dan format font di Word. Jika sudah sesuai, langsung terapkan sebagai template aktif.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 shrink-0">
                        @if($result['resultToken'])
                            <a href="{{ route('payroll.slip-templates.test.download', ['type' => $type, 'token' => $result['resultToken']]) }}"
                               class="inline-flex items-center justify-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold transition-colors"
                               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[18px]">download</span>
                                Unduh Hasil Uji (.docx)
                            </a>
                        @endif

                        @if(isset($result['rawToken']) && $result['rawToken'])
                            <button type="button" @click="showApplyModal = true;"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg px-5 py-2 text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
                                    style="background-color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[18px]">task_alt</span>
                                Terapkan Sebagai Template Aktif
                            </button>
                        @endif
                    </div>
                </div>

                {{-- WARNING: MISSING VARIABLES --}}
                @if(count($result['missingVars']))
                    <div class="rounded-2xl border p-5 space-y-3 shadow-sm"
                         style="background-color: var(--dash-status-warning-bg); border-color: rgba(217, 119, 6, 0.25);">
                        <div class="flex items-center gap-2 font-bold text-sm" style="color: var(--dash-status-warning-text);">
                            <span class="material-symbols-outlined text-[18px]">warning</span>
                            <span>Variabel Didukung yang Belum Digunakan di Template Ini ({{ count($result['missingVars']) }})</span>
                        </div>
                        <p class="text-xs leading-relaxed" style="color: var(--dash-status-warning-text);">
                            Variabel berikut didukung oleh tipe slip {{ $label }}, namun tidak ditemukan di file template yang Anda unggah. Jika Anda memang sengaja tidak menampilkannya, hal ini tidak menimbulkan error.
                        </p>
                        <div class="flex flex-wrap gap-2 pt-1">
                            @foreach($result['missingVars'] as $var)
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

                {{-- INFO: EXTRA VARIABLES --}}
                @if(count($result['extraVars']))
                    <div class="rounded-2xl border p-5 space-y-3 shadow-sm"
                         style="background-color: var(--dash-status-info-bg); border-color: rgba(0, 170, 204, 0.25);">
                        <div class="flex items-center gap-2 font-bold text-sm" style="color: var(--dash-status-info-text);">
                            <span class="material-symbols-outlined text-[18px]">help</span>
                            <span>Variabel di Template yang Tidak Dikenal Sistem ({{ count($result['extraVars']) }})</span>
                        </div>
                        <p class="text-xs leading-relaxed" style="color: var(--dash-status-info-text);">
                            Variabel berikut ditemukan dalam dokumen Word, tetapi tidak ada dalam kamus data tipe slip ini sehingga dibiarkan apa adanya (tidak terisi):
                        </p>
                        <div class="flex flex-wrap gap-2 pt-1">
                            @foreach($result['extraVars'] as $var)
                                <span class="rounded-lg px-2.5 py-1 text-xs font-mono font-semibold"
                                      style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-info-text);">
                                    ${{ '{' }}{{ $var }}{{ '}' }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- SUCCESS: FOUND VARIABLES --}}
                @if(count($result['foundVars']))
                    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
                        <div class="px-5 py-3 rounded-t-2xl flex items-center justify-between"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-status-success-text);">check_circle</span>
                                <h3 class="text-sm font-bold" style="color: var(--dash-text-heading);">
                                    Variabel yang Terisi Sukses ({{ count($result['foundVars']) }})
                                </h3>
                            </div>
                            <span class="text-xs" style="color: var(--dash-text-muted);">Klik variabel untuk menyalin</span>
                        </div>
                        <div class="p-5 flex flex-wrap gap-2">
                            @foreach($result['foundVars'] as $var)
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
        </section>
    @endif

    {{-- ═══ PANDUAN & KAMUS VARIABEL YANG DIDUKUNG ═══════════════════════ --}}
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="px-6 py-4 rounded-t-2xl flex items-center justify-between"
             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">menu_book</span>
                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                    Kamus Variabel Template: {{ $label }} ({{ count($knownVars) }} Variabel)
                </h3>
            </div>
            <span class="text-xs" style="color: var(--dash-text-muted);">Klik tag untuk menyalin</span>
        </div>

        <div class="p-6 space-y-6">
            {{-- Kategori 1: Identitas & Periode --}}
            <div class="space-y-2.5">
                <h4 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-primary-text);">
                    1. Identitas Pegawai & Tanggal Periode
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    @php
                        $identVars = [
                            'nama_pegawai' => 'Nama lengkap pegawai',
                            'kode_pegawai' => 'Nomor ID / kode unik pegawai',
                            'posisi_pegawai' => 'Nama posisi atau jabatan saat ini',
                            'role_pegawai' => 'Role akses akun pegawai',
                            'periode_gaji' => 'Bulan dan tahun periode (contoh: Oktober 2026)',
                            'nama_hari' => 'Nama hari saat generate slip (contoh: Rabu)',
                            'tanggal_bulan_tahun' => 'Tanggal lengkap pencetakan (contoh: 08 Oktober 2026)',
                        ];
                    @endphp
                    @foreach($identVars as $vKey => $vDesc)
                        @if(in_array($vKey, $knownVars))
                            <div class="flex items-center justify-between p-2 rounded-lg transition-colors"
                                 style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                <button type="button" @click="copyToClipboard('${{ '{' . $vKey . '}' }}')"
                                        class="font-mono font-semibold text-left transition-opacity hover:opacity-80"
                                        style="color: var(--dash-primary);">
                                    ${{ '{' }}{{ $vKey }}{{ '}' }}
                                </button>
                                <span class="text-[11px]" style="color: var(--dash-text-muted);">{{ $vDesc }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Kategori 2: Ringkasan Total Finansial --}}
            <div class="space-y-2.5">
                <h4 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-primary-text);">
                    2. Ringkasan Total Finansial (Take Home Pay)
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    @php
                        $finVars = [
                            'net_salary' => 'Gaji bersih final yang dibayarkan',
                            'total_nominal_seluruh_komponen_pendapatan' => 'Total penerimaan kotor sebelum potongan',
                            'total_nominal_seluruh_komponen_pengurangan' => 'Total seluruh potongan gaji',
                        ];
                    @endphp
                    @foreach($finVars as $vKey => $vDesc)
                        @if(in_array($vKey, $knownVars))
                            <div class="flex items-center justify-between p-2 rounded-lg transition-colors"
                                 style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                <button type="button" @click="copyToClipboard('${{ '{' . $vKey . '}' }}')"
                                        class="font-mono font-semibold text-left transition-opacity hover:opacity-80"
                                        style="color: var(--dash-primary);">
                                    ${{ '{' }}{{ $vKey }}{{ '}' }}
                                </button>
                                <span class="text-[11px]" style="color: var(--dash-text-muted);">{{ $vDesc }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Kategori 3: Tabel Komponen Dinamis --}}
            <div class="space-y-2.5">
                <h4 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-primary-text);">
                    3. Komponen Gaji Dinamis (Baris Tabel Otomatis Berulang)
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    @php
                        $compVars = [
                            'nama_komponen_pendapatan' => 'Nama komponen penambah gaji (trigger clone baris)',
                            'nominal_komponen_pendapatan' => 'Nominal rupiah komponen penambah',
                            'nama_komponen_pengurangan' => 'Nama komponen pengurang/potongan',
                            'nominal_komponen_pengurangan' => 'Nominal rupiah komponen pengurang',
                        ];
                    @endphp
                    @foreach($compVars as $vKey => $vDesc)
                        @if(in_array($vKey, $knownVars))
                            <div class="flex items-center justify-between p-2 rounded-lg transition-colors"
                                 style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                <button type="button" @click="copyToClipboard('${{ '{' . $vKey . '}' }}')"
                                        class="font-mono font-semibold text-left transition-opacity hover:opacity-80"
                                        style="color: var(--dash-primary);">
                                    ${{ '{' }}{{ $vKey }}{{ '}' }}
                                </button>
                                <span class="text-[11px]" style="color: var(--dash-text-muted);">{{ $vDesc }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="p-3 rounded-lg text-xs leading-relaxed"
                     style="background-color: var(--dash-nav-hover-bg); border-left: 3px solid var(--dash-primary); color: var(--dash-text-body);">
                    <strong>Panduan Tabel Word:</strong> Tuliskan variabel <code>${'{nama_komponen_pendapatan}'}</code> dan <code>${'{nama_komponen_pengurangan}'}</code> pada satu baris tabel di Word. Sistem secara cerdas akan menggandakan (clone) baris tersebut ke bawah sesuai banyaknya komponen gaji pegawai secara otomatis.
                </div>
            </div>

            {{-- Kategori 4: Omzet, Bonus & Data Khusus --}}
            @php
                $specialVars = [
                    'omzet' => 'Omzet kotor yang dikumpulkan',
                    'ex_adm' => 'Omzet bersih setelah dipotong administrasi',
                    'percentage' => 'Persentase reward bonus (misal: 10%)',
                    'net_bonus' => 'Total bonus yang diterima',
                    'total_meeting_active_program_attended_this_month' => 'Jumlah sesi mengajar yang dihadiri bulan ini',
                    'monthly_meetings' => 'Target/basis sesi pertemuan bulanan',
                    'main_salary_sesi' => 'Tarif basis kompensasi sesi',
                    'gross_salary_sesi' => 'Total nominal pendapatan dari sesi',
                    'days_attended_this_month' => 'Jumlah hari kerja hadir bulan ini',
                    'workdays' => 'Jumlah jadwal hari kerja bulan ini',
                    'main_salary_work_schedule' => 'Gaji pokok basis jadwal harian',
                    'gross_salary_work_schedule' => 'Total nominal pendapatan jadwal harian',
                ];
            @endphp
            @if(collect(array_keys($specialVars))->filter(fn($k) => in_array($k, $knownVars))->isNotEmpty())
                <div class="space-y-2.5">
                    <h4 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-primary-text);">
                        4. Data Bonus, Sesi Mengajar & Jadwal Khusus
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        @foreach($specialVars as $vKey => $vDesc)
                            @if(in_array($vKey, $knownVars))
                                <div class="flex items-center justify-between p-2 rounded-lg transition-colors"
                                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                    <button type="button" @click="copyToClipboard('${{ '{' . $vKey . '}' }}')"
                                            class="font-mono font-semibold text-left transition-opacity hover:opacity-80"
                                            style="color: var(--dash-primary);">
                                        ${{ '{' }}{{ $vKey }}{{ '}' }}
                                    </button>
                                    <span class="text-[11px]" style="color: var(--dash-text-muted);">{{ $vDesc }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Tips Tambahan --}}
            <div class="rounded-xl border p-4 space-y-1.5"
                 style="background-color: var(--dash-input-bg); border-color: var(--dash-input-border);">
                <div class="flex items-center gap-1.5 font-bold text-xs" style="color: var(--dash-text-heading);">
                    <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-primary);">lightbulb</span>
                    <span>Tips Penyusunan Template Word (.docx)</span>
                </div>
                <ul class="text-xs space-y-1 list-disc list-inside leading-relaxed" style="color: var(--dash-text-muted);">
                    <li>Gunakan penulisan persis dengan tanda dollar dan kurung kurawal tanpa spasi di dalamnya, contoh: <code>${'{nama_pegawai}'}</code>.</li>
                    <li>Pastikan tidak ada format font yang berbeda di tengah-tengah teks variabel (misal separuh bold atau ganti warna) karena akan memecah node XML di Word.</li>
                    <li>Jika variabel tidak terdeteksi saat uji coba, coba copy-paste variabel dari daftar di atas sebagai teks biasa (Keep Text Only).</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Toast feedback copy variabel --}}
    <div x-show="copiedVar" x-transition.opacity.duration.200ms
         class="fixed bottom-6 right-6 z-50 flex items-center gap-2 rounded-xl px-4 py-3 shadow-xl text-xs font-semibold text-white"
         style="background-color: var(--dash-primary); display: none;">
        <span class="material-symbols-outlined text-[16px]">content_copy</span>
        <span>Variabel disalin ke clipboard!</span>
    </div>

    {{-- Modal Konfirmasi Terapkan Template --}}
    @if($result && isset($result['rawToken']) && $result['rawToken'])
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
                                        Apakah Anda yakin ingin menerapkan file ini sebagai template aktif untuk <strong style="color: var(--dash-text-heading);">{{ $label }}</strong>?
                                    </p>
                                    <p class="text-xs mt-2" style="color: var(--dash-text-muted);">
                                        Template aktif sebelumnya akan digantikan dengan file yang telah Anda uji ini.
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
                        <form action="{{ route('payroll.slip-templates.apply-tested', $type) }}" method="POST">
                            @csrf
                            <input type="hidden" name="raw_token" value="{{ $result['rawToken'] }}">
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
    @endif
</div>
@endsection
