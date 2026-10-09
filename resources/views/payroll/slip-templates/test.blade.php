@extends('layouts.dashboard')

@section('header_title', 'Tes Template Slip - {{ $label }}')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[color:var(--dash-primary-text,#215aac)]">Payroll → Template Slip</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Tes Template: {{ $label }}</h1>
            <p class="text-sm text-slate-500 mt-0.5">Upload template .docx, sistem isi dengan data nyata/dummy, download hasilnya untuk verifikasi.</p>
        </div>
        <a href="{{ route('payroll.slip-templates.index') }}"
           class="inline-flex items-center gap-2 border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 shrink-0">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
        </a>
    </div>

    {{-- Data source info --}}
    @if($sampleInfo)
        <div class="flex items-center gap-3 border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            <span class="material-symbols-outlined text-[18px] shrink-0">database</span>
            <div><span class="font-semibold">Data nyata dari DB akan digunakan:</span> {{ $sampleInfo }}</div>
        </div>
    @else
        <div class="flex items-center gap-3 border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            <span class="material-symbols-outlined text-[18px] shrink-0">warning</span>
            Belum ada payroll tipe ini di DB — akan menggunakan data contoh statis.
        </div>
    @endif

    {{-- Upload form --}}
    <form action="{{ route('payroll.slip-templates.test.process', $type) }}" method="POST"
          enctype="multipart/form-data" class="border border-slate-200 bg-white">
        @csrf
        <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Upload Template untuk Diuji</h3>
        </div>
        <div class="p-6 space-y-4">
            @error('template')
                <div class="flex gap-2 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <span class="material-symbols-outlined text-[18px]">error</span> {{ $message }}
                </div>
            @enderror
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">File Template (.docx) <span class="text-red-500">*</span></label>
                <input type="file" name="template" accept=".docx" required
                       class="w-full border border-slate-300 px-4 py-2 text-sm focus:border-[color:var(--dash-primary,#215aac)] focus:ring-1 focus:ring-[color:var(--dash-primary,#215aac)]
                              file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100" />
                <p class="text-xs text-slate-500 mt-1">Template diproses dengan data dummy/nyata lalu dihapus dari server.</p>
            </div>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-[var(--dash-primary,#215aac)] text-white text-sm font-semibold hover:bg-[#194a91]">
                Proses & Tes
            </button>
        </div>
    </form>

    {{-- Result --}}
    @if($result)
        @if($result['dataSource'])
            <div class="flex items-center gap-2 border border-green-200 bg-green-50 px-4 py-2.5 text-sm text-green-800">
                <span class="material-symbols-outlined text-[16px]">database</span>
                <span>Diproses dengan data nyata: <strong>{{ $result['dataSource'] }}</strong></span>
            </div>
        @else
            <div class="flex items-center gap-2 border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm text-amber-700">
                <span class="material-symbols-outlined text-[16px]">science</span>
                Diproses dengan data contoh statis.
            </div>
        @endif

        @if($result['error'])
            <div class="border border-red-300 bg-red-50 p-5 space-y-2">
                <div class="flex items-center gap-2 font-semibold text-red-800">
                    <span class="material-symbols-outlined text-[18px]">error</span> Error saat memproses template
                </div>
                <p class="text-sm text-red-700 font-mono bg-red-100 px-3 py-2">{{ $result['error'] }}</p>
                <p class="text-xs text-red-600">Kemungkinan XML di dalam .docx rusak — pastikan tidak ada autocorrect pada karakter <code>${'{'}</code>.</p>
            </div>
        @else
            <div class="grid grid-cols-3 gap-4">
                <div class="border border-green-200 bg-green-50 px-5 py-4 text-center">
                    <div class="text-2xl font-bold text-green-700">{{ count($result['found']) }}</div>
                    <div class="text-xs font-semibold text-green-600 mt-0.5">Variabel Ditemukan</div>
                </div>
                <div class="border {{ count($result['missing']) ? 'border-amber-300 bg-amber-50' : 'border-slate-200 bg-slate-50' }} px-5 py-4 text-center">
                    <div class="text-2xl font-bold {{ count($result['missing']) ? 'text-amber-700' : 'text-slate-400' }}">{{ count($result['missing']) }}</div>
                    <div class="text-xs font-semibold {{ count($result['missing']) ? 'text-amber-600' : 'text-slate-400' }} mt-0.5">Variabel Tidak Ditemukan</div>
                </div>
                <div class="border {{ count($result['extra']) ? 'border-blue-200 bg-blue-50' : 'border-slate-200 bg-slate-50' }} px-5 py-4 text-center">
                    <div class="text-2xl font-bold {{ count($result['extra']) ? 'text-blue-700' : 'text-slate-400' }}">{{ count($result['extra']) }}</div>
                    <div class="text-xs font-semibold {{ count($result['extra']) ? 'text-blue-600' : 'text-slate-400' }} mt-0.5">Variabel Tidak Dikenal</div>
                </div>
            </div>

            @if($result['resultToken'])
                <div class="border border-slate-200 bg-white px-6 py-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Hasil sudah diproses</p>
                        <p class="text-xs text-slate-500">Download dan buka di Word untuk cek tampilan & variabel yang tergantikan.</p>
                    </div>
                    <a href="{{ route('payroll.slip-templates.test.download', ['type' => $type, 'token' => $result['resultToken']]) }}"
                       class="inline-flex items-center gap-2 bg-[var(--dash-primary,#215aac)] text-white px-5 py-2.5 text-sm font-semibold hover:bg-[#194a91] shrink-0">
                        <span class="material-symbols-outlined text-[16px]">download</span> Download Hasil
                    </a>
                </div>
            @endif

            @if(count($result['missing']))
                <div class="border border-amber-200 bg-amber-50 p-5 space-y-3">
                    <div class="flex items-center gap-2 font-semibold text-amber-800 text-sm">
                        <span class="material-symbols-outlined text-[18px]">warning</span>
                        Variabel tidak ditemukan — kemungkinan autocorrect merusak XML
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($result['missing'] as $var)
                            <code class="bg-amber-100 border border-amber-300 text-amber-800 text-xs px-2 py-1">${{ '{' }}{{ $var }}{{ '}' }}</code>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(count($result['extra']))
                <div class="border border-blue-200 bg-blue-50 p-5 space-y-2">
                    <div class="flex items-center gap-2 font-semibold text-blue-800 text-sm">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                        Variabel ada di template tapi tidak dikenali sistem — tidak akan diisi
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($result['extra'] as $var)
                            <code class="bg-blue-100 border border-blue-200 text-blue-700 text-xs px-2 py-1">${{ '{' }}{{ $var }}{{ '}' }}</code>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(count($result['found']))
                <div class="border border-slate-200 bg-white">
                    <div class="border-b border-slate-200 bg-slate-50/50 px-5 py-3">
                        <h3 class="text-sm font-bold text-slate-700">Variabel yang Ditemukan di Template</h3>
                    </div>
                    <div class="p-5 flex flex-wrap gap-2">
                        @foreach($result['found'] as $var)
                            <code class="bg-green-50 border border-green-200 text-green-800 text-xs px-2 py-1">${{ '{' }}{{ $var }}{{ '}' }}</code>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    @endif

    {{-- Known variables reference --}}
    <div class="border border-slate-200 bg-white">
        <div class="border-b border-slate-200 bg-slate-50/50 px-5 py-3">
            <h3 class="text-sm font-bold text-slate-700">Variabel yang Didukung untuk Tipe {{ $label }} ({{ count($knownVars) }} variabel)</h3>
        </div>
        <div class="p-5">
            <div class="flex flex-wrap gap-2">
                @foreach($knownVars as $var)
                    <code class="bg-slate-100 border border-slate-200 text-slate-700 text-xs px-2 py-1">${{ '{' }}{{ $var }}{{ '}' }}</code>
                @endforeach
            </div>
            <p class="text-xs text-slate-500 mt-3">Untuk baris komponen, sistem clone otomatis — cukup tulis satu baris dengan variabel <code>${'{'}nama_komponen_pendapatan{'}'}</code> di template.</p>
        </div>
    </div>
</div>
@endsection
