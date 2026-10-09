@extends('layouts.dashboard')

@section('title', 'Edit Test — ' . $test->test_name)
@section('header_title', 'Edit Test')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('classrooms.show', $classroom) }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Edit Test</h1>
            <p class="mt-0.5 text-sm" style="color: var(--dash-text-muted);">
                Kelas: <strong style="color: var(--dash-text-body);">{{ $classroom->class_name }}</strong>
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('class-tests.update', [$classroom, $test]) }}" method="POST">
            @csrf @method('PUT')
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                        Nama Test <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" name="test_name" required
                           value="{{ old('test_name', $test->test_name) }}"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    @error('test_name')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Tipe Test <span style="color:#dc2626;">*</span>
                        </label>
                        <select name="test_type" required data-no-enhance
                                class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                            <option value="pre_test" {{ old('test_type', $test->test_type) === 'pre_test' ? 'selected' : '' }}>Pre-Test</option>
                            <option value="post_test" {{ old('test_type', $test->test_type) === 'post_test' ? 'selected' : '' }}>Post-Test</option>
                        </select>
                        @error('test_type')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Tanggal Test <span style="color:#dc2626;">*</span>
                        </label>
                        <input type="date" name="date" required
                               value="{{ old('date', $test->date?->format('Y-m-d')) }}"
                               class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        @error('date')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Deskripsi</label>
                    <textarea name="description" rows="3"
                              class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                              style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">{{ old('description', $test->description) }}</textarea>
                    @error('description')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>

                @if($test->scoringCriteria->isNotEmpty())
                <div class="pt-4" style="border-top: 1px solid var(--dash-card-border);">
                    <h3 class="text-sm font-semibold mb-3" style="color: var(--dash-text-heading);">Kriteria Penilaian</h3>
                    <div class="space-y-2">
                        @foreach($test->scoringCriteria as $criterion)
                            <div class="rounded-lg px-4 py-3"
                                 style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                                <p class="font-medium text-sm" style="color: var(--dash-text-heading);">{{ $criterion->criteria_name }}</p>
                                @if($criterion->description)
                                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $criterion->description }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs mt-2" style="color: var(--dash-text-faint);">Kriteria tidak dapat diubah melalui form ini.</p>
                </div>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('classrooms.show', $classroom) }}"
                   class="px-5 py-2 rounded-xl text-sm font-medium transition-colors"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">Batal</a>
                <button type="submit"
                        class="px-5 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">Simpan Perubahan</button>
            </div>
        </form>
    </div>

</div>
@endsection
