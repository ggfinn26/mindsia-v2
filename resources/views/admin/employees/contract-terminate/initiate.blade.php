@extends('layouts.dashboard')

@section('title', 'Terminasi Kontrak - ' . $status->employee->full_name)
@section('header_title', 'Terminasi Kontrak')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('employment-statuses.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Kelola Kontrak
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">SDM & Karyawan</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Mulai Terminasi Kontrak</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Pegawai: <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->full_name }}</span> ({{ $status->employee->employee_code }})
        </p>
    </div>

    {{-- Error validation --}}
    @if($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <div class="flex items-center gap-2 font-semibold mb-1">
            <span class="material-symbols-outlined text-[18px]">error</span>
            Ada kesalahan:
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-600 text-[18px]">person_remove</span>
                </div>
                <h2 class="font-bold text-sm font-jakarta" style="color: var(--dash-text-heading);">Informasi & Persyaratan Terminasi</h2>
            </div>
        </div>

        <div class="px-6 py-4" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
            <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Nama Pegawai</p>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->full_name }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Kode Pegawai</p>
                    <p class="text-sm font-mono font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->employee_code }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Posisi</p>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $status->position?->position_name ?? 'Belum diatur' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Tipe Pekerjaan</p>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $status->type_employment }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('contract-terminate.initiate.store', $status) }}" method="POST" class="px-6 py-6 space-y-6" novalidate>
            @csrf

            <div>
                <label for="reason" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Alasan Terminasi <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" id="reason" rows="3" placeholder="Deskripsikan alasan terminasi..." required
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('reason') }}</textarea>
                @error('reason')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Daftar Kewajiban yang Harus Diselesaikan <span class="text-red-500">*</span>
                </label>
                <p class="text-xs mb-3" style="color: var(--dash-text-muted);">Minimal satu item wajib diisi. Jika tidak ada kewajiban yang perlu diselesaikan, isikan "Tidak ada kewajiban yang perlu diselesaikan."</p>
                @error('checklist_items')<p class="mb-2 text-xs text-red-600">{{ $message }}</p>@enderror

                <div id="checklist-items" class="space-y-3">
                    <div class="flex items-center gap-2 group">
                        <input type="text" name="checklist_items[]" placeholder="Contoh: Pengembalian laptop / ID Card" required
                               class="flex-1 px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        <button type="button" onclick="removeItem(this)"
                                class="w-10 h-10 flex items-center justify-center rounded-lg transition-colors"
                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                onmouseover="this.style.backgroundColor='#fee2e2'; this.style.color='#dc2626'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-muted)'">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="button" onclick="addItem()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">add</span> Tambah Item
                    </button>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3" style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('employment-statuses.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: #dc2626;">
                    <span class="material-symbols-outlined text-[16px]">block</span>
                    Mulai Terminasi
                </button>
            </div>
        </form>
    </div>
</div>

<template id="itemTemplate">
    <div class="flex items-center gap-2 group">
        <input type="text" name="checklist_items[]" placeholder="Contoh: Pengembalian laptop / ID Card" required
               class="flex-1 px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
        <button type="button" onclick="removeItem(this)"
                class="w-10 h-10 flex items-center justify-center rounded-lg transition-colors"
                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                onmouseover="this.style.backgroundColor='#fee2e2'; this.style.color='#dc2626'"
                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-muted)'">
            <span class="material-symbols-outlined text-[20px]">delete</span>
        </button>
    </div>
</template>

<script>
function addItem() {
    const container = document.getElementById('checklist-items');
    const clone = document.getElementById('itemTemplate').content.cloneNode(true);
    container.appendChild(clone);
}

function removeItem(btn) {
    const container = document.getElementById('checklist-items');
    if (container.children.length > 1) {
        btn.parentElement.remove();
    } else {
        btn.parentElement.querySelector('input').value = '';
    }
}
</script>
@endsection
