@extends('layouts.dashboard')

@section('title', $item->item_name)

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
<div class="max-w-3xl mx-auto px-4 py-8" id="quiz-root">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('member.curriculum.index') }}" class="text-sm text-[color:var(--dash-text-muted)] hover:text-[color:var(--dash-text-body)]">← Kurikulum</a>
            <h1 class="text-xl font-bold text-[color:var(--dash-text-heading)] font-jakarta mt-1">{{ $item->item_name }}</h1>
        </div>
        <a href="{{ route('member.curriculum.interactive.history', [$classroom, $item]) }}"
            class="text-sm text-[#5586DB] hover:underline">Riwayat Latihan</a>
    </div>

    @if ($item->interactiveBlocks->isEmpty())
        <div class="bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl p-8 text-center text-[color:var(--dash-text-faint)]">
            Modul ini belum memiliki konten.
        </div>
    @else
        {{-- Block navigation --}}
        <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-1">
            @foreach ($item->interactiveBlocks as $i => $block)
                <button type="button"
                    class="block-nav-btn px-3 py-1.5 rounded-full text-sm border font-medium flex-shrink-0 transition-colors
                        {{ $i === 0 ? 'bg-[#5586DB] text-white border-[#5586DB]' : 'bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] border-[color:var(--dash-card-border)] hover:border-[#5586DB]' }}"
                    data-block="{{ $i }}">Blok {{ $i + 1 }}</button>
            @endforeach
        </div>

        <form id="practice-form" action="{{ route('member.curriculum.interactive.submit', [$classroom, $item]) }}" method="POST">
            @csrf

            @foreach ($item->interactiveBlocks as $i => $block)
                <div class="block-panel {{ $i > 0 ? 'hidden' : '' }}" data-block="{{ $i }}">

                    {{-- Image --}}
                    @if ($block->image)
                        <div class="mb-4 rounded-xl overflow-hidden border border-[color:var(--dash-card-border)]">
                            <img src="{{ Storage::url($block->image->file_path) }}"
                                alt="{{ $block->image->original_name }}"
                                class="w-full object-contain max-h-80"
                                oncontextmenu="return false" draggable="false">
                        </div>
                    @endif

                    {{-- Instruction text: canvas, bukan teks DOM --}}
                    @if ($block->instruction_text)
                        <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                            <canvas class="instr-canvas w-full block" data-block="{{ $i }}"></canvas>
                        </div>
                    @endif

                    {{-- Audio --}}
                    @if ($block->audio)
                        <div class="mb-4 p-3 bg-[var(--dash-nav-hover-bg)] border border-[color:var(--dash-card-border)] rounded-xl flex items-center gap-3">
                            <span class="material-symbols-outlined text-[#5586DB]">volume_up</span>
                            <audio controls class="flex-1" controlsList="nodownload">
                                <source src="{{ Storage::url($block->audio->file_path) }}">
                            </audio>
                        </div>
                    @endif

                    {{-- Questions: teks diganti canvas --}}
                    @forelse ($block->questions as $qi => $question)
                        <div class="mb-6 bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] rounded-xl p-5">
                            <p class="text-sm text-[color:var(--dash-text-muted)] mb-2">Soal {{ $qi + 1 }}</p>
                            {{-- Question text: canvas --}}
                            <canvas class="q-canvas w-full block mb-4"
                                data-block="{{ $i }}"
                                data-qidx="{{ $qi }}"></canvas>

                            <div class="space-y-2">
                                @foreach (['A', 'B', 'C', 'D'] as $opt)
                                    @php $val = $question->{'option_' . strtolower($opt)}; @endphp
                                    @if ($val)
                                        <label class="flex items-center gap-3 p-3 rounded-xl border border-[color:var(--dash-card-border)] hover:border-blue-300 cursor-pointer transition-colors has-[:checked]:border-[#5586DB] has-[:checked]:bg-blue-50">
                                            <input type="radio"
                                                name="answers[{{ $question->id }}]"
                                                value="{{ $opt }}"
                                                class="shrink-0 accent-[#5586DB]">
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
                        <p class="text-sm text-[color:var(--dash-text-faint)] italic">Blok ini tidak memiliki soal.</p>
                    @endforelse

                    {{-- Navigation --}}
                    <div class="flex justify-between mt-6">
                        @if ($i > 0)
                            <button type="button" class="btn-prev px-4 py-2 font-medium rounded-lg bg-[var(--dash-card-bg)] border border-[color:var(--dash-card-border)] text-[color:var(--dash-text-body)] text-sm transition-colors" data-prev="{{ $i - 1 }}">← Sebelumnya</button>
                        @else
                            <div></div>
                        @endif

                        @if ($i < $item->interactiveBlocks->count() - 1)
                            <button type="button" class="btn-next px-4 py-2 font-medium rounded-lg bg-[#5586DB] text-white text-sm transition-colors" data-next="{{ $i + 1 }}">Selanjutnya →</button>
                        @else
                            <button type="button"
                                class="px-4 py-2 font-medium rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm transition-colors"
                                onclick="document.getElementById('confirm-modal').classList.remove('hidden')">
                                Selesai & Kirim Jawaban
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Modal Konfirmasi --}}
            <div id="confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="document.getElementById('confirm-modal').classList.add('hidden')"></div>
                <div class="relative bg-[var(--dash-card-bg)] rounded-xl shadow-xl border border-[color:var(--dash-card-border)] max-w-sm w-full mx-4 p-6 text-center transform transition-all">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <span class="material-symbols-outlined text-green-600 text-2xl">check_circle</span>
                    </div>
                    <h3 class="text-lg font-semibold text-[color:var(--dash-text-heading)] font-jakarta mb-2">Selesaikan Latihan?</h3>
                    <p class="text-sm text-[color:var(--dash-text-muted)] mb-6">Apakah Anda yakin ingin mengirim semua jawaban sekarang? Anda tidak dapat mengulangi jawaban setelah dikirim.</p>
                    <div class="flex gap-3 justify-center">
                        <button type="button" 
                            class="px-4 py-2 font-medium rounded-lg text-[color:var(--dash-text-body)] bg-[var(--dash-nav-hover-bg)] hover:bg-gray-200 transition-colors w-full"
                            onclick="document.getElementById('confirm-modal').classList.add('hidden')">
                            Batal
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition-colors w-full">
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
    root.style.userSelect = 'none';
    root.style.webkitUserSelect = 'none';
    document.addEventListener('copy',        e => e.preventDefault());
    document.addEventListener('cut',         e => e.preventDefault());
    document.addEventListener('selectstart', e => { if (root.contains(e.target)) e.preventDefault(); });
    document.addEventListener('contextmenu', e => { if (root.contains(e.target)) e.preventDefault(); });
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && ['c','x','a','u'].includes(e.key.toLowerCase())) {
            e.preventDefault();
        }
    });

    /* ── Canvas text renderer ── */
    function drawText(canvas, text, opts = {}) {
        const {
            fontSize   = 13,
            color      = '#111827',
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
            if (text) drawText(c, text, { fontSize: 13, color: '#78350f' });
        });

        /* Questions + options */
        document.querySelectorAll(`.q-canvas[data-block="${bi}"]`).forEach(c => {
            const qi = +c.dataset.qidx;
            const q  = data.questions[qi];
            if (!q) return;
            drawText(c, atob(q.text), { fontSize: 14, bold: false, color: '#111827' });
        });

        document.querySelectorAll(`.opt-canvas[data-block="${bi}"]`).forEach(c => {
            const qi  = +c.dataset.qidx;
            const opt = c.dataset.opt;
            const q   = data.questions[qi];
            if (!q || !q.opts[opt]) return;
            drawText(c, opt + '. ' + atob(q.opts[opt]), { fontSize: 13, color: '#374151' });
        });
    }

    /* ── Block navigation ── */
    const panels  = document.querySelectorAll('.block-panel');
    const navBtns = document.querySelectorAll('.block-nav-btn');

    function showBlock(idx) {
        panels.forEach((p, i) => p.classList.toggle('hidden', i !== idx));
        navBtns.forEach((b, i) => {
            b.classList.toggle('bg-[#5586DB]',    i === idx);
            b.classList.toggle('text-white',       i === idx);
            b.classList.toggle('border-[#5586DB]', i === idx);
            b.classList.toggle('bg-[var(--dash-card-bg)]',         i !== idx);
            b.classList.toggle('text-[color:var(--dash-text-body)]',    i !== idx);
            b.classList.toggle('border-[color:var(--dash-card-border)]',  i !== idx);
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
