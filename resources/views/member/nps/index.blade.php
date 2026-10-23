@extends('layouts.dashboard')

@section('title', 'NPS Response - MINDSIA')
@section('header_title', 'NPS Response')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Portal Siswa</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">NPS Response</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Seberapa besar kemungkinan Anda merekomendasikan MINDSIA kepada orang lain?
        </p>
    </div>

    @if(session('success'))
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-semibold shadow-sm"
         style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-success-text);">
        <span class="material-symbols-outlined text-[20px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Submit form --}}
    <div class="rounded-2xl shadow-sm p-5 sm:p-6"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
         x-data="{ score: null }">
        <h3 class="text-base font-bold font-jakarta mb-4" style="color: var(--dash-text-heading);">Beri Penilaian Baru</h3>

        <form method="POST" action="{{ route('member.nps-responses.store') }}" class="space-y-5">
            @csrf

            {{-- Branch: read-only dari cabang member --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">Cabang</label>
                @php
                    $selectedBranch = old('branch_id', $memberBranchId);
                    $branchName = $branches->firstWhere('id', $selectedBranch)?->branch_name;
                @endphp
                @if($selectedBranch && $branchName)
                    <input type="hidden" name="branch_id" value="{{ $selectedBranch }}">
                    <div class="text-sm font-semibold px-3.5 py-2.5 rounded-xl font-jakarta"
                         style="background-color: var(--dash-page-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                        {{ $branchName }}
                    </div>
                @else
                    <select name="branch_id" required
                        class="w-full text-sm rounded-xl px-3.5 py-2.5 transition-colors focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                        <option value="">Pilih Cabang ...</option>
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                        @endforeach
                    </select>
                @endif
                @error('branch_id')<p class="text-xs font-medium mt-1" style="color: var(--dash-status-danger-text);">{{ $message }}</p>@enderror
            </div>

            {{-- Program yang diambil --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">Program</label>
                @if($programs->isEmpty())
                <p class="text-sm py-2" style="color: var(--dash-text-faint);">Belum ada program terdaftar.</p>
                @elseif($programs->count() === 1)
                <input type="hidden" name="program_id" value="{{ $programs->first()->id }}">
                <div class="text-sm font-semibold px-3.5 py-2.5 rounded-xl font-jakarta"
                     style="background-color: var(--dash-page-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                    {{ $programs->first()->program_name }}
                </div>
                @else
                <select name="program_id"
                    class="w-full text-sm rounded-xl px-3.5 py-2.5 transition-colors focus:outline-none"
                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);">
                    <option value="">Pilih Program ...</option>
                    @foreach($programs as $prog)
                    <option value="{{ $prog->id }}" {{ old('program_id') == $prog->id ? 'selected' : '' }}>
                        {{ $prog->program_name }}
                    </option>
                    @endforeach
                </select>
                @endif
                @error('program_id')<p class="text-xs font-medium mt-1" style="color: var(--dash-status-danger-text);">{{ $message }}</p>@enderror
            </div>

            {{-- NPS Score --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--dash-text-muted);">
                    Kemungkinan Merekomendasikan (0 - 10)
                </label>
                <div class="flex gap-1.5 flex-wrap">
                    @for($i = 0; $i <= 10; $i++)
                    <button type="button"
                        @click="score = {{ $i }}"
                        :style="score === {{ $i }}
                            ? 'background-color: {{ $i <= 6 ? 'var(--dash-status-danger-text)' : ($i <= 8 ? 'var(--dash-status-warning-text)' : 'var(--dash-status-success-text)') }}; border-color: transparent; color: #ffffff;'
                            : 'background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);'"
                        class="w-9 h-9 rounded-lg text-sm font-bold transition-all focus:outline-none cursor-pointer">
                        {{ $i }}
                    </button>
                    @endfor
                </div>
                <div class="flex justify-between text-xs mt-2 px-0.5" style="color: var(--dash-text-faint);">
                    <span>Tidak mungkin</span>
                    <span>Sangat mungkin</span>
                </div>
                <input type="hidden" name="score" :value="score">
                @error('score')<p class="text-xs font-medium mt-1" style="color: var(--dash-status-danger-text);">{{ $message }}</p>@enderror
            </div>

            {{-- Rating (for review) --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">Rating Program (1 - 5)</label>
                @include('member.reviews._rating-input', ['currentRating' => 0])
                @error('rating')<p class="text-xs font-medium mt-1" style="color: var(--dash-status-danger-text);">{{ $message }}</p>@enderror
            </div>

            {{-- Comment --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">
                    Komentar <span class="font-normal lowercase" style="color: var(--dash-text-faint);">(opsional)</span>
                </label>
                <textarea name="comment" rows="3"
                    class="w-full text-sm rounded-xl px-3.5 py-2.5 resize-none transition-colors focus:outline-none"
                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    placeholder="Ceritakan pengalaman Anda...">{{ old('comment') }}</textarea>
                @error('comment')<p class="text-xs font-medium mt-1" style="color: var(--dash-status-danger-text);">{{ $message }}</p>@enderror
            </div>

            {{-- Review text --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-1.5" style="color: var(--dash-text-muted);">
                    Review Program <span class="font-normal lowercase" style="color: var(--dash-text-faint);">(opsional)</span>
                </label>
                <textarea name="review" rows="3"
                    class="w-full text-sm rounded-xl px-3.5 py-2.5 resize-none transition-colors focus:outline-none"
                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    placeholder="Apa yang Anda suka dari program ini?">{{ old('review') }}</textarea>
                @error('review')<p class="text-xs font-medium mt-1" style="color: var(--dash-status-danger-text);">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                class="w-full py-2.5 text-xs font-semibold rounded-lg text-white transition-opacity hover:opacity-90 shadow-sm cursor-pointer"
                style="background-color: var(--dash-primary);">
                Kirim Penilaian
            </button>
        </form>
    </div>

    {{-- History --}}
    @if($responses->isNotEmpty())
    <div class="rounded-2xl shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-5 py-3.5" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
            <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Riwayat Penilaian</h3>
        </div>
        <div class="divide-y" style="border-color: var(--dash-card-border);">
            @foreach($responses as $resp)
            <div class="flex items-center justify-between px-5 py-4 transition-colors"
                 onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                 onmouseout="this.style.backgroundColor='transparent'">
                <div>
                    <span class="text-sm font-semibold font-jakarta" style="color: var(--dash-text-heading);">
                        {{ $resp->program->program_name ?? '-' }}
                    </span>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                        {{ $resp->branch->branch_name ?? '-' }} &middot; {{ $resp->created_at->translatedFormat('d M Y') }}
                    </p>
                    @if($resp->comment)
                    <p class="text-xs mt-1 line-clamp-1" style="color: var(--dash-text-body);">{{ $resp->comment }}</p>
                    @endif
                </div>
                <span class="text-2xl font-bold font-jakarta ml-4"
                      style="color: {{ $resp->score <= 6 ? 'var(--dash-status-danger-text)' : ($resp->score <= 8 ? 'var(--dash-status-warning-text)' : 'var(--dash-status-success-text)') }};">
                    {{ $resp->score }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
