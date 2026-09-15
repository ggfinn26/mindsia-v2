@extends('layouts.dashboard')

@section('title', 'Buat Kurikulum Baru')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Buat Kurikulum Baru</h1>
        <a href="{{ route('curriculums.index') }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('curriculums.store') }}" method="POST" class="bg-white shadow sm:rounded-lg">
        @csrf

        <!-- Curriculum Details -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Detail Kurikulum</h2>

            <div class="grid gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Program <span class="text-gray-400">(1:1)</span></label>
                    <select name="program_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Program --</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }} {{ $program->curriculum ? 'disabled' : '' }}>
                                {{ $program->program_name }} {{ $program->curriculum ? '(sudah punya kurikulum)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kurikulum</label>
                    <input type="text" name="curriculum_name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           value="{{ old('curriculum_name') }}" />
                    @error('curriculum_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded" />
                        <span class="text-sm text-gray-700">Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Sessions Section (Pertemuan Mingguan) -->
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">📅 Sesi Pertemuan Mingguan</h2>
                <button type="button" id="add-session" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    + Tambah Sesi
                </button>
            </div>
            <p class="text-sm text-gray-500 mb-4">Setiap sesi mewakili satu pertemuan mingguan. Tambahkan materi (file/link) ke setiap sesi.</p>

            <div id="sessions-container" class="space-y-6"></div>
        </div>

        <!-- Form Actions -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3 justify-end">
            <a href="{{ route('curriculums.index') }}" class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Kurikulum</button>
        </div>
    </form>
</div>

<template id="session-template">
    <div class="border border-gray-200 rounded-lg p-6 session-block" data-session-index="SESSION_INDEX">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-900">Pertemuan Mingguan <span class="session-number">1</span></h3>
            <button type="button" class="remove-session px-3 py-1 text-red-600 hover:bg-red-50 rounded text-sm">Hapus Sesi</button>
        </div>

        <div class="grid gap-4 mb-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Minggu ke-</label>
                    <input type="number" name="sessions[SESSION_INDEX][session_number]" required min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Urutan Tampil</label>
                    <input type="number" name="sessions[SESSION_INDEX][sort_order]" value="0" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Judul Sesi</label>
                <input type="text" name="sessions[SESSION_INDEX][session_title]" required
                       placeholder="Contoh: PERTEMUAN 1 — SPEAKING"
                       class="w-full px-3 py-2 border border-gray-300 rounded" />
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                <textarea name="sessions[SESSION_INDEX][description]" rows="2"
                          class="w-full px-3 py-2 border border-gray-300 rounded"></textarea>
            </div>

            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="sessions[SESSION_INDEX][is_active]" value="1" checked class="rounded" />
                    <span class="text-xs text-gray-700">Sesi Aktif</span>
                </label>
            </div>
        </div>

        <!-- Items in Session -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-medium text-gray-900 text-sm">📄 Materi dalam Sesi</h4>
                <button type="button" class="add-item px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                    + Tambah Materi
                </button>
            </div>
            <div class="items-container space-y-3"></div>
        </div>
    </div>
</template>

<template id="item-template">
    <div class="bg-white border border-gray-300 rounded p-4 item-block" data-item-index="ITEM_INDEX">
        <div class="flex items-center justify-between mb-3">
            <h5 class="font-medium text-gray-900 text-sm">Materi <span class="item-number">1</span></h5>
            <button type="button" class="remove-item text-red-600 text-xs hover:bg-red-50 px-2 py-1 rounded">Hapus</button>
        </div>

        <div class="grid gap-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">No. Urutan</label>
                    <input type="number" name="sessions[SESSION_INDEX][items][ITEM_INDEX][sequence_number]" required min="1"
                           class="w-full px-2 py-1 text-sm border border-gray-300 rounded" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipe Materi</label>
                    <select name="sessions[SESSION_INDEX][items][ITEM_INDEX][material_type]" required class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                        <option value="file">File</option>
                        <option value="external_link">Link Eksternal</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Nama Materi</label>
                <input type="text" name="sessions[SESSION_INDEX][items][ITEM_INDEX][item_name]" required
                       placeholder="Contoh: JUDUL BUKU SPEAKING"
                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded" />
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">File / URL</label>
                <input type="text" name="sessions[SESSION_INDEX][items][ITEM_INDEX][material_value]" required
                       placeholder="Path file atau URL eksternal"
                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded" />
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                <input type="text" name="sessions[SESSION_INDEX][items][ITEM_INDEX][notes]"
                       placeholder="Catatan tambahan"
                       class="w-full px-2 py-1 text-sm border border-gray-300 rounded" />
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="sessions[SESSION_INDEX][items][ITEM_INDEX][is_active]" value="1" checked class="rounded" />
                <span class="text-xs text-gray-700">Materi Aktif</span>
            </label>
        </div>
    </div>
</template>

<script>
    let sessionIndex = 0;

    document.getElementById('add-session').addEventListener('click', () => addSession());

    function addSession() {
        const container = document.getElementById('sessions-container');
        const template = document.getElementById('session-template');
        const clone = template.content.cloneNode(true);
        const html = clone.innerHTML.replace(/SESSION_INDEX/g, sessionIndex);
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
        const clone = template.content.cloneNode(true);
        const html = clone.innerHTML.replace(/SESSION_INDEX/g, sIdx).replace(/ITEM_INDEX/g, itemIndex);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const itemBlock = wrapper.firstElementChild;

        itemBlock.querySelector('.remove-item').addEventListener('click', (e) => {
            e.preventDefault();
            itemBlock.remove();
        });

        container.appendChild(itemBlock);
    }

    // Initialize with one session
    addSession();
</script>
@endsection
