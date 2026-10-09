@extends('layouts.dashboard')

@section('title', 'Template Slip Gaji - MINDSIA')
@section('header_title', 'Template Slip Gaji')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{
         tab: new URLSearchParams(window.location.search).get('tab') || '{{ $errors->any() ? 'upload' : 'templates' }}',
         testType: '{{ $testType ?? '' }}'
     }">

    {{-- Header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Payroll</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Template Slip Gaji</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            Kelola file .docx dan tentukan template per posisi untuk generate slip gaji.
        </p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Tab Nav --}}
    <div style="border-bottom: 2px solid var(--dash-card-border);">
        <nav class="-mb-px flex gap-6">
            <button @click="tab = 'templates'"
                    :style="tab === 'templates'
                        ? 'border-bottom: 2px solid var(--dash-primary); color: var(--dash-primary); margin-bottom: -2px;'
                        : 'border-bottom: 2px solid transparent; color: var(--dash-text-muted); margin-bottom: -2px;'"
                    class="inline-flex items-center gap-2 pb-3 text-sm font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">description</span>
                Template
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold"
                      :style="tab === 'templates'
                          ? 'background-color: var(--dash-primary); color: #fff;'
                          : 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);'">
                    {{ count($templates) }}
                </span>
            </button>
            <button @click="tab = 'upload'"
                    :style="tab === 'upload'
                        ? 'border-bottom: 2px solid var(--dash-primary); color: var(--dash-primary); margin-bottom: -2px;'
                        : 'border-bottom: 2px solid transparent; color: var(--dash-text-muted); margin-bottom: -2px;'"
                    class="inline-flex items-center gap-2 pb-3 text-sm font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">upload_file</span>
                Upload Baru
            </button>
            <button @click="tab = 'tes'"
                    :style="tab === 'tes'
                        ? 'border-bottom: 2px solid var(--dash-primary); color: var(--dash-primary); margin-bottom: -2px;'
                        : 'border-bottom: 2px solid transparent; color: var(--dash-text-muted); margin-bottom: -2px;'"
                    class="inline-flex items-center gap-2 pb-3 text-sm font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">science</span>
                Tes
            </button>
            <button @click="tab = 'assignment'"
                    :style="tab === 'assignment'
                        ? 'border-bottom: 2px solid var(--dash-primary); color: var(--dash-primary); margin-bottom: -2px;'
                        : 'border-bottom: 2px solid transparent; color: var(--dash-text-muted); margin-bottom: -2px;'"
                    class="inline-flex items-center gap-2 pb-3 text-sm font-semibold transition-colors">
                <span class="material-symbols-outlined text-[16px]">assignment_ind</span>
                Assignment
            </button>
        </nav>
    </div>

    {{-- ── TAB 1: Template ──────────────────────────────────────────────── --}}
    <div x-show="tab === 'templates'" class="space-y-3">
        @forelse($templates as $tpl)
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-4 px-5 py-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                     style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                    <span class="material-symbols-outlined text-[20px]">description</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                            {{ $tpl['label'] }}
                        </p>
                        @if(!$tpl['is_base'])
                            <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold"
                                  style="background-color: #f3e8ff; color: #7e22ce;">CUSTOM</span>
                        @endif
                    </div>
                    <p class="text-xs font-mono mt-0.5" style="color: var(--dash-text-faint);">
                        slip_{{ $tpl['type'] }}.docx
                        <span style="color: var(--dash-text-muted);">· {{ $tpl['size'] }} · {{ $tpl['modified'] }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0 flex-wrap justify-end">
                    <a href="{{ route('payroll.slip-templates.download', $tpl['type']) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[13px]">download</span>Download
                    </a>
                    <button type="button"
                            @click="testType = '{{ $tpl['type'] }}'; tab = 'tes'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                            style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[13px]">science</span>Tes
                    </button>
                    <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold cursor-pointer hover:opacity-80 transition-opacity"
                           style="background-color: var(--dash-primary); color: #fff;">
                        <span class="material-symbols-outlined text-[13px]">upload_file</span>Ganti
                        <input type="file" accept=".docx" class="hidden"
                               onchange="uploadTemplate('{{ $tpl['type'] }}', '{{ route('payroll.slip-templates.upload', $tpl['type']) }}', this)">
                    </label>
                    @if(!$tpl['is_base'])
                    <form method="POST" action="{{ route('payroll.slip-templates.destroy', $tpl['type']) }}"
                          onsubmit="return confirm('Hapus template {{ $tpl['label'] }}? Assignment posisi akan direset.')">
                        @csrf @method('DELETE')
                        <button type="submit" title="Hapus template"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                                onmouseover="this.style.backgroundColor='#fecaca'"
                                onmouseout="this.style.backgroundColor='#fee2e2'">
                            <span class="material-symbols-outlined text-[15px]">delete</span>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="rounded-2xl px-6 py-12 text-center"
             style="border: 2px dashed var(--dash-card-border); background-color: var(--dash-card-bg);">
            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">description</span>
            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada template.</p>
            <p class="text-xs mt-1 mb-4" style="color: var(--dash-text-faint);">Upload template pertama untuk mulai generate slip gaji.</p>
            <button @click="tab = 'upload'"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">upload_file</span>Upload Template
            </button>
        </div>
        @endforelse
    </div>

    {{-- ── TAB 2: Upload Baru ───────────────────────────────────────────── --}}
    <div x-show="tab === 'upload'">
        <div class="rounded-2xl p-6"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-start gap-3 mb-5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                     style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                    <span class="material-symbols-outlined text-[20px]">upload_file</span>
                </div>
                <div>
                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Upload Template Baru</h3>
                    <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">
                        Tambah template custom — misalnya khusus direktur atau posisi tertentu.
                    </p>
                </div>
            </div>

            <form action="{{ route('payroll.slip-templates.store') }}" method="POST" enctype="multipart/form-data"
                  class="space-y-4">
                @csrf
                @if($errors->has('label') || $errors->has('template'))
                <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
                     style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
                    <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
                    <div class="space-y-0.5">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div>
                </div>
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Nama Template <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="text" name="label" value="{{ old('label') }}"
                               placeholder="Contoh: Board & Executive"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"
                               required>
                        <p class="text-xs mt-1" style="color: var(--dash-text-faint);">
                            Nama tampil di dropdown assignment posisi.
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            File .docx <span style="color:#dc2626;">*</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer rounded-lg px-3 py-2 transition-colors"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                               onmouseover="this.style.borderColor='var(--dash-primary)'"
                               onmouseout="this.style.borderColor='var(--dash-card-border)'">
                            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-text-muted);">attach_file</span>
                            <span class="text-sm truncate flex-1" id="new-file-name" style="color: var(--dash-text-muted);">Pilih file .docx</span>
                            <input type="file" name="template" accept=".docx" class="hidden" required
                                   onchange="
                                       const nm = document.getElementById('new-file-name');
                                       nm.textContent = this.files[0]?.name || 'Pilih file .docx';
                                       nm.style.color = this.files[0] ? 'var(--dash-text-body)' : 'var(--dash-text-muted)';
                                   ">
                        </label>
                        <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Hanya file .docx yang diterima.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2" style="border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="tab = 'templates'"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">add</span>Upload Template
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── TAB 3: Tes ──────────────────────────────────────────────────── --}}
    <div x-show="tab === 'tes'" class="space-y-4">

        <div class="rounded-2xl p-5"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-start gap-3 mb-5">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                     style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                    <span class="material-symbols-outlined text-[20px]">science</span>
                </div>
                <div>
                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Tes Template</h3>
                    <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">
                        Upload .docx mana saja — sistem isi variabel dengan data nyata/dummy lalu kembalikan hasilnya.
                    </p>
                </div>
            </div>

            @if($testSampleInfo)
                <div class="flex items-center gap-3 mb-4 px-4 py-3 rounded-xl border text-sm"
                     style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
                    <span class="material-symbols-outlined text-[18px]">database</span>
                    <span><strong>Data nyata akan digunakan:</strong> {{ $testSampleInfo }}</span>
                </div>
            @elseif($testType)
                <div class="flex items-center gap-3 mb-4 px-4 py-3 rounded-xl border text-sm"
                     style="background-color: #fffbeb; border-color: #fcd34d; color: #d97706;">
                    <span class="material-symbols-outlined text-[18px]">warning</span>
                    Belum ada payroll tipe ini di DB — akan pakai data contoh statis.
                </div>
            @endif

            {{-- Form bebas — tidak tergantung testType --}}
            <form method="POST" enctype="multipart/form-data"
                  :action="'/payroll-slip-templates/' + (testType || '{{ array_key_first($typeLabels) }}') + '/test'">
                @csrf
                <div class="grid gap-4 sm:grid-cols-[1fr_auto_auto] items-end">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            File .docx <span style="color:#dc2626;">*</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer rounded-lg px-3 py-2 transition-colors"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                               onmouseover="this.style.borderColor='var(--dash-primary)'"
                               onmouseout="this.style.borderColor='var(--dash-card-border)'">
                            <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-text-muted);">attach_file</span>
                            <span class="text-sm truncate flex-1" id="tes-file-name" style="color: var(--dash-text-muted);">Pilih file .docx</span>
                            <input type="file" name="template" accept=".docx" class="hidden" required
                                   onchange="
                                       const nm = document.getElementById('tes-file-name');
                                       nm.textContent = this.files[0]?.name || 'Pilih file .docx';
                                       nm.style.color = this.files[0] ? 'var(--dash-text-body)' : 'var(--dash-text-muted)';
                                   ">
                        </label>
                        <p class="text-xs mt-1" style="color: var(--dash-text-faint);">
                            Diproses lalu dihapus — tidak disimpan permanen.
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">
                            Konteks variabel
                        </label>
                        <select x-model="testType"
                                class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                            @foreach($typeLabels as $type => $label)
                            <option value="{{ $type }}" {{ ($testType ?? array_key_first($typeLabels)) === $type ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">science</span>Proses & Tes
                    </button>
                </div>
            </form>
        </div>

        {{-- Hasil tes --}}
        @if($testResult)
            @if($testResult['dataSource'])
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm"
                     style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
                    <span class="material-symbols-outlined text-[18px]">database</span>
                    Diproses dengan data nyata: <strong class="ml-1">{{ $testResult['dataSource'] }}</strong>
                </div>
            @else
                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm"
                     style="background-color: #fffbeb; border-color: #fcd34d; color: #d97706;">
                    <span class="material-symbols-outlined text-[18px]">science</span>
                    Diproses dengan data contoh statis.
                </div>
            @endif

            @if($testResult['error'])
                <div class="rounded-2xl p-5 space-y-2"
                     style="background-color: #fee2e2; border: 1px solid #fca5a5;">
                    <div class="flex items-center gap-2 font-semibold text-sm" style="color: #dc2626;">
                        <span class="material-symbols-outlined text-[18px]">error</span>Error saat memproses template
                    </div>
                    <p class="text-sm font-mono px-3 py-2 rounded-lg" style="background-color: #fecaca; color: #991b1b;">
                        {{ $testResult['error'] }}
                    </p>
                    <p class="text-xs" style="color: #dc2626;">
                        Kemungkinan XML .docx rusak — pastikan autocorrect tidak merusak karakter <code>${'{'}</code>.
                    </p>
                </div>
            @else
                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-4">
                    <div class="rounded-2xl px-5 py-4 text-center"
                         style="background-color: #f0fdf4; border: 1px solid #bbf7d0;">
                        <p class="text-2xl font-bold" style="color: #059669;">{{ count($testResult['foundVars']) }}</p>
                        <p class="text-xs font-semibold mt-0.5" style="color: #059669;">Variabel Ditemukan</p>
                    </div>
                    <div class="rounded-2xl px-5 py-4 text-center"
                         style="{{ count($testResult['missingVars']) ? 'background-color:#fffbeb;border:1px solid #fcd34d;' : 'background-color:var(--dash-card-bg);border:1px solid var(--dash-card-border);' }}">
                        <p class="text-2xl font-bold" style="color: {{ count($testResult['missingVars']) ? '#d97706' : 'var(--dash-text-faint)' }};">
                            {{ count($testResult['missingVars']) }}
                        </p>
                        <p class="text-xs font-semibold mt-0.5" style="color: {{ count($testResult['missingVars']) ? '#d97706' : 'var(--dash-text-faint)' }};">
                            Variabel Tidak Ditemukan
                        </p>
                    </div>
                    <div class="rounded-2xl px-5 py-4 text-center"
                         style="{{ count($testResult['extraVars']) ? 'background-color:var(--dash-primary-bg);border:1px solid var(--dash-card-border);' : 'background-color:var(--dash-card-bg);border:1px solid var(--dash-card-border);' }}">
                        <p class="text-2xl font-bold" style="color: {{ count(\$testResult['extraVars']) ? 'var(--dash-primary-text)' : 'var(--dash-text-faint)' }};">
                            {{ count($testResult['extraVars']) }}
                        </p>
                        <p class="text-xs font-semibold mt-0.5" style="color: {{ count(\$testResult['extraVars']) ? 'var(--dash-primary-text)' : 'var(--dash-text-faint)' }};">
                            Variabel Tidak Dikenal
                        </p>
                    </div>
                </div>

                {{-- Download hasil --}}
                @if($testResult['resultToken'])
                <div class="rounded-2xl px-5 py-4 flex items-center justify-between gap-4"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div>
                        <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">Hasil sudah diproses</p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Download dan buka di Word untuk cek tampilan.</p>
                    </div>
                    <a href="{{ route('payroll.slip-templates.test.download', ['type' => $testType, 'token' => $testResult['resultToken']]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                       style="background-color: #059669;">
                        <span class="material-symbols-outlined text-[16px]">download</span>Download Hasil
                    </a>
                </div>

                {{-- CTA lanjut upload --}}
                <div class="flex items-center justify-between gap-4 px-5 py-4 rounded-2xl"
                     style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-card-border);">
                    <div>
                        <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">Sudah oke?</p>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Lanjut upload template ke server.</p>
                    </div>
                    <button type="button" @click="tab = 'upload'"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">upload_file</span>Upload Template
                    </button>
                </div>
                @endif

                {{-- Missing vars --}}
                @if(count($testResult['missingVars']))
                <div class="rounded-2xl p-5 space-y-3"
                     style="background-color: #fffbeb; border: 1px solid #fcd34d;">
                    <div class="flex items-center gap-2 font-semibold text-sm" style="color: #92400e;">
                        <span class="material-symbols-outlined text-[18px]">warning</span>
                        Variabel tidak ditemukan — kemungkinan autocorrect merusak XML
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($testResult['missingVars'] as $var)
                            <code class="px-2 py-1 rounded text-xs font-mono"
                                  style="background-color: #fef3c7; border: 1px solid #fcd34d; color: #92400e;">
                                ${{ '{' }}{{ $var }}{{ '}' }}
                            </code>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Extra vars --}}
                @if(count($testResult['extraVars']))
                <div class="rounded-2xl p-5 space-y-3"
                     style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-card-border);">
                    <div class="flex items-center gap-2 font-semibold text-sm" style="color: #1e40af;">
                        <span class="material-symbols-outlined text-[18px]">info</span>
                        Variabel ada di template tapi tidak dikenali — tidak akan diisi
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($testResult['extraVars'] as $var)
                            <code class="px-2 py-1 rounded text-xs font-mono"
                                  style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-card-border); color: var(--dash-primary-text);">
                                ${{ '{' }}{{ $var }}{{ '}' }}
                            </code>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Found vars --}}
                @if(count($testResult['foundVars']))
                <div class="rounded-2xl overflow-hidden"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="px-5 py-3" style="border-bottom: 1px solid var(--dash-card-border);">
                        <h3 class="text-sm font-bold" style="color: var(--dash-text-heading);">
                            Variabel Ditemukan di Template
                        </h3>
                    </div>
                    <div class="p-5 flex flex-wrap gap-2">
                        @foreach($testResult['foundVars'] as $var)
                            <code class="px-2 py-1 rounded text-xs font-mono"
                                  style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
                                ${{ '{' }}{{ $var }}{{ '}' }}
                            </code>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif
        @endif

        {{-- Known vars reference — Alpine driven, selalu tampil berdasarkan konteks dropdown --}}
        <div x-data="{ allVars: @json($allKnownVars), labels: @json($typeLabels) }"
             class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-5 py-3 flex items-center justify-between" style="border-bottom: 1px solid var(--dash-card-border);">
                <h3 class="text-sm font-bold" style="color: var(--dash-text-heading);">
                    Variabel Didukung —
                    <span x-text="labels[testType] || Object.values(labels)[0]"></span>
                    (<span x-text="(allVars[testType] || Object.values(allVars)[0] || []).length"></span>)
                </h3>
            </div>
            <div class="p-5">
                <div class="flex flex-wrap gap-2 mb-3">
                    <template x-for="v in (allVars[testType] || Object.values(allVars)[0] || [])" :key="v">
                        <code class="px-2 py-1 rounded text-xs font-mono"
                              style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"
                              x-text="'${' + v + '}'"></code>
                    </template>
                </div>
                <p class="text-xs" style="color: var(--dash-text-muted);">
                    Untuk baris komponen, sistem clone otomatis — cukup satu baris dengan
                    <code style="color: var(--dash-text-body);">${'{'}nama_komponen_pendapatan{'}'}</code> di template.
                </p>
            </div>
        </div>

    </div>

    {{-- ── TAB 4: Assignment ────────────────────────────────────────────── --}}
    <div x-show="tab === 'assignment'">
        <div class="rounded-2xl p-5 mb-4 text-sm"
             style="background-color: #fffbeb; border: 1px solid #fcd34d; color: #92400e;">
            <div class="flex items-start gap-2">
                <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">auto_fix</span>
                <div>
                    <p class="font-semibold">Auto-detect</p>
                    <p class="mt-0.5 text-xs">Sistem deteksi dari nama posisi — "official tutor" → template tutor, "regular tutor" → tutor, lainnya → marketing. Pilih override untuk paksa template tertentu.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('payroll.slip-templates.assign.update') }}" method="POST">
            @csrf
            <div class="rounded-2xl overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed text-sm">
                        <colgroup>
                            <col class="w-[35%]">
                            <col class="w-[38%]">
                            <col class="w-[27%]">
                        </colgroup>
                        <thead>
                            <tr style="border-bottom: 1px solid var(--dash-card-border);">
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                    style="color: var(--dash-text-muted);">Posisi</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                    style="color: var(--dash-text-muted);">Template</th>
                                <th class="hidden sm:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                                    style="color: var(--dash-text-muted);">Auto-detect</th>
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
                            <tr style="border-bottom: 1px solid var(--dash-card-border);
                                       {{ $current ? 'background-color: var(--dash-primary-bg);' : '' }}"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='{{ $current ? 'var(--dash-primary-bg)' : '' }}'">
                                <td class="px-5 py-3 font-medium" style="color: var(--dash-text-heading);">
                                    {{ $position->position_name }}
                                    @if($current)
                                        <span class="ml-1.5 text-[10px] font-bold uppercase"
                                              style="color: var(--dash-primary);">override</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <select name="assignments[{{ $position->id }}]"
                                            class="block w-full rounded-lg px-3 py-1.5 text-sm focus:outline-none"
                                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                                        <option value="" {{ !$current ? 'selected' : '' }}>Auto-detect</option>
                                        @foreach($typeLabels as $type => $label)
                                        <option value="{{ $type }}" {{ $current === $type ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="hidden sm:table-cell px-5 py-3 text-xs" style="color: var(--dash-text-muted);">
                                    @if(!$current)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 font-medium"
                                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">
                                            <span class="material-symbols-outlined text-[12px]">auto_fix</span>
                                            {{ $typeLabels[$autoType] ?? $autoType }}
                                        </span>
                                    @else
                                        <span style="color: var(--dash-text-faint);">Override aktif</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end px-5 py-4"
                     style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">save</span>Simpan Assignment
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>

<form id="upload-form" method="POST" enctype="multipart/form-data" class="hidden">
    @csrf
    <input type="file" id="upload-input" name="template" accept=".docx">
</form>

@push('scripts')
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
@endpush
@endsection
