@extends('layouts.app')

@section('title', 'Buat Kurikulum Baru')

@section('content')
    <div class="container py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Buat Kurikulum Baru</h1>
                <p class="text-gray-600 mt-2">Tambah kurikulum dengan sesi dan materi</p>
            </div>
            <a href="{{ route('curriculums.index') }}"
               class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                Kembali
            </a>
        </div>

        <form action="{{ route('curriculums.store') }}" method="POST" class="bg-white rounded-lg shadow">
            @csrf

            <!-- Curriculum Details -->
            <div class="p-8 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900 mb-6">📚 Detail Kurikulum</h2>

                <div class="grid gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Program</label>
                        <select name="program_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Pilih Program --</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}">{{ $program->program_name }}</option>
                            @endforeach
                        </select>
                        @error('program_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kurikulum</label>
                        <input type="text" name="curriculum_name" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               value="{{ old('curriculum_name') }}" />
                        @error('curriculum_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
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

            <!-- Sessions Section -->
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">📅 Sesi (Pertemuan)</h2>
                    <button type="button" id="add-session" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        + Tambah Sesi
                    </button>
                </div>

                <div id="sessions-container" class="space-y-6">
                    <!-- Sessions will be added here -->
                </div>
            </div>

            <!-- Form Actions -->
            <div class="p-8 border-t border-gray-200 flex gap-3 justify-end">
                <a href="{{ route('curriculums.index') }}" class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan Kurikulum
                </button>
            </div>
        </form>
    </div>

    <template id="session-template">
        <div class="border border-gray-200 rounded-lg p-6 session-block" data-session-index="SESSION_INDEX">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Sesi <span class="session-number">1</span></h3>
                <button type="button" class="remove-session px-3 py-1 text-red-600 hover:bg-red-50 rounded">
                    Hapus Sesi
                </button>
            </div>

            <div class="grid gap-4 mb-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Sesi</label>
                        <input type="number" name="sessions[SESSION_INDEX][session_number]" required min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil</label>
                        <input type="number" name="sessions[SESSION_INDEX][sort_order]" value="0" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Sesi</label>
                    <input type="text" name="sessions[SESSION_INDEX][session_title]" required
                           placeholder="Contoh: PERTEMUAN 1 — SPEAKING"
                           class="w-full px-3 py-2 border border-gray-300 rounded" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                    <textarea name="sessions[SESSION_INDEX][description]" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded"></textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="sessions[SESSION_INDEX][is_active]" value="1" checked class="rounded" />
                        <span class="text-sm text-gray-700">Sesi Aktif</span>
                    </label>
                </div>
            </div>

            <!-- Items in Session -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-medium text-gray-900">📄 Materi dalam Sesi</h4>
                    <button type="button" class="add-item px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                        + Tambah Materi
                    </button>
                </div>

                <div class="items-container space-y-3">
                    <!-- Items will be added here -->
                </div>
            </div>
        </div>
    </template>

    <template id="item-template">
        <div class="bg-white border border-gray-300 rounded p-4 item-block" data-item-index="ITEM_INDEX">
            <div class="flex items-center justify-between mb-3">
                <h5 class="font-medium text-gray-900">Materi <span class="item-number">1</span></h5>
                <button type="button" class="remove-item text-red-600 text-sm hover:bg-red-50 px-2 py-1 rounded">
                    Hapus
                </button>
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

        document.getElementById('add-session').addEventListener('click', () => {
            addSession();
        });

        function addSession() {
            const container = document.getElementById('sessions-container');
            const template = document.getElementById('session-template');
            const clone = template.content.cloneNode(true);

            // Replace placeholders
            const html = clone.innerHTML.replace(/SESSION_INDEX/g, sessionIndex);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html;
            const sessionBlock = wrapper.firstElementChild;

            // Add remove listener
            sessionBlock.querySelector('.remove-session').addEventListener('click', (e) => {
                e.preventDefault();
                sessionBlock.remove();
            });

            // Add add-item listener
            sessionBlock.querySelector('.add-item').addEventListener('click', (e) => {
                e.preventDefault();
                addItem(sessionBlock.querySelector('.items-container'), sessionIndex);
            });

            container.appendChild(sessionBlock);
            sessionIndex++;
        }

        function addItem(container, sessionIndex) {
            const template = document.getElementById('item-template');
            const itemIndex = container.querySelectorAll('.item-block').length;
            const clone = template.content.cloneNode(true);

            const html = clone.innerHTML.replace(/SESSION_INDEX/g, sessionIndex).replace(/ITEM_INDEX/g, itemIndex);
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
