@extends('layouts.dashboard')

@section('title', $curriculum->curriculum_name . ' - MINDSIA')
@section('header_title', 'Detail Kurikulum')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
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
                    style="color: var(--dash-text-heading);">{{ $curriculum->curriculum_name }}</h1>
                <p class="mt-0.5 text-sm" style="color: var(--dash-text-muted);">
                    Program: <strong style="color: var(--dash-text-body);">{{ $curriculum->program->program_name }}</strong>
                </p>
            </div>
        </div>
        <a href="{{ route('curriculums.edit', $curriculum) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors shrink-0"
           style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[14px]">edit</span>Edit
        </a>
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
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if($curriculum->description)
        <div class="rounded-2xl p-4 text-sm"
             style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            {{ $curriculum->description }}
        </div>
    @endif

    {{-- Sesi --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">calendar_month</span>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Sesi Pertemuan Mingguan</h2>
            </div>
            <button type="button" onclick="openAddSessionModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[14px]">add</span>Tambah Sesi
            </button>
        </div>

        <div class="p-5">
            @if($curriculum->sessions->isEmpty())
                <p class="text-sm py-8 text-center" style="color: var(--dash-text-muted);">
                    Belum ada sesi. Tambahkan sesi pertemuan mingguan.
                </p>
            @else
                <div class="space-y-6">
                    @foreach($curriculum->sessions->sortBy('sort_order') as $session)
                        <div class="pl-6 pb-6" id="session-{{ $session->id }}"
                             style="border-left: 4px solid var(--dash-primary);">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-base font-bold" style="color: var(--dash-text-heading);">
                                        Minggu {{ $session->session_number }}: {{ $session->session_title }}
                                    </h3>
                                    @if($session->description)
                                        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">{{ $session->description }}</p>
                                    @endif
                                </div>
                                <div class="flex gap-2 shrink-0">
                                    <button type="button" onclick="editSession({{ $session->id }}, {{ json_encode($session) }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                            style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[13px]">edit</span>Edit
                                    </button>
                                    <form action="{{ route('curriculum-sessions.destroy', $session) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus sesi ini beserta semua materi di dalamnya?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                                style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                                                onmouseover="this.style.backgroundColor='#fecaca'"
                                                onmouseout="this.style.backgroundColor='#fee2e2'">
                                            <span class="material-symbols-outlined text-[13px]">delete</span>Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="rounded-xl p-4 mt-3" style="background-color: var(--dash-nav-hover-bg);">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-sm" style="color: var(--dash-text-heading);">
                                        Materi ({{ $session->items->count() }} item)
                                    </h4>
                                    <button type="button" onclick="openAddItemModal({{ $session->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                                            style="background-color: var(--dash-primary);">
                                        <span class="material-symbols-outlined text-[13px]">add</span>Tambah Materi
                                    </button>
                                </div>

                                @if($session->items->isEmpty())
                                    <p class="py-4 text-center text-sm" style="color: var(--dash-text-muted);">
                                        Belum ada materi dalam sesi ini.
                                    </p>
                                @else
                                    <div class="space-y-3">
                                        @foreach($session->items->sortBy('sequence_number') as $item)
                                            <div class="rounded-lg p-3 flex items-start justify-between"
                                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $item->sequence_number }}.</span>
                                                        <h5 class="font-medium text-sm" style="color: var(--dash-text-heading);">{{ $item->item_name }}</h5>
                                                        <span class="px-2 py-0.5 text-xs rounded-full"
                                                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">{{ $item->material_type }}</span>
                                                    </div>
                                                    @if($item->material_type === 'file' && $item->material_value)
                                                        <a href="{{ asset('storage/' . $item->material_value) }}" target="_blank"
                                                           class="text-xs hover:underline mt-1 ml-6 block" style="color: var(--dash-primary);">
                                                            {{ basename($item->material_value) }}
                                                        </a>
                                                    @elseif($item->material_value)
                                                        <p class="text-xs mt-1 ml-6" style="color: var(--dash-text-muted);">{{ $item->material_value }}</p>
                                                    @endif
                                                    @if($item->notes)
                                                        <p class="text-xs mt-1 ml-6" style="color: var(--dash-text-faint);">Catatan: {{ $item->notes }}</p>
                                                    @endif
                                                </div>
                                                <div class="flex gap-2 ml-3 shrink-0">
                                                    @if($item->material_type === 'interactive')
                                                        <a href="{{ route('curriculum-items.interactive.show', $item) }}"
                                                           class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold transition-colors"
                                                           style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text); border: 1px solid var(--dash-card-border);">
                                                            <span class="material-symbols-outlined text-[14px]">edit</span>
                                                            Edit Modul
                                                        </a>
                                                    @else
                                                        <button type="button" onclick="editItem({{ $item->id }}, {{ json_encode($item) }})"
                                                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg transition-colors"
                                                                title="Edit"
                                                                style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                            <span class="material-symbols-outlined text-[13px]">edit</span>
                                                        </button>
                                                    @endif
                                                    <form action="{{ route('curriculum-items.destroy', $item) }}" method="POST" class="inline"
                                                          onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" title="Hapus"
                                                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg transition-colors"
                                                                style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                                                                onmouseover="this.style.backgroundColor='#fecaca'"
                                                                onmouseout="this.style.backgroundColor='#fee2e2'">
                                                            <span class="material-symbols-outlined text-[13px]">delete</span>
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
</div>

