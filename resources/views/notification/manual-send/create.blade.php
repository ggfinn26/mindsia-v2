@extends('layouts.dashboard')

@section('title', 'Kirim Notifikasi Manual')
@section('header_title', 'Kirim Notifikasi Manual')

@section('content')
@php
    $initialPayload = old('payload', ['nama_pegawai' => '']);
    if (!is_array($initialPayload) || empty($initialPayload)) {
        $initialPayload = ['nama_pegawai' => ''];
    }
    $initialPayloadItems = [];
    foreach ($initialPayload as $k => $v) {
        $initialPayloadItems[] = ['key' => (string) $k, 'value' => (string) $v];
    }
@endphp
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6"
     x-data="{
         recipientType: '{{ old('recipient_type', 'employee') }}',
         templateKey: '{{ old('template_key') }}',
         payloadItems: {{ Js::from($initialPayloadItems) }},
         addPayloadItem() {
             this.payloadItems.push({ key: '', value: '' });
         },
         removePayloadItem(index) {
             this.payloadItems.splice(index, 1);
         }
     }">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('notification-logs.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Log Notifikasi
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Notifikasi</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Kirim Notifikasi Manual</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Kirim notifikasi langsung ke penerima tertentu tanpa melalui routing otomatis.
        </p>
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
            <span>Terdapat kesalahan pada input pengiriman:</span>
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form Card --}}
    <div class="rounded-2xl shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('notification-manual-send.store') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            {{-- Template Selection --}}
            <div>
                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                    Template Notifikasi <span class="text-red-500">*</span>
                </label>
                <select name="template_key" x-model="templateKey"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'"
                        required>
                    <option value="">Pilih template...</option>
                    @foreach ($templates as $tpl)
                    <option value="{{ $tpl->template_key }}" {{ old('template_key') === $tpl->template_key ? 'selected' : '' }}>
                        {{ $tpl->template_key }} ({{ ucfirst($tpl->type) }})
                    </option>
                    @endforeach
                </select>
                @error('template_key')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Recipient Type & ID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                        Tipe Penerima <span class="text-red-500">*</span>
                    </label>
                    <select name="recipient_type" x-model="recipientType"
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'"
                            required>
                        <option value="employee">Pegawai</option>
                        <option value="member">Member</option>
                    </select>
                    @error('recipient_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                        ID Penerima <span class="text-red-500">*</span>
                    </label>
                    <input name="recipient_id" type="number" value="{{ old('recipient_id') }}" min="1"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           placeholder="Masukkan ID..."
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                           required />
                    <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);"
                       x-text="recipientType === 'employee' ? 'ID dari data pegawai (employees)' : 'ID dari data member (members_data)'"></p>
                    @error('recipient_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Payload Variabel --}}
            <div>
                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                    Payload Variabel <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                </label>
                <p class="text-xs mb-3" style="color: var(--dash-text-muted);">
                    Isi parameter nilai untuk menggantikan placeholder variabel di template.
                </p>

                <div class="space-y-2.5">
                    <template x-for="(item, index) in payloadItems" :key="index">
                        <div class="flex items-center gap-2">
                            <input type="text"
                                   x-model="item.key"
                                   placeholder="Nama key (mis. nama_pegawai)"
                                   class="w-1/3 sm:w-2/5 px-3 py-2 text-sm rounded-lg focus:outline-none font-mono transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'" />
                            <input type="text"
                                   :name="item.key ? 'payload[' + item.key + ']' : ''"
                                   x-model="item.value"
                                   placeholder="Nilai variabel..."
                                   class="flex-1 px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'" />
                            <button type="button"
                                    @click="removePayloadItem(index)"
                                    class="p-2 rounded-lg text-xs transition-colors shrink-0"
                                    style="color: var(--dash-danger, #dc2626); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                                    title="Hapus baris variabel">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button"
                        @click="addPayloadItem()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors mt-3"
                        style="color: var(--dash-primary-text); border: 1px dashed var(--dash-card-border); background-color: var(--dash-nav-hover-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-card-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-nav-hover-bg)'">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Tambah Variabel Payload
                </button>
                <p class="mt-2 text-xs" style="color: var(--dash-text-faint);">
                    Variabel yang tidak diisi akan dibiarkan sebagai placeholder asli di dalam pesan.
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t"
                 style="border-color: var(--dash-card-border);">
                <a href="{{ route('notification-logs.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shadow-sm"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    Kirim Sekarang
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
