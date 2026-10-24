@props(['name', 'value' => '', 'rows' => 4])

@php
    $editorId = 'rich-'.$name.'-'.uniqid();
    $initial = old($name, $value);
@endphp

<div data-rich-editor class="rounded-lg overflow-hidden"
     style="border: 1px solid var(--dash-input-border); background-color: var(--dash-input-bg);">
    <div class="flex flex-wrap items-center gap-1 px-2 py-1.5"
         style="border-bottom: 1px solid var(--dash-input-border); background-color: var(--dash-nav-hover-bg);">
        @foreach ([
            ['bold', 'format_bold', 'Tebal'],
            ['italic', 'format_italic', 'Miring'],
            ['underline', 'format_underlined', 'Garis bawah'],
            ['sep', '', ''],
            ['insertUnorderedList', 'format_list_bulleted', 'Daftar poin'],
            ['insertOrderedList', 'format_list_numbered', 'Daftar nomor'],
            ['sep', '', ''],
            ['justifyLeft', 'format_align_left', 'Rata kiri'],
            ['justifyCenter', 'format_align_center', 'Rata tengah'],
            ['justifyRight', 'format_align_right', 'Rata kanan'],
            ['justifyFull', 'format_align_justify', 'Rata kiri-kanan'],
        ] as [$cmd, $icon, $label])
            @if ($cmd === 'sep')
                <span class="w-px h-5 mx-0.5" style="background-color: var(--dash-input-border);"></span>
            @else
                <button type="button" data-cmd="{{ $cmd }}" title="{{ $label }}" aria-label="{{ $label }}"
                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                        style="color: var(--dash-text-body);">
                    <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
                </button>
            @endif
        @endforeach
    </div>

    <div data-rich-content contenteditable="true"
         class="rich-content px-4 py-2.5 text-sm focus:outline-none"
         style="color: var(--dash-text-heading); min-height: {{ $rows * 1.75 }}rem;">{!! clean($initial) !!}</div>

    <textarea data-rich-input name="{{ $name }}" class="hidden">{{ $initial }}</textarea>
</div>
