@extends('layouts.dashboard')

@section('title', $item->item_name . ' - Latihan Interaktif')
@section('header_title', 'Latihan Interaktif')

@php
/* Kumpulkan semua teks soal → base64, tidak tampil sebagai text node di DOM */
$blocksData = [];
foreach ($item->interactiveBlocks as $bi => $block) {
    $qs = [];
    foreach ($block->questions as $qi => $question) {
        $opts = [];
        foreach (['A', 'B', 'C', 'D'] as $opt) {
            $val = $question->{'option_' . strtolower($opt)};
            if ($val) {
                $opts[$opt] = base64_encode($val);
            }
        }
        $qs[] = [
            'id'   => $question->id,
            'num'  => $qi + 1,
            'text' => base64_encode($question->question_text ?? ''),
            'opts' => $opts,
        ];
    }
    $blocksData[$bi] = [
        'instruction' => base64_encode($block->instruction_text ?? ''),
        'questions'   => $qs,
    ];
}
@endphp

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6" id="quiz-root">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('member.curriculum.index') }}"
               class="dash-back flex items-center justify-center w-9 h-9 rounded-lg transition-colors cursor-pointer shrink-0"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
               title="Kembali ke Kurikulum">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-0.5"
                   style="color: var(--dash-primary-text);">Latihan Interaktif</p>
                <h1 class="font-jakarta text-xl sm:text-2xl font-bold tracking-tight"
                    style="color: var(--dash-text-heading);">{{ $item->item_name }}</h1>
            </div>
        </div>
        <a href="{{ route('member.curriculum.interactive.history', [$classroom, $item]) }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold transition-colors cursor-pointer self-start sm:self-auto shadow-sm"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-primary);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[16px]">history</span>
            Riwayat Latihan
        </a>
    </div>

    @if ($item->interactiveBlocks->isEmpty())
        <div class="rounded-2xl p-12 text-center shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
                  style="color: var(--dash-text-faint);">quiz</span>
            <h3 class="text-base font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Belum Ada Konten</h3>
            <p class="text-sm" style="color: var(--dash-text-muted);">Modul ini belum memiliki soal atau blok interaktif.</p>
        </div>
    @else
        {{-- Block navigation --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            @foreach ($item->interactiveBlocks as $i => $block)
                <button type="button"
                    class="block-nav-btn px-4 py-1.5 rounded-lg text-xs font-semibold flex-shrink-0 transition-colors cursor-pointer shadow-sm"
                    style="{{ $i === 0 ? 'background-color: var(--dash-primary); color: #ffffff; border: 1px solid var(--dash-primary);' : 'background-color: var(--dash-card-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);' }}"
                    data-block="{{ $i }}">Blok {{ $i + 1 }}</button>
            @endforeach
        </div>

        <form id="practice-form" action="{{ route('member.curriculum.interactive.submit', [$classroom, $item]) }}" method="POST">
            @csrf

            @foreach ($item->interactiveBlocks as $i => $block)
                <div class="block-panel space-y-6 {{ $i > 0 ? 'hidden' : '' }}" data-block="{{ $i }}">

                    {{-- Image --}}
                    @if ($block->image)
                        <div class="rounded-2xl overflow-hidden shadow-sm p-2 flex items-center justify-center"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                            <img src="{{ Storage::url($block->image->file_path) }}"
                                alt="{{ $block->image->original_name }}"
                                class="w-full object-contain max-h-80 rounded-xl"
                                oncontextmenu="return false" draggable="false">
                        </div>
                    @endif

                    {{-- Instruction text: canvas, bukan teks DOM --}}
                    @if ($block->instruction_text)
                        <div class="p-4 rounded-2xl shadow-sm"
                             style="background-color: var(--dash-status-warning-bg); border: 1px solid var(--dash-card-border);">
                            <canvas class="instr-canvas w-full block" data-block="{{ $i }}"></canvas>
                        </div>
                    @endif

                    {{-- Audio --}}
                    @if ($block->audio)
                        <div class="p-4 rounded-2xl shadow-sm flex items-center gap-3"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                                 style="background-color: var(--dash-status-info-bg);">
                                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">volume_up</span>
                            </div>
                            <audio controls class="flex-1" controlsList="nodownload">
                                <source src="{{ Storage::url($block->audio->file_path) }}">
                            </audio>
                        </div>
                    @endif

                    {{-- Questions: teks diganti canvas --}}
                    @forelse ($block->questions as $qi => $question)
                        <div class="rounded-2xl p-5 shadow-sm space-y-4"
                             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                            <div class="flex items-center justify-between pb-2"
                                 style="border-bottom: 1px solid var(--dash-card-border);">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-md"
                                      style="background-color: var(--dash-status-info-bg); color: var(--dash-status-info-text);">
                                    Soal {{ $qi + 1 }}
                                </span>
                            </div>

                            {{-- Question text: canvas --}}
                            <canvas class="q-canvas w-full block"
                                data-block="{{ $i }}"
                                data-qidx="{{ $qi }}"></canvas>

                            <div class="space-y-2 pt-2">
                                @foreach (['A', 'B', 'C', 'D'] as $opt)
                                    @php $val = $question->{'option_' . strtolower($opt)}; @endphp
                                    @if ($val)
                                        <label class="flex items-center gap-3 p-3 rounded-xl cursor-pointer transition-colors"
                                               style="background-color: var(--dash-page-bg); border: 1px solid var(--dash-card-border);"
                                               onmouseover="this.style.borderColor='var(--dash-primary)'"
                                               onmouseout="if(!this.querySelector('input').checked){this.style.borderColor='var(--dash-card-border)'}">
                                            <input type="radio"
                                                name="answers[{{ $question->id }}]"
                                                value="{{ $opt }}"
                                                class="shrink-0 w-4 h-4 cursor-pointer"
                                                style="accent-color: var(--dash-primary);"
                                                onchange="document.querySelectorAll('input[name=\'answers[{{ $question->id }}]\']').forEach(r => r.closest('label').style.borderColor = r.checked ? 'var(--dash-primary)' : 'var(--dash-card-border)')">
                                            {{-- Option text: canvas --}}
                                            <canvas class="opt-canvas block flex-1"
                                                data-block="{{ $i }}"
                                                data-qidx="{{ $qi }}"
                                                data-opt="{{ $opt }}"></canvas>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-sm italic" style="color: var(--dash-text-faint);">Blok ini tidak memiliki soal.</p>
                    @endforelse

                    {{-- Navigation --}}
                    <div class="flex items-center justify-between pt-2">
                        @if ($i > 0)
                            <button type="button"
                                class="btn-prev inline-flex items-center gap-1.5 px-4 py-2 font-semibold rounded-lg text-xs transition-colors cursor-pointer shadow-sm"
                                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                                data-prev="{{ $i - 1 }}">
                                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                                Sebelumnya
                            </button>
                        @else
                            <div></div>
                        @endif

                        @if ($i < $item->interactiveBlocks->count() - 1)
                            <button type="button"
                                class="btn-next inline-flex items-center gap-1.5 px-4 py-2 font-semibold rounded-lg text-white text-xs transition-opacity hover:opacity-90 shadow-sm cursor-pointer"
                                style="background-color: var(--dash-primary);"
                                data-next="{{ $i + 1 }}">
                                Selanjutnya
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </button>
                        @else
                            <button type="button"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 font-semibold rounded-lg text-white text-xs transition-opacity hover:opacity-90 shadow-sm cursor-pointer"
                                style="background-color: var(--dash-status-success-text);"
                                onclick="document.getElementById('confirm-modal').classList.remove('hidden')">
                                <span class="material-symbols-outlined text-[16px]">send</span>
                                Selesai & Kirim Jawaban
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Modal Konfirmasi --}}
            <div id="confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="document.getElementById('confirm-modal').classList.add('hidden')"></div>
                <div class="relative rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center transform transition-all z-10"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4"
                         style="background-color: var(--dash-status-success-bg);">
                        <span class="material-symbols-outlined text-2xl" style="color: var(--dash-status-success-text);">check_circle</span>
                    </div>
                    <h3 class="text-base font-bold font-jakarta mb-2" style="color: var(--dash-text-heading);">Selesaikan Latihan?</h3>
                    <p class="text-xs mb-6" style="color: var(--dash-text-muted);">Apakah Anda yakin ingin mengirim semua jawaban sekarang? Anda tidak dapat mengulangi jawaban setelah dikirim.</p>
                    <div class="flex gap-3 justify-center">
                        <button type="button" 
                            class="px-4 py-2 font-semibold rounded-lg text-xs transition-colors w-full cursor-pointer"
                            style="background-color: var(--dash-page-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-page-bg)'"
                            onclick="document.getElementById('confirm-modal').classList.add('hidden')">
                            Batal
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 font-semibold rounded-lg text-white text-xs transition-opacity hover:opacity-90 w-full shadow-sm cursor-pointer"
                            style="background-color: var(--dash-status-success-text);">
                            Ya, Kirim
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

