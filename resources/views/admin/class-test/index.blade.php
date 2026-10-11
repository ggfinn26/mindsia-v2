@extends('layouts.dashboard')

@section('title', 'Test & Sertifikat')
@section('header_title', 'Test & Sertifikat')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Akademik</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Test & Sertifikat</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Daftar pre-test, post-test, dan rekap hasil evaluasi penilaian kelas.
            </p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
            <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: color-mix(in srgb, var(--dash-danger) 10%, transparent); border: 1px solid color-mix(in srgb, var(--dash-danger) 25%, transparent); color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Tanggal</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Kelas & Program</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Nama Test</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                            style="color: var(--dash-text-muted);">Tipe</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                            style="color: var(--dash-text-muted);">Hasil</th>
                        <th class="px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($tests as $test)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3 whitespace-nowrap text-xs" style="color: var(--dash-text-heading);">
                                {{ $test->date?->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 font-medium">
                                <a href="{{ route('classrooms.show', $test->classRoom) }}"
                                   class="font-semibold block hover:underline transition-colors text-xs"
                                   style="color: var(--dash-primary-text);">
                                    {{ $test->classRoom?->class_name ?? '-' }}
                                </a>
                                <p class="text-[11px] mt-0.5 flex items-center gap-1" style="color: var(--dash-text-muted);">
                                    <span>{{ $test->classRoom?->program?->program_name ?? '-' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $test->classRoom?->branch?->branch_name ?? '-' }}</span>
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs font-medium" style="color: var(--dash-text-heading);">
                                {{ $test->test_name }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($test->test_type === 'pre_test')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: color-mix(in srgb, var(--dash-primary) 15%, transparent); color: var(--dash-primary);">
                                        Pre-Test
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: color-mix(in srgb, #9333ea 15%, transparent); color: #9333ea;">
                                        Post-Test
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-xs font-medium" style="color: var(--dash-text-body);">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full"
                                      style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                                    <span class="material-symbols-outlined text-[13px]" style="color: var(--dash-text-muted);">how_to_reg</span>
                                    {{ $test->member_test_results_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <a href="{{ route('classrooms.show', $test->classRoom) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                    Buka
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">quiz</span>
                                <h3 class="font-jakarta text-base font-semibold" style="color: var(--dash-text-heading);">Belum ada test tersedia.</h3>
                                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Test dapat ditambahkan langsung dari halaman detail masing-masing kelas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tests->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $tests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