{{-- Modal Sesi --}}
<div id="session-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);">
    <div class="rounded-2xl shadow-xl max-w-md w-full"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="text-base font-bold" id="session-modal-title" style="color: var(--dash-text-heading);">Tambah Sesi Pertemuan</h3>
        </div>
        <form id="session-form" method="POST" class="p-6 space-y-4" novalidate>
            @csrf
            <input type="hidden" id="session-method" name="_method" value="POST">
            <div id="session-errors" class="hidden rounded-xl px-3 py-2 text-sm"
                 style="background-color: #fee2e2; border: 1px solid #fca5a5; color: #dc2626;"></div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Minggu ke-</label>
                <input type="number" id="session-number" name="session_number" required min="1"
                       class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Judul Sesi</label>
                <input type="text" id="session-title" name="session_title" required
                       class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Deskripsi (opsional)</label>
                <textarea id="session-description" name="description" rows="2"
                          class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Urutan Tampil</label>
                <input type="number" id="session-sort-order" name="sort_order" value="0" min="0"
                       class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="session-active" name="is_active" value="1" checked class="h-4 w-4 rounded">
                <span class="text-sm font-medium" style="color: var(--dash-text-body);">Aktif</span>
            </label>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('session-modal','session-errors')"
                        class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center justify-center gap-1.5"
                        style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity inline-flex items-center justify-center gap-1.5"
                        style="background-color: var(--dash-primary);"><span class="material-symbols-outlined text-[16px]">save</span>Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Materi --}}
