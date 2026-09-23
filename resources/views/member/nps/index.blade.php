@extends('layouts.dashboard')

@section('title', 'NPS Response - MINDSIA')
@section('header_title', 'NPS Response')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    <div>
        <h2 class="text-xl font-bold font-jakarta text-gray-900">NPS Response</h2>
        <p class="text-sm text-gray-500 mt-0.5">Seberapa besar kemungkinan Anda merekomendasikan MINDSIA kepada orang lain?</p>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 font-medium">
        <span class="material-symbols-outlined text-green-500" style="font-size:18px">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Submit form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6"
         x-data="{ score: null }">
        <h3 class="text-sm font-bold text-gray-900 font-jakarta mb-4">Beri Penilaian Baru</h3>

        <form method="POST" action="{{ route('member.nps-responses.store') }}" class="space-y-5">
            @csrf

            {{-- Branch: read-only dari cabang member --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Cabang</label>
                @php
                    $selectedBranch = old('branch_id', $memberBranchId);
                    $branchName = $branches->firstWhere('id', $selectedBranch)?->branch_name;
                @endphp
                @if($selectedBranch && $branchName)
                    <input type="hidden" name="branch_id" value="{{ $selectedBranch }}">
                    <div class="text-sm font-medium text-gray-800 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl">
                        {{ $branchName }}
                    </div>
                @else
                    <select name="branch_id" required
                        class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#5586DB]/30 focus:border-[#5586DB] bg-white">
                        <option value="">Pilih cabang...</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                @endif
                @error('branch_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Program yang diambil --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Program</label>
                @if($programs->isEmpty())
                <p class="text-sm text-gray-400 py-2">Belum ada program terdaftar.</p>
                @elseif($programs->count() === 1)
                <input type="hidden" name="program_id" value="{{ $programs->first()->id }}">
                <div class="text-sm font-medium text-gray-800 px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl">
                    {{ $programs->first()->program_name }}
                </div>
                @else
                <select name="program_id"
                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#5586DB]/30 focus:border-[#5586DB] bg-white">
                    <option value="">Pilih program...</option>
                    @foreach($programs as $prog)
                    <option value="{{ $prog->id }}" {{ old('program_id') == $prog->id ? 'selected' : '' }}>
                        {{ $prog->program_name }}
                    </option>
                    @endforeach
                </select>
                @endif
                @error('program_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- NPS Score --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-2">
                    Kemungkinan Merekomendasikan (0–10)
                </label>
                <div class="flex gap-1 flex-wrap">
                    @for($i = 0; $i <= 10; $i++)
                    <button type="button"
                        @click="score = {{ $i }}"
                        :class="score === {{ $i }}
                            ? '{{ $i <= 6 ? 'bg-red-500 border-red-500 text-white' : ($i <= 8 ? 'bg-amber-400 border-amber-400 text-white' : 'bg-green-500 border-green-500 text-white') }}'
                            : 'bg-white border-gray-200 text-gray-600 hover:border-gray-400'"
                        class="w-9 h-9 rounded-lg border text-sm font-bold transition-colors focus:outline-none">
                        {{ $i }}
                    </button>
                    @endfor
                </div>
                <div class="flex justify-between text-[11px] text-gray-400 mt-1.5 px-0.5">
                    <span>Tidak mungkin</span>
                    <span>Sangat mungkin</span>
                </div>
                <input type="hidden" name="score" :value="score">
                @error('score')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Rating (for review) --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Rating Program (1–5)</label>
                @include('member.reviews._rating-input', ['currentRating' => 0])
                @error('rating')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Comment --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Komentar <span class="font-normal text-gray-400">(opsional)</span></label>
                <textarea name="comment" rows="3"
                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#5586DB]/30 focus:border-[#5586DB] resize-none"
                    placeholder="Ceritakan pengalaman Anda...">{{ old('comment') }}</textarea>
                @error('comment')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Review text --}}
            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wide mb-1.5">Review Program <span class="font-normal text-gray-400">(opsional)</span></label>
                <textarea name="review" rows="3"
                    class="w-full text-sm border border-gray-200 rounded-xl px-3 py-2.5 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#5586DB]/30 focus:border-[#5586DB] resize-none"
                    placeholder="Apa yang Anda suka dari program ini?">{{ old('review') }}</textarea>
                @error('review')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-[#5586DB] text-white text-sm font-semibold rounded-xl hover:bg-[#4370BC] transition-colors">
                Kirim Penilaian
            </button>
        </form>
    </div>

    {{-- History --}}
    @if($responses->isNotEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900 font-jakarta">Riwayat Penilaian</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($responses as $resp)
            <div class="flex items-center justify-between px-5 py-3.5">
                <div>
                    <span class="text-sm font-semibold text-gray-800">{{ $resp->program->program_name ?? '-' }}</span>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $resp->branch->branch_name ?? '-' }} &middot; {{ $resp->created_at->translatedFormat('d M Y') }}</p>
                    @if($resp->comment)
                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $resp->comment }}</p>
                    @endif
                </div>
                <span class="text-2xl font-bold ml-4
                    {{ $resp->score <= 6 ? 'text-red-500' : ($resp->score <= 8 ? 'text-amber-500' : 'text-green-600') }}">
                    {{ $resp->score }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
