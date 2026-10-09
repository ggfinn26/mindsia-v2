@extends('layouts.dashboard')

@section('title', 'Buat Kurikulum Baru - MINDSIA')
@section('header_title', 'Buat Kurikulum')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('curriculums.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Buat Kurikulum Baru</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">Kurikulum → Program (relasi 1:1)</p>
        </div>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('curriculums.store') }}" method="POST" class="space-y-5" novalidate>
        @csrf

        {{-- Detail Kurikulum --}}
        <div class="rounded-2xl overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Detail Kurikulum</h2>
            </div>
            <div class="p-5 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Program <span style="color:#dc2626;">*</span>
                    </label>
                    <select name="program_id" required
                            class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        <option value="">Pilih Program</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }} {{ $program->curriculum ? 'disabled' : '' }}>
                                {{ $program->program_name }} {{ $program->curriculum ? '(sudah punya kurikulum)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_id')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Nama Kurikulum <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" name="curriculum_name" required value="{{ old('curriculum_name') }}"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    @error('curriculum_name')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Deskripsi <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                    </label>
                    <textarea name="description" rows="3"
                              class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                              style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded">
                        <span class="text-sm font-medium" style="color: var(--dash-text-body);">Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Sesi Mingguan --}}
        <div class="rounded-2xl p-5"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">calendar_month</span>
                    <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Sesi Pertemuan Mingguan</h2>
                </div>
                <button type="button" id="add-session"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: #059669;">
                    <span class="material-symbols-outlined text-[14px]">add</span>Tambah Sesi
                </button>
            </div>
            <p class="text-xs mb-4" style="color: var(--dash-text-muted);">
                Setiap sesi mewakili satu pertemuan mingguan. Tambahkan materi (file/link) ke setiap sesi.
            </p>
            <div id="sessions-container" class="space-y-6"></div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 justify-end">
            <a href="{{ route('curriculums.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors"
               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</a>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">Simpan Kurikulum</button>
        </div>
    </form>
</div>

<template id="session-template">
    <div class="rounded-xl p-5 session-block" data-session-index="SESSION_INDEX"
         style="border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold" style="color: var(--dash-text-heading);">
                Pertemuan Mingguan <span class="session-number">1</span>
            </h3>
            <button type="button"
                    class="remove-session inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold transition-colors"
                    style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;">
                <span class="material-symbols-outlined text-[13px]">delete</span>Hapus Sesi
            </button>
        </div>
        <div class="grid gap-4 mb-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Minggu ke-</label>
                    <input type="number" name="sessions[SESSION_INDEX][session_number]" required min="1"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Urutan Tampil</label>
                    <input type="number" name="sessions[SESSION_INDEX][sort_order]" value="0" min="0"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Judul Sesi</label>
                <input type="text" name="sessions[SESSION_INDEX][session_title]" required
                       placeholder="Contoh: PERTEMUAN 1 — SPEAKING"
                       class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Deskripsi (opsional)</label>
                <textarea name="sessions[SESSION_INDEX][description]" rows="2"
                          class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"></textarea>
            </div>
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="sessions[SESSION_INDEX][is_active]" value="1" checked class="h-4 w-4 rounded">
                    <span class="text-xs font-medium" style="color: var(--dash-text-body);">Sesi Aktif</span>
                </label>
            </div>
        </div>
        <div class="rounded-xl p-4" style="background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-medium text-sm" style="color: var(--dash-text-heading);">Materi dalam Sesi</h4>
                <button type="button"
                        class="add-item inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[13px]">add</span>Tambah Materi
                </button>
            </div>
            <div class="items-container space-y-3"></div>
        </div>
    </div>
</template>

<template id="item-template">
    <div class="rounded-lg p-4 item-block" data-item-index="ITEM_INDEX"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between mb-3">
            <h5 class="font-medium text-sm" style="color: var(--dash-text-heading);">Materi <span class="item-number">1</span></h5>
            <button type="button"
                    class="remove-item inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold transition-colors"
                    style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;">
                <span class="material-symbols-outlined text-[13px]">close</span>Hapus
            </button>
        </div>
        <div class="grid gap-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">No. Urutan</label>
                    <input type="number" name="sessions[SESSION_INDEX][items][ITEM_INDEX][sequence_number]" required min="1"
                           class="block w-full rounded-lg px-2.5 py-1.5 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Tipe Materi</label>
                    <select name="sessions[SESSION_INDEX][items][ITEM_INDEX][material_type]" required
                            class="block w-full rounded-lg px-2.5 py-1.5 text-sm focus:outline-none"
                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        <option value="file">File</option>
                        <option value="external_link">Link Eksternal</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Nama Materi</label>
                <input type="text" name="sessions[SESSION_INDEX][items][ITEM_INDEX][item_name]" required
                       placeholder="Contoh: JUDUL BUKU SPEAKING"
                       class="block w-full rounded-lg px-2.5 py-1.5 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">File / URL</label>
                <input type="text" name="sessions[SESSION_INDEX][items][ITEM_INDEX][material_value]" required
                       placeholder="Path file atau URL eksternal"
                       class="block w-full rounded-lg px-2.5 py-1.5 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>
            <div>
                <label class="block text-xs font-medium mb-1" style="color: var(--dash-text-body);">Catatan (opsional)</label>
                <input type="text" name="sessions[SESSION_INDEX][items][ITEM_INDEX][notes]"
                       placeholder="Catatan tambahan"
                       class="block w-full rounded-lg px-2.5 py-1.5 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="sessions[SESSION_INDEX][items][ITEM_INDEX][is_active]" value="1" checked class="h-4 w-4 rounded">
                <span class="text-xs font-medium" style="color: var(--dash-text-body);">Materi Aktif</span>
            </label>
        </div>
    </div>
</template>

@push('scripts')
<script>
    let sessionIndex = 0;

    document.getElementById('add-session').addEventListener('click', () => addSession());

    function addSession() {
        const container = document.getElementById('sessions-container');
        const template = document.getElementById('session-template');
        const temp = document.createElement('div');
        temp.appendChild(template.content.cloneNode(true));
        const html = temp.innerHTML.replace(/SESSION_INDEX/g, sessionIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const sessionBlock = wrapper.firstElementChild;

        sessionBlock.querySelector('.remove-session').addEventListener('click', (e) => {
            e.preventDefault();
            sessionBlock.remove();
        });

        sessionBlock.querySelector('.add-item').addEventListener('click', (e) => {
            e.preventDefault();
            addItem(sessionBlock.querySelector('.items-container'), sessionIndex);
        });

        container.appendChild(sessionBlock);
        sessionIndex++;
    }

    function addItem(container, sIdx) {
        const template = document.getElementById('item-template');
        const itemIndex = container.querySelectorAll('.item-block').length;
        const temp = document.createElement('div');
        temp.appendChild(template.content.cloneNode(true));
        const html = temp.innerHTML.replace(/SESSION_INDEX/g, sIdx).replace(/ITEM_INDEX/g, itemIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const itemBlock = wrapper.firstElementChild;

        itemBlock.querySelector('.remove-item').addEventListener('click', (e) => {
            e.preventDefault();
            itemBlock.remove();
        });

        container.appendChild(itemBlock);
    }

    addSession();
</script>
@endpush
@endsection
