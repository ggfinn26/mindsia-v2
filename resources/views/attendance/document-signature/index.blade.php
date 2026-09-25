@extends('layouts.dashboard')

@section('title', 'Pengaturan Tanda Tangan Dokumen')
@section('header_title', 'Pengaturan Tanda Tangan Dokumen')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Pengaturan Tanda Tangan Dokumen</h2>
        <p class="mt-1 text-sm text-slate-500">Nama dan jabatan penandatangan institusional per tipe dokumen.</p>
    </div>

    @if (session('success'))
        <div class="flex gap-3 border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex gap-3 border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <ul class="list-disc pl-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    @php
        $employeesJson = $employees->values()->toJson();
    @endphp

    <form method="POST" action="{{ route('attendance.document-signature.upsert') }}"
          x-data="{
              active: '{{ old('_active_type', $documentTypes[0] ?? '') }}',
              employees: {{ $employeesJson }},
              pick(type, name, title) {
                  this.$refs['name_' + type].value = name;
                  this.$refs['title_' + type].value = title;
              }
          }">
        @csrf
        @method('PUT')

        <div class="border border-slate-200 bg-white">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Tipe Dokumen</label>
                <select x-model="active"
                        class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                    @foreach ($documentTypes as $type)
                        @php $hasSetting = isset($settings[$type]) && $settings[$type]->signer_name; @endphp
                        <option value="{{ $type }}">
                            {{ strtoupper($type) }} — {{ $hasSetting ? '✓ ' . $settings[$type]->signer_name : 'Belum diatur' }}
                        </option>
                    @endforeach
                </select>
            </div>

            @foreach ($documentTypes as $type)
                @php $s = $settings[$type] ?? null; @endphp
                <div x-show="active === '{{ $type }}'" x-cloak class="px-5 py-6 space-y-5">

                    {{-- Dropdown autofill --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Pilih dari Daftar Karyawan</label>
                        <select @change="pick('{{ $type }}', $event.target.selectedOptions[0].dataset.name, $event.target.selectedOptions[0].dataset.title)"
                                class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac]">
                            <option value="">— Pilih karyawan untuk autofill —</option>
                            <template x-for="e in employees" :key="e.name">
                                <option :data-name="e.name" :data-title="e.title"
                                        x-text="(e.has_signature ? '✓ ' : '✗ ') + e.name + (e.title ? ' — ' + e.title : '')"></option>
                            </template>
                        </select>
                        <p class="mt-1 text-xs text-slate-400">✓ = sudah ada TTD &nbsp;·&nbsp; ✗ = belum input TTD di profil</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Nama Penandatangan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   x-ref="name_{{ $type }}"
                                   name="signers[{{ $type }}][signer_name]"
                                   value="{{ old("signers.$type.signer_name", $s?->signer_name) }}"
                                   class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac] @error("signers.$type.signer_name") border-rose-500 @enderror"
                                   placeholder="Nama lengkap penandatangan">
                            @error("signers.$type.signer_name")
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Jabatan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text"
                                   x-ref="title_{{ $type }}"
                                   name="signers[{{ $type }}][signer_title]"
                                   value="{{ old("signers.$type.signer_title", $s?->signer_title) }}"
                                   class="block w-full border-slate-300 text-sm focus:border-[#215aac] focus:ring-[#215aac] @error("signers.$type.signer_title") border-rose-500 @enderror"
                                   placeholder="Jabatan / posisi">
                            @error("signers.$type.signer_title")
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="border-t border-slate-100 px-5 py-4 flex justify-end">
                <button type="submit"
                        class="inline-flex min-h-10 items-center gap-2 bg-[#215aac] px-5 text-sm font-semibold text-white hover:bg-[#194a91]">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