<script>
(function () {
    /* ── Data soal (base64, tidak terbaca langsung di DOM) ── */
    const BD = @json($blocksData);

    /* ── Anti-copy ── */
    const root = document.getElementById('quiz-root');
    if (root) {
        root.style.userSelect = 'none';
        root.style.webkitUserSelect = 'none';
    }
    document.addEventListener('copy',        e => e.preventDefault());
    document.addEventListener('cut',         e => e.preventDefault());
    document.addEventListener('selectstart', e => { if (root && root.contains(e.target)) e.preventDefault(); });
    document.addEventListener('contextmenu', e => { if (root && root.contains(e.target)) e.preventDefault(); });
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && ['c','x','a','u'].includes(e.key.toLowerCase())) {
            e.preventDefault();
        }
    });

    function getThemeTextColor() {
        return document.documentElement.classList.contains('mode-dark') ? '#f1f5f9' : '#1e293b';
    }
    function getThemeSubtextColor() {
        return document.documentElement.classList.contains('mode-dark') ? '#cbd5e1' : '#374151';
    }
    function getThemeWarningColor() {
        return document.documentElement.classList.contains('mode-dark') ? '#fef08a' : '#78350f';
    }

    /* ── Canvas text renderer ── */
    function drawText(canvas, text, opts = {}) {
        const {
            fontSize   = 13,
            color      = getThemeTextColor(),
            bold       = false,
            lineHeight = 1.65,
            padX       = 2,
        } = opts;

        const dpr = window.devicePixelRatio || 1;
        const W   = canvas.parentElement ? canvas.parentElement.clientWidth - padX * 2 : 300;
        if (W <= 0) return; /* hidden — akan digambar ulang saat ditampilkan */

        const fontStr = `${bold ? '600 ' : ''}${fontSize}px Inter, system-ui, sans-serif`;
        const lh      = fontSize * lineHeight;

        /* Hitung wrapping */
        const tmp = document.createElement('canvas').getContext('2d');
        tmp.font  = fontStr;
        const words = text.split(' ');
        const lines = [];
        let line    = '';
        for (const w of words) {
            const test = line ? line + ' ' + w : w;
            if (tmp.measureText(test).width > W) {
                if (line) lines.push(line);
                line = w;
            } else {
                line = test;
            }
        }
        if (line) lines.push(line);

        const H = Math.ceil(lines.length * lh) + 8;

        canvas.width  = W * dpr;
        canvas.height = H * dpr;
        canvas.style.width  = W + 'px';
        canvas.style.height = H + 'px';

        const ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);
        ctx.font         = fontStr;
        ctx.fillStyle    = color;
        ctx.textBaseline = 'top';
        lines.forEach((l, i) => ctx.fillText(l, padX, i * lh + 4));
    }

    /* ── Render satu blok ── */
    function renderBlock(bi) {
        const data = BD[bi];
        if (!data) return;

        /* Instruction */
        document.querySelectorAll(`.instr-canvas[data-block="${bi}"]`).forEach(c => {
            const text = data.instruction ? atob(data.instruction) : '';
            if (text) drawText(c, text, { fontSize: 13, color: getThemeWarningColor() });
        });

        /* Questions + options */
        document.querySelectorAll(`.q-canvas[data-block="${bi}"]`).forEach(c => {
            const qi = +c.dataset.qidx;
            const q  = data.questions[qi];
            if (!q) return;
            drawText(c, atob(q.text), { fontSize: 14, bold: false, color: getThemeTextColor() });
        });

        document.querySelectorAll(`.opt-canvas[data-block="${bi}"]`).forEach(c => {
            const qi  = +c.dataset.qidx;
            const opt = c.dataset.opt;
            const q   = data.questions[qi];
            if (!q || !q.opts[opt]) return;
            drawText(c, opt + '. ' + atob(q.opts[opt]), { fontSize: 13, color: getThemeSubtextColor() });
        });
    }

    /* ── Block navigation ── */
    const panels  = document.querySelectorAll('.block-panel');
    const navBtns = document.querySelectorAll('.block-nav-btn');

    function showBlock(idx) {
        panels.forEach((p, i) => p.classList.toggle('hidden', i !== idx));
        navBtns.forEach((b, i) => {
            if (i === idx) {
                b.style.backgroundColor = 'var(--dash-primary)';
                b.style.color = '#ffffff';
                b.style.borderColor = 'var(--dash-primary)';
            } else {
                b.style.backgroundColor = 'var(--dash-card-bg)';
                b.style.color = 'var(--dash-text-muted)';
                b.style.borderColor = 'var(--dash-card-border)';
            }
        });
        /* Render teks saat blok baru ditampilkan (butuh width tersedia) */
        requestAnimationFrame(() => renderBlock(idx));
    }

    navBtns.forEach(b => b.addEventListener('click', () => showBlock(+b.dataset.block)));
    document.querySelectorAll('.btn-next').forEach(b => b.addEventListener('click', () => showBlock(+b.dataset.next)));
    document.querySelectorAll('.btn-prev').forEach(b => b.addEventListener('click', () => showBlock(+b.dataset.prev)));

    /* ── Render blok pertama setelah layout selesai ── */
    requestAnimationFrame(() => renderBlock(0));

    /* ── Redraw saat resize ── */
    window.addEventListener('resize', () => {
        const active = document.querySelector('.block-panel:not(.hidden)');
        if (active) renderBlock(+active.dataset.block);
    });
}());
</script>
@endsection
