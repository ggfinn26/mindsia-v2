@extends('layouts.dashboard')

@section('title', $curriculum->curriculum_name)

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $curriculum->curriculum_name }}</h1>
            <p class="text-sm text-gray-500 mt-1">Program: <strong>{{ $curriculum->program->program_name }}</strong></p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('curriculums.edit', $curriculum) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Edit</a>
            <a href="{{ route('curriculums.index') }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">Kembali</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($curriculum->description)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-gray-700 text-sm">{{ $curriculum->description }}</p>
        </div>
    @endif

    <div class="bg-white shadow sm:rounded-lg mb-6">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">📅 Sesi Pertemuan Mingguan</h2>
            <button type="button" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm"
                    onclick="openAddSessionModal()">
                + Tambah Sesi
            </button>
        </div>

        <div class="px-6 py-4">
            @if ($curriculum->sessions->isEmpty())
                <p class="text-gray-500 text-sm py-8 text-center">Belum ada sesi. Tambahkan sesi pertemuan mingguan.</p>
            @else
                <div class="space-y-6">
                    @foreach ($curriculum->sessions->sortBy('sort_order') as $session)
                        <div class="border-l-4 border-blue-500 pl-6 pb-6" id="session-{{ $session->id }}">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-base font-bold text-gray-900">
                                        Minggu {{ $session->session_number }}: {{ $session->session_title }}
                                    </h3>
                                    @if ($session->description)
                                        <p class="text-gray-600 mt-1 text-sm">{{ $session->description }}</p>
                                    @endif
                                </div>
                                <div class="flex gap-2">
                                    <button type="button"
                                            onclick="editSession({{ $session->id }}, {{ json_encode($session) }})"
                                            class="px-3 py-1 text-sm text-blue-600 hover:bg-blue-50 rounded">Edit</button>
                                    <form action="{{ route('curriculum-sessions.destroy', $session) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus sesi ini beserta semua materi di dalamnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 text-sm text-red-600 hover:bg-red-50 rounded">Hapus</button>
                                    </form>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4 mt-3">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-900 text-sm">📄 Materi ({{ $session->items->count() }} item)</h4>
                                    <button type="button"
                                            onclick="openAddItemModal({{ $session->id }})"
                                            class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                        + Tambah Materi
                                    </button>
                                </div>

                                @if ($session->items->isEmpty())
                                    <p class="text-gray-500 py-4 text-center text-sm">Belum ada materi dalam sesi ini.</p>
                                @else
                                    <div class="space-y-3">
                                        @foreach ($session->items->sortBy('sequence_number') as $item)
                                            <div class="bg-white border border-gray-200 rounded p-3 flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-medium text-gray-900">{{ $item->sequence_number }}.</span>
                                                        <h5 class="font-medium text-gray-900 text-sm">{{ $item->item_name }}</h5>
                                                        <span class="px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-700">{{ $item->material_type }}</span>
                                                    </div>
                                                    @if ($item->material_type === 'file' && $item->material_value)
                                                        <a href="{{ asset('storage/' . $item->material_value) }}" target="_blank"
                                                           class="text-xs text-blue-600 hover:underline mt-1 ml-6 block">
                                                            {{ basename($item->material_value) }}
                                                        </a>
                                                    @elseif ($item->material_value)
                                                        <p class="text-xs text-gray-600 mt-1 ml-6">{{ $item->material_value }}</p>
                                                    @endif
                                                    @if ($item->notes)
                                                        <p class="text-xs text-gray-500 mt-1 ml-6">Catatan: {{ $item->notes }}</p>
                                                    @endif
                                                </div>
                                                <div class="flex gap-2 ml-3">
                                                    @if ($item->material_type === 'interactive')
                                                        <a href="{{ route('curriculum-items.interactive.show', $item) }}"
                                                           class="px-2 py-1 text-xs text-indigo-600 hover:bg-indigo-50 rounded font-medium">Edit Modul</a>
                                                    @else
                                                        <button type="button"
                                                                onclick="editItem({{ $item->id }}, {{ json_encode($item) }})"
                                                                class="px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded">Edit</button>
                                                    @endif
                                                    <form action="{{ route('curriculum-items.destroy', $item) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-2 py-1 text-xs text-red-600 hover:bg-red-50 rounded">Hapus</button>
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
</div>

