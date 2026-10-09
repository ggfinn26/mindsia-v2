@extends('layouts.dashboard')

@section('title', 'Test & Sertifikat')
@section('header_title', 'Test & Sertifikat')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Akademik</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Test & Sertifikat</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Daftar semua test yang telah dilakukan pada berbagai kelas.
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #a7f3d0; background-color: #ecfdf5; color: #065f46;">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kelas</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Program</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Cabang</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Nama Test</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Tipe</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Hasil</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tests as $test)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">{{ $test->date?->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <a href="{{ route('classrooms.show', $test->classRoom) }}" class="font-semibold transition-opacity hover:opacity-75" style="color: var(--dash-primary);">
                                    {{ $test->classRoom?->class_name ?? '-' }}
                                </a>
                            </td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">{{ $test->classRoom?->program?->program_name ?? '-' }}</td>
                            <td class="px-5 py-4" style="color: var(--dash-text-body);">{{ $test->classRoom?->branch?->branch_name ?? '-' }}</td>
                            <td class="px-5 py-4" style="color: var(--dash-text-heading);">{{ $test->test_name }}</td>
                            <td class="px-5 py-4 text-center">
                                @if($test->test_type === 'pre_test')
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold" style="background-color: #eff6ff; color: #1d4ed8;">Pre-Test</span>
                                @else
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold" style="background-color: #faf5ff; color: #7e22ce;">Post-Test</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center tabular-nums" style="color: var(--dash-text-body);">{{ $test->member_test_results_count }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('classrooms.show', $test->classRoom) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>Buka
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">quiz</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada test tersedia.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tests->hasPages())
        <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            {{ $tests->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
