{{--
    Passage body renderer.
    Variables: $passage (ToeflPassage), $titleColor (Tailwind class, default 'text-green-700')
    Syntax: [U]word[/U] → <u>word</u>, each non-empty line gets a line number.
--}}
@php
    $titleColor ??= 'text-green-700';
    $rawLines = explode("\n", $passage->body_text ?? '');
    $lines = [];
    foreach ($rawLines as $line) {
        if (trim($line) !== '') {
            $lines[] = $line;
        }
    }
@endphp

<p class="text-xs uppercase font-semibold {{ $titleColor }} mb-3 tracking-wide">{{ $passage->title }}</p>

@if ($lines)
    <div class="text-sm text-gray-700 leading-[1.85] font-serif">
        @foreach ($lines as $i => $line)
            <div class="flex gap-2.5 mb-0.5 group">
                <span class="text-[10px] text-gray-300 font-mono w-5 text-right shrink-0 pt-[3px] select-none tabular-nums">
                    {{ $i + 1 }}
                </span>
                <span class="flex-1">{!! preg_replace('/\[U\](.*?)\[\/U\]/s', '<u>$1</u>', e($line)) !!}</span>
            </div>
        @endforeach
    </div>
@else
    <p class="text-sm text-gray-400 italic">Tidak ada teks passage.</p>
@endif
