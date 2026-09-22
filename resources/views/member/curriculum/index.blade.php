@extends('layouts.dashboard')

@section('title', 'Materi Kurikulum - MINDSIA')

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Materi Kurikulum</h2>
        <p class="text-sm text-gray-500 mt-1">Akses materi belajar dan latihan interaktif dari kelas aktif Anda.</p>
    </div>

    @if ($memberClasses->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 flex flex-col items-center justify-center text-center">
            <div class="h-16 w-16 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[#5586DB] text-3xl">menu_book</span>
            </div>
            <h3 class="text-base font-bold text-gray-900 mb-2">Belum Ada Kurikulum</h3>
            <p class="text-sm text-gray-500 max-w-sm mx-auto">Materi akan muncul setelah kelas Anda memiliki kurikulum aktif.</p>
        </div>
    @else
        <div class="space-y-8" x-data="{}">
            @foreach ($memberClasses as $mc)
                @php
                    $classroom  = $mc->classRoom;
                    $curriculum = $classroom->program->curriculum;
                @endphp
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    {{-- Class header --}}
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-0.5">{{ $classroom->program->program_name }}</p>
                            <h3 class="font-bold text-gray-900">{{ $classroom->class_name }}</h3>
                        </div>
                        <span class="text-xs text-gray-400">{{ $curriculum->sessions->count() }} sesi</span>
                    </div>

                    @if ($curriculum->sessions->isEmpty())
                        <div class="px-6 py-8 text-center text-sm text-gray-400">Belum ada sesi kurikulum.</div>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($curriculum->sessions as $session)
                                <div x-data="{ open: false }">
                                    {{-- Session row --}}
                                    <button type="button" @click="open = !open"
                                        class="w-full flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors text-left">
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center shrink-0">
                                                {{ $session->session_number }}
                                            </span>
                                            <div>
                                                <p class="font-medium text-gray-800 text-sm">{{ $session->session_title }}</p>
                                                @if ($session->description)
                                                    <p class="text-xs text-gray-400 mt-0.5">{{ $session->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 shrink-0 ml-4">
                                            <span class="text-xs text-gray-400">{{ $session->items->count() }} materi</span>
                                            <span class="material-symbols-outlined text-gray-400 text-sm transition-transform"
                                                :class="open ? 'rotate-180' : ''">expand_more</span>
                                        </div>
                                    </button>

                                    {{-- Items --}}
                                    <div x-show="open" style="display:none"
                                        class="border-t border-gray-100 bg-gray-50">
                                        @if ($session->items->isEmpty())
                                            <p class="px-6 py-4 text-xs text-gray-400 italic">Belum ada materi.</p>
                                        @else
                                            <ul class="divide-y divide-gray-100">
                                                @foreach ($session->items as $item)
                                                    <li class="px-6 py-3 flex items-center justify-between gap-4">
                                                        <div class="flex items-center gap-3">
                                                            @php
                                                                $icon = match($item->material_type) {
                                                                    'interactive'   => 'quiz',
                                                                    'external_link' => 'link',
                                                                    default         => 'description',
                                                                };
                                                                $iconColor = $item->material_type === 'interactive'
                                                                    ? 'text-indigo-500'
                                                                    : ($item->material_type === 'external_link' ? 'text-blue-500' : 'text-gray-400');
                                                            @endphp
                                                            <span class="material-symbols-outlined text-base {{ $iconColor }}">{{ $icon }}</span>
                                                            <div>
                                                                <p class="text-sm font-medium text-gray-800">{{ $item->item_name }}</p>
                                                                @if ($item->notes)
                                                                    <p class="text-xs text-gray-400">{{ $item->notes }}</p>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="shrink-0">
                                                            @if ($item->material_type === 'interactive')
                                                                <a href="{{ route('member.curriculum.interactive.show', [$classroom, $item]) }}"
                                                                    class="btn btn-sm bg-indigo-500 hover:bg-indigo-600 text-white text-xs px-3 py-1.5">
                                                                    Latihan
                                                                </a>
                                                            @elseif ($item->material_type === 'external_link')
                                                                <a href="{{ $item->material_value }}" target="_blank" rel="noopener"
                                                                    class="btn btn-sm bg-white border border-gray-200 hover:border-gray-300 text-gray-700 text-xs px-3 py-1.5">
                                                                    Buka Link
                                                                </a>
                                                            @elseif ($item->material_type === 'file')
                                                                <a href="{{ route('member.curriculum.view', [$classroom, $item]) }}"
                                                                    class="btn btn-sm bg-white border border-gray-200 hover:border-gray-300 text-gray-700 text-xs px-3 py-1.5">
                                                                    Lihat
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
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
