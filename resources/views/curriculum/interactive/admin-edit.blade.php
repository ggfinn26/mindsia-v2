@extends('layouts.dashboard')

@section('title', 'Edit Modul Interaktif')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-jakarta">Edit Modul Interaktif</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $item->item_name }}</p>
        </div>
        <button onclick="history.back()" class="px-4 py-2 font-medium rounded-lg transition-colors bg-white border border-gray-100 hover:border-gray-200 text-gray-600 text-sm">← Kembali</button>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 text-sm rounded">
            <p class="font-semibold mb-1">Gagal menyimpan — periksa kembali:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('curriculum-items.interactive.store', $item) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="blocks-container" class="space-y-6">
            @forelse($item->interactiveBlocks as $bi => $block)
                <div class="block-item bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
                    <input type="hidden" name="blocks[{{ $bi }}][sort_order]" value="{{ $block->sort_order }}">
                    @if($block->audio_media_id)
                        <input type="hidden" name="blocks[{{ $bi }}][existing_audio_id]" value="{{ $block->audio_media_id }}">
                    @endif
                    @if($block->image_media_id)
                        <input type="hidden" name="blocks[{{ $bi }}][existing_image_id]" value="{{ $block->image_media_id }}">
                    @endif
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-gray-900 font-jakarta">Blok {{ $bi + 1 }}</h3>
                        <button type="button" class="remove-block text-red-500 hover:text-red-700 text-sm">Hapus Blok</button>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teks Instruksi</label>
                        <textarea name="blocks[{{ $bi }}][instruction_text]" rows="3"
                            class="w-full border border-gray-200 rounded-lg text-sm p-2.5 focus:border-[#5586DB] focus:ring focus:ring-[#5586DB]/20 outline-none transition-all"
                            placeholder="Instruksi / penjelasan untuk blok ini...">{{ $block->instruction_text }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Audio (opsional)</label>
                            @if($block->audio)
                                <p class="text-xs text-gray-500 mb-1">
                                    Saat ini: <span class="font-medium">{{ $block->audio->original_name }}</span>
                                    <audio controls class="mt-1 w-full">
                                        <source src="{{ Storage::url($block->audio->file_path) }}">
                                    </audio>
                                </p>
                            @endif
                            <input type="file" name="blocks[{{ $bi }}][audio_file]"
                                accept="audio/mp3,audio/mpeg,audio/wav,audio/ogg,audio/m4a"
                                class="w-full border border-gray-200 rounded-lg text-sm p-2.5 focus:border-[#5586DB] focus:ring focus:ring-[#5586DB]/20 outline-none transition-all file:mr-2 file:text-xs file:border-0 file:bg-blue-50 file:text-[#4370BC] file:rounded file:px-2 file:py-1" />
                            <p class="text-xs text-gray-400 mt-0.5">MP3/WAV/OGG/M4A, maks 50MB. Kosongkan jika tidak diganti.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar (opsional)</label>
                            @if($block->image)
                                <p class="text-xs text-gray-500 mb-1">Saat ini: <span class="font-medium">{{ $block->image->original_name }}</span></p>
                                <img src="{{ Storage::url($block->image->file_path) }}" class="h-20 rounded mb-1 object-contain border" alt="">
                            @endif
                            <input type="file" name="blocks[{{ $bi }}][image_file]"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                                class="w-full border border-gray-200 rounded-lg text-sm p-2.5 focus:border-[#5586DB] focus:ring focus:ring-[#5586DB]/20 outline-none transition-all file:mr-2 file:text-xs file:border-0 file:bg-blue-50 file:text-[#4370BC] file:rounded file:px-2 file:py-1" />
                            <p class="text-xs text-gray-400 mt-0.5">JPG/PNG/WEBP, maks 10MB. Kosongkan jika tidak diganti.</p>
                        </div>
                    </div>

                    <div class="questions-wrapper space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Soal ({{ $block->questions->count() }})</span>
                            <button type="button" class="add-question text-[#5586DB] hover:text-[#4370BC] text-sm" data-block="{{ $bi }}">+ Tambah Soal</button>
                        </div>

                        @foreach($block->questions as $qi => $q)
                            @php
                                $activeOpts = ['a','b'];
                                if (!empty($q->option_c)) $activeOpts[] = 'c';
                                if (!empty($q->option_d)) $activeOpts[] = 'd';
                            @endphp
                            <div class="question-item border border-gray-100 rounded-lg p-4 bg-gray-50">
                                <input type="hidden" name="blocks[{{ $bi }}][questions][{{ $qi }}][sort_order]" value="{{ $q->sort_order }}">
                                <div class="flex justify-between mb-2">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Soal {{ $qi + 1 }}</span>
                                    <button type="button" class="remove-question text-red-400 hover:text-red-600 text-xs">Hapus</button>
                                </div>
                                <textarea name="blocks[{{ $bi }}][questions][{{ $qi }}][question_text]" rows="2"
                                    class="w-full border border-gray-200 rounded-lg text-sm p-2.5 focus:border-[#5586DB] focus:ring focus:ring-[#5586DB]/20 outline-none transition-all mb-3"
                                    placeholder="Teks pertanyaan (opsional — kosongkan jika langsung pilihan)">{{ $q->question_text }}</textarea>
                                <div class="options-list space-y-2 mb-2">
                                    @foreach(['a','b','c','d'] as $opt)
                                        <label class="option-row flex items-center gap-2 cursor-pointer {{ !in_array($opt, $activeOpts) ? 'hidden' : '' }}">
                                            <input type="radio" name="blocks[{{ $bi }}][questions][{{ $qi }}][correct_option]"
                                                value="{{ strtoupper($opt) }}" @checked($q->correct_option === strtoupper($opt))
                                                class="accent-[#5586DB] shrink-0">
                                            <span class="text-xs font-bold text-gray-500 w-5">{{ strtoupper($opt) }}</span>
                                            <input type="text" name="blocks[{{ $bi }}][questions][{{ $qi }}][option_{{ $opt }}]"
                                                value="{{ $q->{'option_'.$opt} }}"
                                                class="flex-1 border border-gray-200 rounded-lg text-sm p-2 focus:border-[#5586DB] focus:ring focus:ring-[#5586DB]/20 outline-none transition-all"
                                                placeholder="Pilihan {{ strtoupper($opt) }}"
                                                {{ in_array($opt, ['a','b']) ? 'required' : '' }}>
                                            @if (!in_array($opt, ['a','b']))
                                                <button type="button" class="remove-opt text-gray-300 hover:text-red-400 text-xs shrink-0">✕</button>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                                <div class="flex items-center gap-3 mb-3">
                                    <button type="button" class="add-opt text-xs text-[#5586DB] hover:text-[#4370BC]"
                                        {{ count($activeOpts) >= 4 ? 'disabled style=opacity:.4' : '' }}>
                                        + Tambah Opsi
                                    </button>
                                    <span class="text-xs text-gray-400 opt-count">{{ count($activeOpts) }} opsi</span>
                                </div>
                                <input type="text" name="blocks[{{ $bi }}][questions][{{ $qi }}][explanation]"
                                    value="{{ $q->explanation }}"
                                    class="w-full border border-gray-200 rounded-lg text-sm p-2.5 focus:border-[#5586DB] focus:ring focus:ring-[#5586DB]/20 outline-none transition-all"
                                    placeholder="Penjelasan jawaban (opsional)">
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 italic" id="empty-notice">Belum ada blok. Klik "Tambah Blok" untuk memulai.</p>
            @endforelse
        </div>

        <div class="mt-6 flex gap-3">
            <button type="button" id="add-block"
                class="px-4 py-2 font-medium rounded-lg transition-colors bg-white border border-gray-200 hover:border-gray-300 text-gray-700 text-sm">+ Tambah Blok</button>
            <button type="submit"
                class="px-4 py-2 font-medium rounded-lg transition-colors bg-[#5586DB] hover:bg-[#4370BC] text-white text-sm">Simpan Semua</button>
        </div>
    </form>
</div>

<template id="tpl-block">
    <div class="block-item bg-white border border-gray-100 rounded-xl p-6 shadow-sm">
        <input type="hidden" name="__BN__[sort_order]" value="__SORT__">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-900 font-jakarta">Blok __LABEL__</h3>
            <button type="button" class="remove-block text-red-500 hover:text-red-700 text-sm">Hapus Blok</button>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Teks Instruksi</label>
            <textarea name="__BN__[instruction_text]" rows="3"
                class="w-full border border-gray-200 rounded-lg text-sm p-2.5 focus:border-[#5586DB] focus:ring focus:ring-[#5586DB]/20 outline-none transition-all"
                placeholder="Instruksi / penjelasan untuk blok ini..."></textarea>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Audio (opsional)</label>
                <input type="file" name="__BN__[audio_file]"
                    accept="audio/mp3,audio/mpeg,audio/wav,audio/ogg,audio/m4a"
                    class="w-full border border-gray-200 rounded-lg text-sm p-2.5 focus:border-[#5586DB] focus:ring focus:ring-[#5586DB]/20 outline-none transition-all file:mr-2 file:text-xs file:border-0 file:bg-blue-50 file:text-[#4370BC] file:rounded file:px-2 file:py-1" />
                <p class="text-xs text-gray-400 mt-0.5">MP3/WAV/OGG/M4A, maks 50MB.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar (opsional)</label>
                <input type="file" name="__BN__[image_file]"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    class="w-full border border-gray-200 rounded-lg text-sm p-2.5 focus:border-[#5586DB] focus:ring focus:ring-[#5586DB]/20 outline-none transition-all file:mr-2 file:text-xs file:border-0 file:bg-blue-50 file:text-[#4370BC] file:rounded file:px-2 file:py-1" />
                <p class="text-xs text-gray-400 mt-0.5">JPG/PNG/WEBP, maks 10MB.</p>
            </div>
        </div>
        <div class="questions-wrapper space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700">Soal (0)</span>
                <button type="button" class="add-question text-[#5586DB] hover:text-[#4370BC] text-sm">+ Tambah Soal</button>
            </div>
        </div>
    </div>
</template>

<script>
(function () {
    let blockCount = {{ $item->interactiveBlocks->count() }};

    const INPUT_CLS = 'flex-1 border border-gray-200 rounded-lg text-sm p-2 focus:border-[#5586DB] focus:ring focus:ring-[#5586DB]/20 outline-none transition-all';

    function qTemplate(bi, qi) {
        const opts = ['A','B','C','D'];
        const optRows = opts.map((o, i) => {
            const isOptional = i >= 2;
            return `<label class="option-row flex items-center gap-2 cursor-pointer ${isOptional ? 'hidden' : ''}">
                <input type="radio" name="blocks[${bi}][questions][${qi}][correct_option]" value="${o}" class="accent-[#5586DB] shrink-0">
                <span class="text-xs font-bold text-gray-500 w-5">${o}</span>
                <input type="text" name="blocks[${bi}][questions][${qi}][option_${o.toLowerCase()}]"
                    class="${INPUT_CLS}" placeholder="Pilihan ${o}" ${isOptional ? '' : 'required'}>
                ${isOptional ? '<button type="button" class="remove-opt text-gray-300 hover:text-red-400 text-xs shrink-0">✕</button>' : ''}
            </label>`;
        }).join('');

        return `<div class="question-item border border-gray-100 rounded-lg p-4 bg-gray-50">
            <input type="hidden" name="blocks[${bi}][questions][${qi}][sort_order]" value="${qi}">
            <div class="flex justify-between mb-2">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Soal ${qi + 1}</span>
                <button type="button" class="remove-question text-red-400 hover:text-red-600 text-xs">Hapus</button>
            </div>
            <textarea name="blocks[${bi}][questions][${qi}][question_text]" rows="2"
                class="w-full border border-gray-200 rounded-lg text-sm p-2.5 focus:border-[#5586DB] focus:ring focus:ring-[#5586DB]/20 outline-none transition-all mb-3"
                placeholder="Teks pertanyaan (opsional)"></textarea>
            <div class="options-list space-y-2 mb-2">${optRows}</div>
            <div class="flex items-center gap-3 mb-3">
                <button type="button" class="add-opt text-xs text-[#5586DB] hover:text-[#4370BC]">+ Tambah Opsi</button>
                <span class="text-xs text-gray-400 opt-count">2 opsi</span>
            </div>
            <input type="text" name="blocks[${bi}][questions][${qi}][explanation]"
                class="w-full border border-gray-200 rounded-lg text-sm p-2.5 focus:border-[#5586DB] focus:ring focus:ring-[#5586DB]/20 outline-none transition-all"
                placeholder="Penjelasan jawaban (opsional)">
        </div>`;
    }

    function bindQuestion(qEl) {
        qEl.querySelector('.remove-question')?.addEventListener('click', () => qEl.remove());

        function updateOptState() {
            const visible = qEl.querySelectorAll('.option-row:not(.hidden)').length;
            const countEl = qEl.querySelector('.opt-count');
            const addBtn = qEl.querySelector('.add-opt');
            if (countEl) countEl.textContent = `${visible} opsi`;
            if (addBtn) addBtn.disabled = visible >= 4;
        }

        qEl.querySelector('.add-opt')?.addEventListener('click', function () {
            const rows = qEl.querySelectorAll('.option-row');
            for (const row of rows) {
                if (row.classList.contains('hidden')) {
                    row.classList.remove('hidden');
                    break;
                }
            }
            updateOptState();
        });

        qEl.querySelectorAll('.remove-opt').forEach(btn => {
            btn.addEventListener('click', function () {
                const row = this.closest('.option-row');
                const inp = row.querySelector('input[type=text]');
                if (inp) inp.value = '';
                row.classList.add('hidden');
                updateOptState();
            });
        });

        updateOptState();
    }

    function bindBlock(el, bi) {
        el.querySelector('.remove-block').addEventListener('click', () => el.remove());

        el.querySelectorAll('.question-item').forEach(q => bindQuestion(q));

        const addQBtn = el.querySelector('.add-question');
        addQBtn.addEventListener('click', function () {
            const wrapper = el.querySelector('.questions-wrapper');
            const qi = wrapper.querySelectorAll('.question-item').length;
            wrapper.insertAdjacentHTML('beforeend', qTemplate(bi, qi));
            const newQ = wrapper.querySelector('.question-item:last-child');
            bindQuestion(newQ);
            wrapper.querySelector('.text-sm.font-medium').textContent = `Soal (${qi + 1})`;
        });
    }

    document.querySelectorAll('.block-item').forEach((el, i) => bindBlock(el, i));

    document.getElementById('add-block').addEventListener('click', function () {
        const notice = document.getElementById('empty-notice');
        if (notice) notice.remove();

        const bi = blockCount++;
        const tpl = document.getElementById('tpl-block').content.cloneNode(true);
        const el = tpl.querySelector('.block-item');

        el.innerHTML = el.innerHTML
            .replaceAll('__BN__', `blocks[${bi}]`)
            .replaceAll('__SORT__', bi)
            .replaceAll('__LABEL__', bi + 1);

        document.getElementById('blocks-container').appendChild(el);
        bindBlock(document.getElementById('blocks-container').lastElementChild, bi);
    });
}());
</script>
@endsection
