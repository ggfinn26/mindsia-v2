@extends('layouts.dashboard')

@section('title', 'Pengaturan Tanda Tangan Dokumen - MINDSIA')
@section('header_title', 'Pengaturan Tanda Tangan Dokumen')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Sistem & Akses</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Pengaturan Tanda Tangan Dokumen</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Nama dan jabatan penandatangan institusional per tipe dokumen resmi.
        </p>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <div class="flex items-center gap-2 font-semibold mb-1">
                <span class="material-symbols-outlined text-[18px]">error</span>
                Terjadi kesalahan validasi:
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $employeesJson = $employees->values()->toJson();
        $initialType = old('_active_type', session('active_type', array_key_first($documentTypes) ?? ''));
    @endphp

    <div x-data="{
        active: '{{ $initialType }}',
        employees: {{ $employeesJson }},
        pick(type, name, title) {
            if (!name) return;
            if (this.$refs['name_' + type]) {
                this.$refs['name_' + type].value = name;
            }
            if (this.$refs['title_' + type]) {
                this.$refs['title_' + type].value = title || '';
            }
        }
    }"
    class="rounded-2xl overflow-hidden"
    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        {{-- Tab Navigasi Dokumen --}}
        <div class="border-b flex overflow-x-auto" style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            @foreach ($documentTypes as $type => $label)
                @php $hasSetting = isset($settings[$type]) && !empty($settings[$type]->signer_name); @endphp
                <button type="button"
                        @click="active = '{{ $type }}'"
                        class="px-5 py-3.5 text-sm font-semibold transition-colors flex items-center gap-2.5 border-b-2 whitespace-nowrap"
                        :style="active === '{{ $type }}'
                            ? 'border-color: var(--dash-primary); color: var(--dash-primary); background-color: var(--dash-card-bg);'
                            : 'border-color: transparent; color: var(--dash-text-muted); background-color: transparent;'"
                        onmouseover="if (active !== '{{ $type }}') { this.style.color = 'var(--dash-text-heading)'; this.style.backgroundColor = 'var(--dash-nav-hover-bg)'; }"
                        onmouseout="if (active !== '{{ $type }}') { this.style.color = 'var(--dash-text-muted)'; this.style.backgroundColor = 'transparent'; }">
                    <span class="material-symbols-outlined text-[18px]">description</span>
                    <span>{{ $label }}</span>
                    @if ($hasSetting)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                              style="background-color: #f0fdf4; color: #059669;">
                            <span class="material-symbols-outlined text-[13px] leading-none">check_circle</span>
                            Teratur
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                              style="background-color: #fef2f2; color: #dc2626;">
                            <span class="material-symbols-outlined text-[13px] leading-none">warning</span>
                            Belum diatur
                        </span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Konten Per Tipe Dokumen --}}
        @foreach ($documentTypes as $type => $label)
            @php
                $s = $settings[$type] ?? null;
                $hasSetting = isset($s) && !empty($s->signer_name);
            @endphp
            <div x-show="active === '{{ $type }}'" x-cloak>
                <form method="POST" action="{{ route('attendance.document-signature.upsert') }}" class="m-0">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_active_type" value="{{ $type }}">

                    <div class="p-6 space-y-6">
                        {{-- Info Penjelasan Dokumen --}}
                        <div class="p-4 rounded-xl flex items-start gap-3.5"
                             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-primary);">
                                <span class="material-symbols-outlined text-[20px]">assignment_turned_in</span>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                                    Penandatangan {{ $label }}
                                </h2>
                                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                    @if ($type === 'SP')
                                        Nama dan jabatan penandatangan institusional yang dicantumkan secara otomatis pada dokumen Surat Peringatan (SP-I, SP-II, SP-III).
                                    @elseif ($type === 'kwitansi')
                                        Nama dan jabatan penandatangan institusional yang dicantumkan pada tanda bukti kwitansi member dan slip gaji resmi.
                                    @else
                                        Nama dan jabatan penandatangan resmi yang dicantumkan pada dokumen {{ $label }}.
                                    @endif
                                </p>
                            </div>
                        </div>

                        {{-- Status Penandatangan Saat Ini --}}
                        @if ($hasSetting)
                            <div class="px-4 py-3 rounded-xl border flex items-center justify-between text-xs font-medium"
                                 style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">verified</span>
                                    <span>Penandatangan saat ini: <strong>{{ $s->signer_name }}</strong> ({{ $s->signer_title }})</span>
                                </div>
                                <span class="text-[11px] px-2.5 py-0.5 rounded-full font-semibold"
                                      style="background-color: #dcfce7; color: #166534;">Aktif</span>
                            </div>
                        @else
                            <div class="px-4 py-3 rounded-xl border flex items-center gap-2 text-xs font-medium"
                                 style="background-color: #fffbeb; border-color: #fde68a; color: #b45309;">
                                <span class="material-symbols-outlined text-[18px]">info</span>
                                <span>Tipe dokumen ini belum memiliki penandatangan yang diatur. Silakan pilih dari daftar karyawan atau ketik manual.</span>
                            </div>
                        @endif

                        {{-- Dropdown Autofill Karyawan --}}
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                                   style="color: var(--dash-text-muted);">
                                Pilih dari Daftar Karyawan (Autofill)
                            </label>
                            <select @change="pick('{{ $type }}', $event.target.selectedOptions[0].dataset.name, $event.target.selectedOptions[0].dataset.title); $event.target.value = '';"
                                    class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                <option value="">Pilih karyawan untuk autofill...</option>
                                <template x-for="e in employees" :key="e.name">
                                    <option :data-name="e.name"
                                            :data-title="e.title"
                                            x-text="(e.has_signature ? '✓ ' : '✗ ') + e.name + (e.title ? ' - ' + e.title : '')"></option>
                                </template>
                            </select>
                            <p class="mt-1.5 text-xs" style="color: var(--dash-text-faint);">
                                ✓ = sudah unggah berkas TTD di profil &nbsp;·&nbsp; ✗ = belum unggah berkas TTD di profil
                            </p>
                        </div>

                        {{-- Form Inputs --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                                    Nama Penandatangan <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       x-ref="name_{{ $type }}"
                                       name="signers[{{ $type }}][signer_name]"
                                       value="{{ old("signers.$type.signer_name", $s?->signer_name) }}"
                                       placeholder="Contoh: Dr. Budi Santoso, M.M."
                                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all @error("signers.$type.signer_name") border-red-500 @enderror"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                                @error("signers.$type.signer_name")
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                                    Jabatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       x-ref="title_{{ $type }}"
                                       name="signers[{{ $type }}][signer_title]"
                                       value="{{ old("signers.$type.signer_title", $s?->signer_title) }}"
                                       placeholder="Contoh: Direktur Utama"
                                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all @error("signers.$type.signer_title") border-red-500 @enderror"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                                @error("signers.$type.signer_title")
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Footer Action --}}
                    <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-b-2xl"
                         style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                        <p class="text-xs" style="color: var(--dash-text-faint);">
                            Berkas tanda tangan visual diambil otomatis dari profil karyawan terkait.
                        </p>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        @endforeach

    </div>

</div>
@endsection
