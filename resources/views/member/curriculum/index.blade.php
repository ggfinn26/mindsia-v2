@extends('layouts.dashboard')

@section('title', 'Materi Kurikulum - MINDSIA')
@section('header_title', 'Materi Kurikulum')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Portal Siswa</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Materi Kurikulum</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Akses materi belajar dan latihan interaktif dari kelas aktif Anda.
        </p>
    </div>

    @if ($memberClasses->isEmpty())
        {{-- Empty State Card --}}
        <div class="rounded-2xl p-12 flex flex-col items-center justify-center text-center shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="h-16 w-16 rounded-full flex items-center justify-center mb-4"
                 style="background-color: var(--dash-status-info-bg);">
                <span class="material-symbols-outlined text-3xl" style="color: var(--dash-primary);">menu_book</span>
            </div>
            <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Belum Ada Kurikulum</h3>
            <p class="text-sm max-w-sm mx-auto mb-6" style="color: var(--dash-text-muted);">
                Materi akan muncul setelah kelas Anda memiliki kurikulum aktif.
            </p>
            <a href="{{ route('member.dashboard') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Dashboard
            </a>
        </div>
    @else
        <div class="space-y-6" x-data="{}">
            @foreach ($memberClasses as $mc)
                @php
                    $classroom  = $mc->classRoom;
                    $curriculum = $classroom->program->curriculum;
                @endphp
                <div class="rounded-2xl shadow-sm overflow-hidden"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    {{-- Class header --}}
                    <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2"
                         style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider mb-0.5"
                               style="color: var(--dash-primary-text);">{{ $classroom->program->program_name }}</p>
                            <h3 class="font-jakarta text-base font-bold"
                                style="color: var(--dash-text-heading);">{{ $classroom->class_name }}</h3>
                        </div>
                        <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-md self-start sm:self-auto"
                              style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                            {{ $curriculum->sessions->count() }} sesi
                        </span>
                    </div>

                    @if ($curriculum->sessions->isEmpty())
                        <div class="px-6 py-8 text-center text-sm" style="color: var(--dash-text-faint);">
                            Belum ada sesi kurikulum.
                        </div>
                    @else
                        <div>
                            @foreach ($curriculum->sessions as $index => $session)
                                <div x-data="{ open: false }" style="{{ $index < $curriculum->sessions->count() - 1 ? 'border-bottom: 1px solid var(--dash-card-border);' : '' }}">
                                    {{-- Session row --}}
                                    <button type="button" @click="open = !open"
                                        class="w-full flex items-center justify-between px-6 py-4 transition-colors text-left cursor-pointer"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='transparent'">
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-full text-xs font-bold flex items-center justify-center shrink-0"
                                                  style="background-color: var(--dash-status-info-bg); color: var(--dash-status-info-text);">
                                                {{ $session->session_number }}
                                            </span>
                                            <div>
                                                <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                                                    {{ $session->session_title }}
                                                </p>
                                                @if ($session->description)
                                                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                                        {{ $session->description }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0 ml-4">
                                            <span class="text-xs font-medium" style="color: var(--dash-text-muted);">
                                                {{ $session->items->count() }} materi
                                            </span>
                                            <span class="material-symbols-outlined text-sm transition-transform"
                                                  :class="open ? 'rotate-180' : ''"
                                                  style="color: var(--dash-text-faint);">expand_more</span>
                                        </div>
                                    </button>

                                    {{-- Items --}}
                                    <div x-show="open" style="display:none; border-top: 1px solid var(--dash-card-border); background-color: var(--dash-page-bg);">
                                        @if ($session->items->isEmpty())
                                            <p class="px-6 py-4 text-xs italic" style="color: var(--dash-text-faint);">Belum ada materi.</p>
                                        @else
                                            <div>
                                                @foreach ($session->items as $itemIdx => $item)
                                                    <div class="px-6 py-3 flex items-center justify-between gap-4"
                                                         style="{{ $itemIdx < $session->items->count() - 1 ? 'border-bottom: 1px solid var(--dash-card-border);' : '' }}">
                                                        <div class="flex items-center gap-3">
                                                            @php
                                                                $icon = match($item->material_type) {
                                                                    'interactive'   => 'quiz',
                                                                    'external_link' => 'link',
                                                                    default         => 'description',
                                                                };
                                                            @endphp
                                                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0"
                                                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                                                <span class="material-symbols-outlined text-[18px]"
                                                                      style="color: var(--dash-primary);">{{ $icon }}</span>
                                                            </div>
                                                            <div>
                                                                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                                                                    {{ $item->item_name }}
                                                                </p>
                                                                @if ($item->notes)
                                                                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                                                        {{ $item->notes }}
                                                                    </p>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="shrink-0">
                                                            @if ($item->material_type === 'interactive')
                                                                <a href="{{ route('member.curriculum.interactive.show', [$classroom, $item]) }}"
                                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white shadow-sm transition-opacity hover:opacity-90"
                                                                    style="background-color: var(--dash-primary);">
                                                                    <span class="material-symbols-outlined text-[15px]">play_circle</span>
                                                                    Latihan
                                                                </a>
                                                            @elseif ($item->material_type === 'external_link')
                                                                <a href="{{ $item->material_value }}" target="_blank" rel="noopener"
                                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                                                                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);"
                                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                                    <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                                                                    Buka Link
                                                                </a>
                                                            @elseif ($item->material_type === 'file')
                                                                <a href="{{ route('member.curriculum.view', [$classroom, $item]) }}"
                                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
                                                                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);"
                                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                                                                    Lihat
                                                                </a>
                                                            @endif
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
            @endforeach
        </div>
    @endif
</div>
@endsection
