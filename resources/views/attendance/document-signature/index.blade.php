@extends('layouts.dashboard')

@section('title', 'Tanda Tangan Dokumen - MINDSIA')
@section('header_title', 'Tanda Tangan Dokumen')

@section('content')
@php $employeesJson = $employees->values()->toJson(); @endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page header --}}
    <div>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Pengaturan Tanda Tangan Dokumen</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            Nama dan jabatan penandatangan institusional per tipe dokumen.
        </p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5 font-medium">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('attendance.document-signature.upsert') }}"
          x-data="{
              active: '{{ old('_active_type', array_key_first($documentTypes) ?? '') }}',
              employees: {{ $employeesJson }},
              pick(type, name, title) {
                  this.$refs['name_' + type].value = name;
                  this.$refs['title_' + type].value = title;
              }
          }">
        @csrf
        @method('PUT')

        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

            {{-- Type selector --}}
            <div class="px-5 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                       style="color: var(--dash-text-muted);">Tipe Dokumen</label>
                <select x-model="active">
                    @foreach($documentTypes as $type => $label)
                        @php $hasSetting = isset($settings[$type]) && $settings[$type]->signer_name; @endphp
                        <option value="{{ $type }}">
                            {{ $label }} — {{ $hasSetting ? '✓ ' . $settings[$type]->signer_name : 'Belum diatur' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Per-type form panels --}}
            @foreach($documentTypes as $type => $label)
                @php $s = $settings[$type] ?? null; @endphp
                <div x-show="active === '{{ $type }}'" x-cloak class="px-5 py-6 space-y-5">

                    {{-- Employee autofill --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                               style="color: var(--dash-text-muted);">Pilih dari Daftar Karyawan</label>
                        <select @change="pick('{{ $type }}', $event.target.selectedOptions[0].dataset.name, $event.target.selectedOptions[0].dataset.title)">
                            <option value="">Pilih Karyawan (autofill)</option>
                            <template x-for="e in employees" :key="e.name">
                                <option :data-name="e.name" :data-title="e.title"
                                        x-text="(e.has_signature ? '✓ ' : '✗ ') + e.name + (e.title ? ' — ' + e.title : '')"></option>
                            </template>
                        </select>
                        <p class="mt-1.5 text-xs" style="color: var(--dash-text-faint);">
                            ✓ = sudah ada TTD &nbsp;·&nbsp; ✗ = belum input TTD di profil
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5"
                                   style="color: var(--dash-text-body);">
                                Nama Penandatangan <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text"
                                   x-ref="name_{{ $type }}"
                                   name="signers[{{ $type }}][signer_name]"
                                   value="{{ old("signers.$type.signer_name", $s?->signer_name) }}"
                                   placeholder="Nama lengkap penandatangan">
                            @error("signers.$type.signer_name")
                                <p class="mt-1 text-xs" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5"
                                   style="color: var(--dash-text-body);">
                                Jabatan <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text"
                                   x-ref="title_{{ $type }}"
                                   name="signers[{{ $type }}][signer_title]"
                                   value="{{ old("signers.$type.signer_title", $s?->signer_title) }}"
                                   placeholder="Jabatan / posisi">
                            @error("signers.$type.signer_title")
                                <p class="mt-1 text-xs" style="color: #dc2626;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Footer --}}
            <div class="px-5 py-4 flex justify-end"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
