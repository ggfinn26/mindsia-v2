@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">{{ $curriculum->curriculum_name }}</h1>
        <p class="text-[color:var(--dash-text-muted)] mt-1">Kelas: {{ $classroom->class_name }}</p>
    </div>

    @foreach($sessions as $session)
    <div class="mb-6 bg-[var(--dash-card-bg)] shadow-sm rounded-sm border border-[color:var(--dash-card-border)] p-6">
        <h2 class="text-lg font-bold text-[color:var(--dash-text-heading)] mb-4">{{ $session->session_title }}</h2>
        <div class="space-y-2">
            @foreach($session->items as $item)
            <div class="flex items-center justify-between p-3 bg-[var(--dash-nav-hover-bg)] rounded">
                <span class="text-sm text-[color:var(--dash-text-body)]">{{ $item->item_name }}</span>
                <a href="{{ route('class-curriculum.show', [$classroom, $item]) }}"
                   class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                    Lihat &rarr;
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
