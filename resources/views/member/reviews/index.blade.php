@extends('layouts.dashboard')

@section('title', 'Review Program - MINDSIA')
@section('header_title', 'Review Program')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <div>
        <h2 class="text-xl font-bold font-jakarta text-gray-900">Review Program</h2>
        <p class="text-sm text-gray-500 mt-0.5">Berikan ulasan untuk program yang telah Anda selesaikan.</p>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium">
        <span class="material-symbols-outlined text-green-500" style="font-size:18px">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if($registrations->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 flex flex-col items-center justify-center text-center">
        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-[#5586DB] text-3xl">rate_review</span>
        </div>
        <h3 class="text-base font-bold text-gray-900 font-jakarta mb-1">Belum Ada Program Selesai</h3>
        <p class="text-sm text-gray-500 max-w-xs mx-auto">Review tersedia setelah program Anda dinyatakan lulus.</p>
    </div>

    @else
    @foreach($registrations as $reg)
    @php $review = $reg->review; @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Program header --}}
        <div class="flex items-start gap-3 p-5 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-[#5586DB]/10 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-[#5586DB]" style="font-size:20px">local_library</span>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-bold text-gray-900 font-jakarta leading-tight">{{ $reg->program->program_name ?? '-' }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Selesai {{ $reg->updated_at?->translatedFormat('M Y') }}
                    &middot;
                    <span class="{{ $reg->graduation_status === 'LULUS' ? 'text-green-600' : 'text-red-500' }} font-semibold">{{ $reg->graduation_status }}</span>
                </p>
            </div>
        </div>

        <div class="p-5">
            @if($review)
            {{-- Display existing review --}}
            <div class="{{ $review->is_approved ? '' : 'mb-5' }}">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                        <span class="material-symbols-outlined {{ $i <= $review->rating ? 'text-amber-400' : 'text-gray-200' }}" style="font-size:18px">star</span>
                        @endfor
                        <span class="text-xs text-gray-500 ml-1.5">{{ $review->rating }}/5</span>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-md border font-semibold
                        {{ $review->is_approved ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-600 border-amber-200' }}">
                        {{ $review->is_approved ? 'Disetujui' : 'Menunggu Persetujuan' }}
                    </span>
                </div>
                @if($review->review)
                <p class="text-sm text-gray-700 leading-relaxed mt-2">{{ $review->review }}</p>
                @endif
            </div>

            @unless($review->is_approved)
            <form method="POST" action="{{ route('member.reviews.update', $review) }}" class="border-t border-gray-100 pt-4 space-y-3">
                @csrf @method('PUT')
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1.5">Program</p>
                    <div class="text-sm font-medium text-gray-800 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl">
                        {{ $reg->program->program_name ?? '-' }}
                    </div>
                </div>
                @include('member.reviews._rating-input', ['currentRating' => $review->rating])
                <textarea name="review" rows="3"
                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#5586DB]/30 focus:border-[#5586DB] resize-none"
                    placeholder="Tulis ulasan Anda... (opsional, min. 10 karakter)">{{ old('review', $review->review) }}</textarea>
                <button type="submit" class="px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Perbarui Review
                </button>
            </form>
            @endunless

            @else
            {{-- New review form --}}
            <form method="POST" action="{{ route('member.reviews.store') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="member_registration_id" value="{{ $reg->id }}">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-1.5">Program</p>
                    <div class="text-sm font-medium text-gray-800 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl">
                        {{ $reg->program->program_name ?? '-' }}
                    </div>
                </div>
                @include('member.reviews._rating-input', ['currentRating' => 0])
                <textarea name="review" rows="3"
                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#5586DB]/30 focus:border-[#5586DB] resize-none"
                    placeholder="Tulis ulasan Anda... (opsional, min. 10 karakter)">{{ old('review') }}</textarea>
                @error('rating')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                @error('review')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                <button type="submit" class="px-4 py-2 text-sm font-semibold bg-[#5586DB] text-white rounded-lg hover:bg-[#4370BC] transition-colors">
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
