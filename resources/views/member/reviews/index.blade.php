@extends('layouts.dashboard')

@section('title', 'Review Program - MINDSIA')
@section('header_title', 'Review Program')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Portal Siswa</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Review Program</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Berikan ulasan dan penilaian untuk program yang telah Anda selesaikan.
        </p>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-semibold shadow-sm"
         style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-success-text);">
        <span class="material-symbols-outlined text-[20px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if($registrations->isEmpty())
    {{-- Empty State Card --}}
    <div class="rounded-2xl p-12 flex flex-col items-center justify-center text-center shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4"
             style="background-color: var(--dash-status-info-bg);">
            <span class="material-symbols-outlined text-3xl" style="color: var(--dash-primary);">rate_review</span>
        </div>
        <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Belum Ada Program Selesai</h3>
        <p class="text-sm max-w-sm mx-auto mb-6" style="color: var(--dash-text-muted);">Review program tersedia setelah status kelulusan Anda diperbarui.</p>
        <a href="{{ route('member.dashboard') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shadow-sm"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Dashboard
        </a>
    </div>

    @else
    @foreach($registrations as $reg)
    @php $review = $reg->review; @endphp
    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        {{-- Program header --}}
        <div class="flex items-start gap-3.5 p-5 sm:p-6"
             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background-color: var(--dash-status-info-bg);">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">local_library</span>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold font-jakarta text-base leading-tight" style="color: var(--dash-text-heading);">
                    {{ $reg->program->program_name ?? '-' }}
                </h3>
                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                    Selesai {{ $reg->updated_at?->translatedFormat('M Y') }}
                    &middot;
                    <span class="font-bold" style="color: {{ $reg->graduation_status === 'LULUS' ? 'var(--dash-status-success-text)' : 'var(--dash-status-danger-text)' }};">
                        {{ $reg->graduation_status }}
                    </span>
                </p>
            </div>
        </div>

        <div class="p-5 sm:p-6">
            @if($review)
            {{-- Display existing review --}}
            <div class="{{ $review->is_approved ? '' : 'mb-6' }}">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                        <span class="material-symbols-outlined text-[20px] {{ $i <= $review->rating ? 'text-amber-400' : '' }}"
                              style="{{ $i > $review->rating ? 'color: var(--dash-text-faint);' : '' }}">star</span>
                        @endfor
                        <span class="text-xs font-semibold ml-1.5" style="color: var(--dash-text-muted);">{{ $review->rating }}/5</span>
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-semibold shadow-xs"
                          style="{{ $review->is_approved ? 'background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid var(--dash-card-border);' : 'background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text); border: 1px solid var(--dash-card-border);' }}">
                        <span class="material-symbols-outlined text-[13px]">{{ $review->is_approved ? 'check' : 'schedule' }}</span>
                        {{ $review->is_approved ? 'Disetujui' : 'Menunggu Persetujuan' }}
                    </span>
                </div>
                @if($review->review)
                <p class="text-sm leading-relaxed mt-3" style="color: var(--dash-text-body);">{{ $review->review }}</p>
                @endif
            </div>

            @unless($review->is_approved)
            <form method="POST" action="{{ route('member.reviews.update', $review) }}"
                  class="pt-5 space-y-4" style="border-top: 1px solid var(--dash-card-border);">
                @csrf @method('PUT')
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">Program</p>
                    <div class="text-sm font-semibold px-3.5 py-2.5 rounded-xl font-jakarta"
                         style="background-color: var(--dash-page-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                        {{ $reg->program->program_name ?? '-' }}
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">Rating</p>
                    @include('member.reviews._rating-input', ['currentRating' => $review->rating])
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">Ulasan Anda</p>
                    <textarea name="review" rows="3"
                        class="w-full text-sm rounded-xl px-3.5 py-2.5 resize-none transition-colors focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        placeholder="Tulis ulasan Anda... (opsional, min. 10 karakter)">{{ old('review', $review->review) }}</textarea>
                </div>
                <button type="submit"
                        class="px-5 py-2.5 text-xs font-semibold rounded-lg text-white transition-opacity hover:opacity-90 shadow-sm cursor-pointer"
                        style="background-color: var(--dash-primary);">
                    Perbarui Review
                </button>
            </form>
            @endunless

            @else
            {{-- New review form --}}
            <form method="POST" action="{{ route('member.reviews.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="member_registration_id" value="{{ $reg->id }}">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">Program</p>
                    <div class="text-sm font-semibold px-3.5 py-2.5 rounded-xl font-jakarta"
                         style="background-color: var(--dash-page-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                        {{ $reg->program->program_name ?? '-' }}
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">Rating</p>
                    @include('member.reviews._rating-input', ['currentRating' => 0])
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">Ulasan Anda</p>
                    <textarea name="review" rows="3"
                        class="w-full text-sm rounded-xl px-3.5 py-2.5 resize-none transition-colors focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        placeholder="Tulis ulasan Anda... (opsional, min. 10 karakter)">{{ old('review') }}</textarea>
                </div>
                @error('rating')<p class="text-xs font-medium" style="color: var(--dash-status-danger-text);">{{ $message }}</p>@enderror
                @error('review')<p class="text-xs font-medium" style="color: var(--dash-status-danger-text);">{{ $message }}</p>@enderror
                <button type="submit"
                        class="px-5 py-2.5 text-xs font-semibold rounded-lg text-white transition-opacity hover:opacity-90 shadow-sm cursor-pointer"
                        style="background-color: var(--dash-primary);">
                    Kirim Review
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
    @endif

</div>
@endsection