<!-- Add/Edit Session Modal -->
<div id="session-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900" id="session-modal-title">Tambah Sesi Pertemuan</h3>
        </div>
        <form id="session-form" method="POST" class="p-6 space-y-4" novalidate>
            @csrf
            <input type="hidden" id="session-method" name="_method" value="POST">
            <div id="session-errors" class="hidden bg-red-50 border border-red-300 text-red-700 rounded px-3 py-2 text-sm"></div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Minggu ke-</label>
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
                <button type="button" onclick="closeModal('session-modal','session-errors')"
                        class="flex-1 px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Item Modal -->
<div id="item-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 id="item-modal-title" class="text-lg font-bold text-gray-900">Tambah Materi</h3>
        </div>
        <form id="item-form" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" novalidate>
            @csrf
            <input type="hidden" id="item-method" name="_method" value="POST">
            <div id="item-errors" class="hidden bg-red-50 border border-red-300 text-red-700 rounded px-3 py-2 text-sm"></div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Urutan</label>
                    <input type="number" id="item-sequence" name="sequence_number" required min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Materi</label>
                    <select id="item-type" name="material_type" required onchange="onItemTypeChange(this.value)"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        <option value="file">File</option>
                        <option value="external_link">Link Eksternal</option>
                        <option value="interactive">Latihan Interaktif (TOEFL)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Materi</label>
                <input type="text" id="item-name" name="item_name" required
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
            </div>
            {{-- File upload --}}
            <div id="item-value-file">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                <div id="item-file-zone"
                     class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors"
                     onclick="document.getElementById('item-file').click()"
                     onkeydown="if(event.key==='Enter'||event.key===' ')document.getElementById('item-file').click()"
                     tabindex="0" role="button" aria-label="Pilih file untuk diupload">
                    <input type="file" id="item-file" name="material_file" class="hidden"
                           accept="application/pdf,video/mp4,video/webm,video/ogg,video/quicktime,image/jpeg,image/png,image/gif,image/webp,audio/mpeg,audio/wav,audio/aac,audio/mp4"
                           onchange="updateUploadDisplay(this)" />
                    <div id="item-file-label">
                        <p class="text-sm text-gray-500">Klik untuk pilih file</p>
                        <p class="text-xs text-gray-400 mt-1">PDF &middot; Video &middot; Gambar &middot; Audio &mdash; maks. 50MB</p>
                    </div>
                    <div id="item-file-selected" class="hidden">
                        <p class="text-sm font-medium text-blue-600" id="item-file-name"></p>
                        <p class="text-xs text-gray-400 mt-1">Klik untuk ganti file</p>
                    </div>
                </div>
                <p id="item-current-file" class="text-xs text-blue-600 mt-1 hidden"></p>
            </div>
            {{-- URL input --}}
            <div id="item-value-url" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                <input type="text" id="item-value" name="material_value"
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                       placeholder="https://..." />
            </div>
            {{-- Interactive notice --}}
            <div id="item-value-interactive" class="hidden">
                <p class="text-sm text-indigo-600 bg-indigo-50 border border-indigo-200 rounded p-3">
                    Setelah disimpan, klik <strong>Edit Modul</strong> untuk menyusun blok &amp; soal latihan.
                </p>
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
                <button type="button" onclick="closeModal('item-modal','item-errors')"
                        class="flex-1 px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const sessionItemCounts = @json($curriculum->sessions->mapWithKeys(fn ($s) => [$s->id => $s->items->max('sequence_number') ?? 0]));
    const nextSessionNumber = {{ ($curriculum->sessions->max('session_number') ?? 0) + 1 }};

    function updateUploadDisplay(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('item-file-label').classList.add('hidden');
        document.getElementById('item-file-selected').classList.remove('hidden');
        document.getElementById('item-file-name').textContent = file.name;
        document.getElementById('item-file-zone').classList.remove('border-gray-300');
        document.getElementById('item-file-zone').classList.add('border-blue-400', 'bg-blue-50');
    }

    function resetUploadDisplay() {
        document.getElementById('item-file-label').classList.remove('hidden');
        document.getElementById('item-file-selected').classList.add('hidden');
        document.getElementById('item-file-name').textContent = '';
        document.getElementById('item-file-zone').classList.add('border-gray-300');
        document.getElementById('item-file-zone').classList.remove('border-blue-400', 'bg-blue-50');
    }

    function showModalErrors(containerId, errors) {
        const el = document.getElementById(containerId);
        const msgs = Object.values(errors).flat();
        el.innerHTML = '<ul class="list-disc list-inside space-y-1">' + msgs.map(m => `<li>${m}</li>`).join('') + '</ul>';
        el.classList.remove('hidden');
    }

    function clearModalErrors(containerId) {
        const el = document.getElementById(containerId);
        el.innerHTML = '';
        el.classList.add('hidden');
    }

    async function submitModalForm(formId, errorContainerId) {
        const form = document.getElementById(formId);
        const btn = form.querySelector('[type=submit]');
        btn.disabled = true;
        clearModalErrors(errorContainerId);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });

            if (res.status === 422) {
                const data = await res.json();
                showModalErrors(errorContainerId, data.errors ?? { error: [data.message] });
                return;
            }

            window.location.reload();
        } catch {
            showModalErrors(errorContainerId, { error: ['Terjadi kesalahan koneksi. Coba lagi.'] });
        } finally {
            btn.disabled = false;
        }
    }

    document.getElementById('session-form').addEventListener('submit', e => {
        e.preventDefault();
        submitModalForm('session-form', 'session-errors');
    });

    document.getElementById('item-form').addEventListener('submit', e => {
        e.preventDefault();
        submitModalForm('item-form', 'item-errors');
    });

    function closeModal(modalId, errorContainerId) {
        document.getElementById(modalId).classList.add('hidden');
        if (errorContainerId) clearModalErrors(errorContainerId);
    }

    function openAddSessionModal() {
        document.getElementById('session-modal-title').textContent = 'Tambah Sesi Pertemuan';
        document.getElementById('session-form').action = '{{ route("curriculums.sessions.store", $curriculum) }}';
        document.getElementById('session-method').value = 'POST';
        document.getElementById('session-form').reset();
        document.getElementById('session-number').value = nextSessionNumber;
        clearModalErrors('session-errors');
        document.getElementById('session-modal').classList.remove('hidden');
    }

    function editSession(id, session) {
        document.getElementById('session-modal-title').textContent = 'Edit Sesi Pertemuan';
        document.getElementById('session-form').action = '{{ url("curriculum-sessions") }}/' + id;
        document.getElementById('session-method').value = 'PUT';
        document.getElementById('session-number').value = session.session_number;
        document.getElementById('session-title').value = session.session_title;
        document.getElementById('session-description').value = session.description || '';
        document.getElementById('session-sort-order').value = session.sort_order;
        document.getElementById('session-active').checked = !!session.is_active;
        clearModalErrors('session-errors');
        document.getElementById('session-modal').classList.remove('hidden');
    }

    function onItemTypeChange(type) {
        document.getElementById('item-value-file').classList.toggle('hidden', type !== 'file');
        document.getElementById('item-value-url').classList.toggle('hidden', type !== 'external_link');
        document.getElementById('item-value-interactive').classList.toggle('hidden', type !== 'interactive');
    }

    function openAddItemModal(sessionId) {
        document.getElementById('item-modal-title').textContent = 'Tambah Materi';
        document.getElementById('item-form').action = '{{ url("curriculum-sessions") }}/' + sessionId + '/items';
        document.getElementById('item-method').value = 'POST';
        document.getElementById('item-form').reset();
        document.getElementById('item-sequence').value = (sessionItemCounts[sessionId] ?? 0) + 1;
        document.getElementById('item-current-file').classList.add('hidden');
        resetUploadDisplay();
        clearModalErrors('item-errors');
        onItemTypeChange('file');
        document.getElementById('item-modal').classList.remove('hidden');
    }

    function editItem(id, item) {
        document.getElementById('item-modal-title').textContent = 'Edit Materi';
        document.getElementById('item-form').action = '{{ url("curriculum-items") }}/' + id;
        document.getElementById('item-method').value = 'PUT';
        document.getElementById('item-sequence').value = item.sequence_number;
        document.getElementById('item-type').value = item.material_type;
        document.getElementById('item-name').value = item.item_name;
        document.getElementById('item-value').value = item.material_value || '';
        document.getElementById('item-notes').value = item.notes || '';
        document.getElementById('item-active').checked = !!item.is_active;
        clearModalErrors('item-errors');
        onItemTypeChange(item.material_type);
        resetUploadDisplay();
        if (item.material_type === 'file' && item.material_value) {
            var p = document.getElementById('item-current-file');
            p.textContent = 'File saat ini: ' + item.material_value.split('/').pop();
            p.classList.remove('hidden');
        } else {
            document.getElementById('item-current-file').classList.add('hidden');
        }
        document.getElementById('item-modal').classList.remove('hidden');
    }
</script>
@endsection
