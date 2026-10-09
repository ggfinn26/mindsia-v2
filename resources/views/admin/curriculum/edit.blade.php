@extends('layouts.dashboard')

@section('title', 'Edit ' . $curriculum->curriculum_name . ' - MINDSIA')
@section('header_title', 'Edit Kurikulum')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('curriculums.show', $curriculum) }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Edit Kurikulum</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">{{ $curriculum->curriculum_name }}</p>
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

    <div class="rounded-2xl p-6"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('curriculums.update', $curriculum) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Program</label>
                <select name="program_id" disabled data-no-enhance
                        class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);">
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ $program->id === $curriculum->program_id ? 'selected' : '' }}>
                            {{ $program->program_name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Program tidak dapat diubah (relasi 1:1).</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Nama Kurikulum <span style="color:#dc2626;">*</span>
                </label>
                <input type="text" name="curriculum_name" required
                       value="{{ old('curriculum_name', $curriculum->curriculum_name) }}"
                       class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                @error('curriculum_name')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Deskripsi <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(opsional)</span>
                </label>
                <textarea name="description" rows="4"
                          class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">{{ old('description', $curriculum->description) }}</textarea>
            </div>
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $curriculum->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded">
                    <span class="text-sm font-medium" style="color: var(--dash-text-body);">Aktif</span>
                </label>
            </div>
            <div class="flex items-center gap-3 pt-2" style="border-top: 1px solid var(--dash-card-border);">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>Simpan
                </button>
                <a href="{{ route('curriculums.show', $curriculum) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Sesi --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center gap-2 px-5 py-4" style="border-bottom: 1px solid var(--dash-card-border);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">calendar_month</span>
            <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Sesi Pertemuan Mingguan</h2>
        </div>
        <div class="p-5">
            @if($curriculum->sessions->isEmpty())
                <p class="text-sm py-4 text-center" style="color: var(--dash-text-muted);">
                    Belum ada sesi. Lihat detail kurikulum untuk menambah sesi.
                </p>
            @else
                <div class="space-y-3">
                    @foreach($curriculum->sessions->sortBy('sort_order') as $session)
                        <div class="flex items-center justify-between rounded-xl p-4"
                             style="border: 1px solid var(--dash-card-border);">
                            <div>
                                <h3 class="font-medium text-sm" style="color: var(--dash-text-heading);">
                                    Minggu {{ $session->session_number }}: {{ $session->session_title }}
                                </h3>
                                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                                    {{ $session->items->count() }} item materi
                                </p>
                            </div>
                            <a href="{{ route('curriculums.show', $curriculum) . '#session-' . $session->id }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Kelola
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="mt-4">
                <a href="{{ route('curriculums.show', $curriculum) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                   style="background-color: #059669;">
                    <span class="material-symbols-outlined text-[16px]">add</span>Tambah Sesi / Kelola Konten
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
