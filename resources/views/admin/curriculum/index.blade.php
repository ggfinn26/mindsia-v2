@extends('layouts.dashboard')

@section('title', 'Kelola Kurikulum - MINDSIA')
@section('header_title', 'Kelola Kurikulum')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Kurikulum</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kelola Kurikulum</h1>
        </div>
        <a href="{{ route('curriculums.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>Tambah Kurikulum
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="rounded-2xl px-5 py-4"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" action="{{ route('curriculums.index') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium mb-1.5" style="color: var(--dash-text-muted);">Program</label>
                <select name="program_id" onchange="this.form.submit()"
                        class="rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Semua Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                            {{ $program->program_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if(request('program_id'))
                <a href="{{ route('curriculums.index') }}"
                   class="inline-flex items-center gap-1 text-xs font-medium transition-opacity hover:opacity-75 pb-2.5"
                   style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[14px]">close</span>Reset
                </a>
            @endif
        </form>
    </div>

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[26%]"><col class="w-[20%]"><col class="w-[10%]">
                    <col class="w-[9%]"><col class="w-[9%]"><col class="w-[26%]">
                </colgroup>
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kurikulum</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Program</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Sesi</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Materi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($curriculums as $curriculum)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4 font-medium truncate" style="color: var(--dash-text-heading);">{{ $curriculum->curriculum_name }}</td>
                            <td class="px-5 py-4 truncate" style="color: var(--dash-text-body);">{{ $curriculum->program->program_name }}</td>
                            <td class="px-5 py-4 text-right" style="color: var(--dash-text-body);">{{ $curriculum->sessions->count() }}</td>
                            <td class="px-5 py-4 text-right" style="color: var(--dash-text-body);">{{ $curriculum->sessions->sum(fn($s) => $s->items->count()) }}</td>
                            <td class="px-5 py-4">
                                @if($curriculum->is_active)
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5 flex-nowrap">
                                    <a href="{{ route('curriculums.show', $curriculum) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[13px]">open_in_new</span>Detail
                                    </a>
                                    <a href="{{ route('curriculums.edit', $curriculum) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[13px]">edit</span>Edit
                                    </a>
                                    <form action="{{ route('curriculums.destroy', $curriculum) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus kurikulum ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                                style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                                                onmouseover="this.style.backgroundColor='#fecaca'"
                                                onmouseout="this.style.backgroundColor='#fee2e2'">
                                            <span class="material-symbols-outlined text-[13px]">delete</span>Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">menu_book</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada kurikulum.</p>
                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">
                                    Kurikulum dibuat dari halaman detail Program (relasi 1:1).
                                </p>
                                <a href="{{ route('programs.index') }}"
                                   class="inline-flex items-center gap-1 mt-4 text-sm font-semibold hover:underline"
                                   style="color: var(--dash-primary);">
                                    Lihat daftar Program <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($curriculums->hasPages())
            <div class="px-5 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $curriculums->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