<div id="item-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);">
    <div class="rounded-2xl shadow-xl max-w-md w-full"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <h3 id="item-modal-title" class="text-base font-bold" style="color: var(--dash-text-heading);">Tambah Materi</h3>
        </div>
        <form id="item-form" method="POST" enctype="multipart/form-data" class="p-6 space-y-4" novalidate>
            @csrf
            <input type="hidden" id="item-method" name="_method" value="POST">
            <div id="item-errors" class="hidden rounded-xl px-3 py-2 text-sm"
                 style="background-color: #fee2e2; border: 1px solid #fca5a5; color: #dc2626;"></div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">No. Urutan</label>
                    <input type="number" id="item-sequence" name="sequence_number" required min="1"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Tipe Materi</label>
                    <select id="item-type" name="material_type" required onchange="onItemTypeChange(this.value)"
                            class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        <option value="file">File</option>
                        <option value="external_link">Link Eksternal</option>
                        <option value="interactive">Latihan Interaktif (TOEFL)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Nama Materi</label>
                <input type="text" id="item-name" name="item_name" required
                       class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>
            {{-- File upload --}}
            <div id="item-value-file">
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Upload File</label>
                <div id="item-file-zone"
                     class="rounded-lg p-4 text-center cursor-pointer transition-colors"
                     style="border: 2px dashed var(--dash-card-border); background-color: var(--dash-card-bg);"
                     onclick="document.getElementById('item-file').click()"
                     onkeydown="if(event.key==='Enter'||event.key===' ')document.getElementById('item-file').click()"
                     tabindex="0" role="button" aria-label="Pilih file untuk diupload">
                    <input type="file" id="item-file" name="material_file" class="hidden"
                           accept="application/pdf,video/mp4,video/webm,video/ogg,video/quicktime,image/jpeg,image/png,image/gif,image/webp,audio/mpeg,audio/wav,audio/aac,audio/mp4"
                           onchange="updateUploadDisplay(this)">
                    <div id="item-file-label">
                        <p class="text-sm" style="color: var(--dash-text-muted);">Klik untuk pilih file</p>
                        <p class="text-xs mt-1" style="color: var(--dash-text-faint);">PDF · Video · Gambar · Audio — maks. 50MB</p>
                    </div>
                    <div id="item-file-selected" class="hidden">
                        <p class="text-sm font-medium" id="item-file-name" style="color: var(--dash-primary);"></p>
                        <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Klik untuk ganti file</p>
                    </div>
                </div>
                <p id="item-current-file" class="text-xs mt-1 hidden" style="color: var(--dash-primary);"></p>
            </div>
            {{-- URL --}}
            <div id="item-value-url" class="hidden">
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">URL</label>
                <input type="text" id="item-value" name="material_value" placeholder="https://..."
                       class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>
            {{-- Interactive notice --}}
            <div id="item-value-interactive" class="hidden">
                <p class="text-sm rounded-xl p-3"
                   style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-card-border); color: var(--dash-primary-text);">
                    Setelah disimpan, klik <strong>Edit Modul</strong> untuk menyusun blok &amp; soal latihan.
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Catatan (opsional)</label>
                <input type="text" id="item-notes" name="notes"
                       class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
            </div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="item-active" name="is_active" value="1" checked class="h-4 w-4 rounded">
                <span class="text-sm font-medium" style="color: var(--dash-text-body);">Aktif</span>
            </label>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('item-modal','item-errors')"
                        class="flex-1 px-4 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center justify-center gap-1.5"
                        style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                <button type="submit"
                        class="flex-1 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity inline-flex items-center justify-center gap-1.5"
                        style="background-color: var(--dash-primary);"><span class="material-symbols-outlined text-[16px]">save</span>Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const sessionItemCounts = @json($curriculum->sessions->mapWithKeys(fn ($s) => [$s->id => $s->items->max('sequence_number') ?? 0]));
    const nextSessionNumber = {{ ($curriculum->sessions->max('session_number') ?? 0) + 1 }};

    function updateUploadDisplay(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('item-file-label').classList.add('hidden');
        document.getElementById('item-file-selected').classList.remove('hidden');
        document.getElementById('item-file-name').textContent = file.name;
        const zone = document.getElementById('item-file-zone');
        zone.style.borderColor = 'var(--dash-primary)';
        zone.style.backgroundColor = 'var(--dash-primary-bg)';
    }

    function resetUploadDisplay() {
        document.getElementById('item-file-label').classList.remove('hidden');
        document.getElementById('item-file-selected').classList.add('hidden');
        document.getElementById('item-file-name').textContent = '';
        const zone = document.getElementById('item-file-zone');
        zone.style.borderColor = 'var(--dash-card-border)';
        zone.style.backgroundColor = 'var(--dash-card-bg)';
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
@endpush
@endsection
