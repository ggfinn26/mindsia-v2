@extends('layouts.dashboard')

@section('title', 'Kurikulum - ' . $curriculum->curriculum_name)
@section('header_title', 'Kurikulum')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Kurikulum</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">{{ $curriculum->curriculum_name }}</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Kelas: {{ $classroom->class_name }}</p>
    </div>

    @foreach($sessions as $session)
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">{{ $session->session_title }}</h2>
        </div>
        <div class="px-6 py-4 space-y-2">
            @foreach($session->items as $item)
            <div class="flex items-center justify-between p-3 rounded-lg" style="border: 1px solid var(--dash-card-border);"
                 onmouseover="this.style.borderColor='var(--dash-primary)'"
                 onmouseout="this.style.borderColor='var(--dash-card-border)'">
                <span class="text-sm" style="color: var(--dash-text-body);">{{ $item->item_name }}</span>
                <a href="{{ route('class-curriculum.show', [$classroom, $item]) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[14px]">visibility</span>Lihat
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
