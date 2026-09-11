@extends('layouts.app')

@section('title', $curriculum->curriculum_name)

@section('content')
    <div class="container py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $curriculum->curriculum_name }}</h1>
                <p class="text-gray-600 mt-2">Program: <strong>{{ $curriculum->program->program_name }}</strong></p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('curriculums.edit', $curriculum) }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Edit Kurikulum
                </a>
                <a href="{{ route('curriculums.index') }}"
                   class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>

        @if ($curriculum->description)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-gray-700">{{ $curriculum->description }}</p>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">📅 Sesi (Pertemuan)</h2>
                <button type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700" onclick="document.getElementById('add-session-modal').classList.remove('hidden')">
                    + Tambah Sesi
                </button>
            </div>

            @if ($curriculum->sessions->isEmpty())
                <p class="text-gray-600 py-8 text-center">Belum ada sesi. Mulai tambahkan sesi pertama.</p>
            @else
                <div class="space-y-6">
                    @foreach ($curriculum->sessions->sortBy('sort_order') as $session)
                        <div class="border-l-4 border-blue-500 pl-6 pb-6">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">
                                        Sesi {{ $session->session_number }}: {{ $session->session_title }}
                                    </h3>
                                    @if ($session->description)
                                        <p class="text-gray-600 mt-1">{{ $session->description }}</p>
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <button type="button"
                                            onclick="editSession({{ $session->id }}, {{ json_encode($session) }})"
                                            class="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded">
                                        Edit
                                    </button>
                                    <form action="{{ route('curriculum-sessions.destroy', $session) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus sesi ini beserta semua materi di dalamnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 text-sm text-red-600 hover:bg-red-50 rounded">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Items in Session -->
                            <div class="bg-gray-50 rounded-lg p-4 mt-3">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-900">📄 Materi ({{ $session->items->count() }} item)</h4>
                                    <button type="button"
                                            onclick="openAddItemModal({{ $session->id }})"
                                            class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                        + Tambah Materi
                                    </button>
                                </div>

                                @if ($session->items->isEmpty())
                                    <p class="text-gray-600 py-4 text-center text-sm">Belum ada materi dalam sesi ini.</p>
                                @else
                                    <div class="space-y-3">
                                        @foreach ($session->items->sortBy('sequence_number') as $item)
                                            <div class="bg-white border border-gray-200 rounded p-3 flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-medium text-gray-900">{{ $item->sequence_number }}.</span>
                                                        <h5 class="font-medium text-gray-900">{{ $item->item_name }}</h5>
                                                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">{{ $item->material_type }}</span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mt-1 ml-6">{{ $item->material_value }}</p>
                                                    @if ($item->notes)
                                                        <p class="text-xs text-gray-500 mt-1 ml-6">Catatan: {{ $item->notes }}</p>
                                                    @endif
                                                </div>
                                                <div class="flex gap-2 ml-3">
                                                    <button type="button"
                                                            onclick="editItem({{ $item->id }}, {{ json_encode($item) }})"
                                                            class="px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded">
                                                        Edit
                                                    </button>
                                                    <form action="{{ route('curriculum-items.destroy', $item) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-2 py-1 text-xs text-red-600 hover:bg-red-50 rounded">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Add/Edit Session Modal -->
    <div id="session-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900" id="session-modal-title">Tambah Sesi</h3>
            </div>

            <form id="session-form" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Sesi</label>
                    <input type="number" id="session-number" name="session_number" required min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Sesi</label>
                    <input type="text" id="session-title" name="session_title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                    <textarea id="session-description" name="description" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil</label>
                    <input type="number" id="session-sort-order" name="sort_order" value="0" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" id="session-active" name="is_active" value="1" checked class="rounded" />
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>

                <div class="flex gap-3 pt-4">
                    <button type="button"
                            onclick="document.getElementById('session-modal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Sesi</label>
                    <input type="number" name="session_number" required min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Sesi</label>
                    <input type="text" name="session_title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="0" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded" />
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>

                <div class="flex gap-3 pt-4">
                    <button type="button"
                            onclick="document.getElementById('session-modal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add/Edit Item Modal -->
    <div id="item-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">Tambah Materi</h3>
            </div>

            <form id="item-form" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Urutan</label>
                        <input type="number" id="item-sequence" name="sequence_number" required min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <select id="item-type" name="material_type" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="file">File</option>
                            <option value="external_link">Link Eksternal</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Materi</label>
                    <input type="text" id="item-name" name="item_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File / URL</label>
                    <input type="text" id="item-value" name="material_value" required
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                    <input type="text" id="item-notes" name="notes"
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" id="item-active" name="is_active" value="1" checked class="rounded" />
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>

                <div class="flex gap-3 pt-4">
                    <button type="button"
                            onclick="document.getElementById('item-modal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Urutan</label>
                        <input type="number" name="sequence_number" required min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <select name="material_type" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                            <option value="file">File</option>
                            <option value="external_link">Link Eksternal</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Materi</label>
                    <input type="text" name="item_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File / URL</label>
                    <input type="text" name="material_value" required
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                    <input type="text" name="notes"
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded" />
                    <span class="text-sm text-gray-700">Aktif</span>
                </label>

                <div class="flex gap-3 pt-4">
                    <button type="button"
                            onclick="document.getElementById('item-modal').classList.add('hidden')"
                            class="flex-1 px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddSessionModal() {
            document.getElementById('session-modal-title').textContent = 'Tambah Sesi';
            document.getElementById('session-form').action = '{{ route("curriculums.sessions.store", $curriculum) }}';
            document.getElementById('session-form').reset();
            document.getElementById('session-modal').classList.remove('hidden');
        }

        function editSession(id, session) {
            document.getElementById('session-modal-title').textContent = 'Edit Sesi';
            document.getElementById('session-form').action = '{{ url("curriculum-sessions") }}/' + id;
            document.getElementById('session-number').value = session.session_number;
            document.getElementById('session-title').value = session.session_title;
            document.getElementById('session-description').value = session.description || '';
            document.getElementById('session-sort-order').value = session.sort_order;
            document.getElementById('session-active').checked = !!session.is_active;
            document.getElementById('session-modal').classList.remove('hidden');
        }

        function openAddItemModal(sessionId) {
            document.getElementById('item-form').action = '{{ url("curriculum-sessions") }}/' + sessionId + '/items';
            document.getElementById('item-form').reset();
            document.getElementById('item-modal').classList.remove('hidden');
        }

        function editItem(id, item) {
            document.getElementById('item-form').action = '{{ url("curriculum-items") }}/' + id;
            document.getElementById('item-sequence').value = item.sequence_number;
            document.getElementById('item-type').value = item.material_type;
            document.getElementById('item-name').value = item.item_name;
            document.getElementById('item-value').value = item.material_value;
            document.getElementById('item-notes').value = item.notes || '';
            document.getElementById('item-active').checked = !!item.is_active;
            document.getElementById('item-modal').classList.remove('hidden');
        }
    </script>
@endsection
