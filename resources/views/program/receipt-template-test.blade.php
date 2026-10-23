@extends('layouts.dashboard')

@section('title', 'Tes Template Kwitansi - MINDSIA')
@section('header_title', 'Tes Template Kwitansi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('programs.index') }}"
               class="dash-back flex items-center justify-center w-9 h-9 rounded-lg transition-colors cursor-pointer shrink-0"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
               title="Kembali ke Daftar Program">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5"
                   style="color: var(--dash-primary-text);">Manajemen Program</p>
                <h1 class="font-jakarta text-xl sm:text-2xl font-bold tracking-tight"
                    style="color: var(--dash-text-heading);">Tes Template Kwitansi</h1>
            </div>
        </div>
        <a href="{{ route('programs.receipt-template.download') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors cursor-pointer self-start sm:self-auto shadow-sm"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-primary);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[16px]">download</span>
            Contoh Template
        </a>
    </div>

    {{-- Data source info --}}
    @if($sampleInfo)
        <div class="flex items-center gap-3 px-4 py-3 rounded-2xl shadow-sm text-sm"
             style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-success-text);">
            <span class="material-symbols-outlined text-[20px] shrink-0">database</span>
            <div>
                <span class="font-bold">Data nyata dari DB akan digunakan:</span>
                {{ $sampleInfo }}
            </div>
        </div>
    @else
        <div class="flex items-center gap-3 px-4 py-3 rounded-2xl shadow-sm text-sm"
             style="background-color: var(--dash-status-warning-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-warning-text);">
            <span class="material-symbols-outlined text-[20px] shrink-0">warning</span>
            Belum ada registrasi member di DB - akan menggunakan data contoh statis.
        </div>
    @endif

    {{-- Upload form --}}
    <form action="{{ route('programs.receipt-template.test.process') }}" method="POST"
          enctype="multipart/form-data"
          class="rounded-2xl shadow-sm overflow-hidden"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @csrf
        <div class="px-6 py-4"
             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Upload Template untuk Diuji</h3>
        </div>
        <div class="p-6 space-y-4">
            @error('template')
                <div class="flex items-center gap-2 px-4 py-3 rounded-xl text-sm"
                     style="background-color: var(--dash-status-danger-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
                    <span class="material-symbols-outlined text-[18px]">error</span> {{ $message }}
                </div>
            @enderror
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">
                    File Template (.docx) <span style="color: var(--dash-status-danger-text);">*</span>
                </label>
                <input type="file" name="template" accept=".docx" required
                       class="w-full px-4 py-2 text-sm rounded-lg file:mr-4 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);" />
                <p class="text-xs mt-1.5" style="color: var(--dash-text-muted);">
                    Template akan diproses dengan data dummy lalu dihapus dari server secara otomatis.
                </p>
            </div>
        </div>
        <div class="px-6 py-4 flex justify-end rounded-b-2xl"
             style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90 shadow-sm cursor-pointer"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">science</span>
                Proses & Tes
            </button>
        </div>
    </form>

    {{-- Result --}}
    @if($result)
        {{-- Data source used --}}
        @if($result['dataSource'])
            <div class="flex items-center gap-2.5 px-4 py-3 rounded-2xl shadow-sm text-sm"
                 style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-success-text);">
                <span class="material-symbols-outlined text-[18px]">database</span>
                <span>Diproses dengan data nyata: <strong>{{ $result['dataSource'] }}</strong></span>
            </div>
        @else
            <div class="flex items-center gap-2.5 px-4 py-3 rounded-2xl shadow-sm text-sm"
                 style="background-color: var(--dash-status-warning-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-warning-text);">
                <span class="material-symbols-outlined text-[18px]">science</span>
                Diproses dengan data contoh statis (tidak ada registrasi di DB).
            </div>
        @endif

        @if($result['error'])
            <div class="rounded-2xl p-6 shadow-sm space-y-2"
                 style="background-color: var(--dash-status-danger-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
                <div class="flex items-center gap-2 font-bold text-base">
                    <span class="material-symbols-outlined text-[20px]">error</span>
                    Error saat memproses template
                </div>
                <p class="text-xs font-mono p-3 rounded-xl"
                   style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
                    {{ $result['error'] }}
                </p>
                <p class="text-xs">Kemungkinan XML di dalam berkas .docx rusak - buka di Word, pastikan tidak ada autocorrect pada karakter <code>${'{'}</code>.</p>
            </div>
        @else
            {{-- Summary badges --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl p-5 text-center shadow-sm"
                     style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border);">
                    <div class="text-3xl font-bold font-jakarta" style="color: var(--dash-status-success-text);">{{ count($result['found']) }}</div>
                    <div class="text-xs font-semibold mt-1" style="color: var(--dash-status-success-text);">Variabel Ditemukan</div>
                </div>
                <div class="rounded-2xl p-5 text-center shadow-sm"
                     style="background-color: {{ count($result['missing']) ? 'var(--dash-status-warning-bg)' : 'var(--dash-card-bg)' }}; border: 1px solid var(--dash-card-border);">
                    <div class="text-3xl font-bold font-jakarta" style="color: {{ count($result['missing']) ? 'var(--dash-status-warning-text)' : 'var(--dash-text-faint)' }};">{{ count($result['missing']) }}</div>
                    <div class="text-xs font-semibold mt-1" style="color: {{ count($result['missing']) ? 'var(--dash-status-warning-text)' : 'var(--dash-text-muted)' }};">Variabel Tidak Ditemukan</div>
                </div>
                <div class="rounded-2xl p-5 text-center shadow-sm"
                     style="background-color: {{ count($result['extra']) ? 'var(--dash-status-info-bg)' : 'var(--dash-card-bg)' }}; border: 1px solid var(--dash-card-border);">
                    <div class="text-3xl font-bold font-jakarta" style="color: {{ count($result['extra']) ? 'var(--dash-status-info-text)' : 'var(--dash-text-faint)' }};">{{ count($result['extra']) }}</div>
                    <div class="text-xs font-semibold mt-1" style="color: {{ count($result['extra']) ? 'var(--dash-status-info-text)' : 'var(--dash-text-muted)' }};">Variabel Tidak Dikenal</div>
                </div>
            </div>

            {{-- Download --}}
            @if($result['resultToken'])
                <div class="rounded-2xl px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div>
                        <p class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Hasil Pengujian Siap</p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Download dan buka di Microsoft Word untuk memeriksa tampilan dan variabel yang tergantikan.</p>
                    </div>
                    <a href="{{ route('programs.receipt-template.test.download', ['token' => $result['resultToken']]) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-90 shadow-sm shrink-0"
                       style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">download</span>
                        Download Hasil
                    </a>
                </div>
            @endif

            {{-- Missing vars warning --}}
            @if(count($result['missing']))
                <div class="rounded-2xl p-5 space-y-3 shadow-sm"
                     style="background-color: var(--dash-status-warning-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-warning-text);">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <span class="material-symbols-outlined text-[18px]">warning</span>
                        Variabel ini tidak ditemukan di template - kemungkinan autocorrect merusak XML
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($result['missing'] as $var)
                            <code class="rounded-md px-2.5 py-1 text-xs font-mono font-bold"
                                  style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-warning-text);">${{ '{' }}{{ $var }}{{ '}' }}</code>
                        @endforeach
                    </div>
                    <p class="text-xs" style="color: var(--dash-status-warning-text);">
                        Buka file .docx di Word -> Find & Replace -> cari <strong>${{ '{' }}{{ $result['missing'][0] }}{{ '}' }}</strong>. Jika tidak ketemu, matikan autocorrect sebelum mengetik variabel.
                    </p>
                </div>
            @endif

            {{-- Extra vars info --}}
            @if(count($result['extra']))
                <div class="rounded-2xl p-5 space-y-2 shadow-sm"
                     style="background-color: var(--dash-status-info-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-info-text);">
                    <div class="flex items-center gap-2 font-bold text-sm">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                        Variabel ini ada di template tetapi tidak dikenali sistem - tidak akan diisi
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($result['extra'] as $var)
                            <code class="rounded-md px-2.5 py-1 text-xs font-mono font-bold"
                                  style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-info-text);">${{ '{' }}{{ $var }}{{ '}' }}</code>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- All found vars --}}
            @if(count($result['found']))
                <div class="rounded-2xl shadow-sm overflow-hidden"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="px-5 py-3"
                         style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <h3 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">Variabel yang Ditemukan di Template</h3>
                    </div>
                    <div class="p-5 flex flex-wrap gap-2">
                        @foreach($result['found'] as $var)
                            <code class="rounded-md px-2.5 py-1 text-xs font-mono font-bold"
                                  style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-success-text);">${{ '{' }}{{ $var }}{{ '}' }}</code>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    @endif

    {{-- Known variables reference --}}
    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-3.5"
             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-heading);">
                Daftar Variabel yang Didukung ({{ count($knownVars) }} variabel)
            </h3>
        </div>
        <div class="p-5">
            <div class="flex flex-wrap gap-2">
                @foreach($knownVars as $var)
                    <code class="rounded-md px-2.5 py-1 text-xs font-mono"
                          style="background-color: var(--dash-page-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">${{ '{' }}{{ $var }}{{ '}' }}</code>
                @endforeach
            </div>
            <p class="text-xs mt-3" style="color: var(--dash-text-muted);">
                Ketik persis seperti di atas di dalam dokumen Microsoft Word (tanpa spasi tambahan). Nonaktifkan fitur autocorrect di Word saat mengetik karakter <code>${'{'}</code>.
            </p>
        </div>
    </div>
</div>
@endsection
