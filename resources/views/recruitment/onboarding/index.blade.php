@extends('layouts.dashboard')

@section('title', 'Offering & Onboarding')
@section('header_title', 'Offering & Onboarding')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Rekrutmen</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Offering & Onboarding</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola proses persiapan penempatan kerja dan tinjauan onboarding calon karyawan.
            </p>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('recruitment.onboarding.index') }}"
          class="p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="w-full sm:w-64">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Status Onboarding</label>
                <select name="status"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Status</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="pending_review" @selected(request('status') === 'pending_review')>Pending Review</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    Filter
                </button>
                @if(request()->filled('status'))
                    <a href="{{ route('recruitment.onboarding.index') }}"
                       class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold transition-colors"
                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table Card Fit Full Desktop --}}
    <div class="rounded-2xl border shadow-sm overflow-hidden"
         style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border);">
        <div class="w-full">
            <table class="w-full text-sm text-left table-fixed">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="w-12 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                            style="color: var(--dash-text-muted);">No</th>
                        <th class="w-2/5 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Kandidat & Penempatan</th>
                        <th class="w-1/4 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Tipe & Mulai Kerja</th>
                        <th class="w-1/6 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="w-1/5 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($onboardings as $onboarding)
                        @php
                            $statusMap = [
                                'draft'          => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-700 dark:text-slate-300', 'label' => 'Draft'],
                                'pending_review' => ['bg' => 'bg-amber-100 dark:bg-amber-950/40', 'text' => 'text-amber-700 dark:text-amber-400', 'label' => 'Pending Review'],
                                'approved'       => ['bg' => 'bg-blue-100 dark:bg-blue-950/40', 'text' => 'text-blue-700 dark:text-blue-400', 'label' => 'Approved'],
                                'completed'      => ['bg' => 'bg-emerald-100 dark:bg-emerald-950/40', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'Completed'],
                                'rejected'       => ['bg' => 'bg-red-100 dark:bg-red-950/40', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'Rejected'],
                            ];
                            $st = $statusMap[$onboarding->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700'];
                            $st['label'] = \App\Support\EnumLabels::get('onboarding_status', $onboarding->status);
                            $candidateName = optional(optional($onboarding->application)->applicant)->full_name ?? '-';
                            $branchName = optional($onboarding->branch)->branch_name ?? optional($onboarding->branch)->name ?? '-';
                            $positionName = optional($onboarding->position)->position_name ?? optional($onboarding->position)->name ?? '-';
                            $empType = \App\Support\EnumLabels::get('employment_type', $onboarding->employment_type);
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3 text-center text-xs" style="color: var(--dash-text-muted);">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('recruitment.onboarding.show', $onboarding->id) }}"
                                   class="font-semibold block hover:underline transition-colors text-xs sm:text-sm truncate"
                                   style="color: var(--dash-primary-text);">
                                    {{ $candidateName }}
                                </a>
                                <p class="text-[11px] mt-0.5 flex items-center gap-1.5 truncate" style="color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[13px]">location_on</span>
                                    <span>{{ $branchName }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $positionName }}</span>
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <div class="font-medium" style="color: var(--dash-text-heading);">
                                    {{ $empType }}
                                </div>
                                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">
                                    Mulai: {{ $onboarding->start_date ? $onboarding->start_date->format('d M Y') : '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-semibold rounded-full {{ $st['bg'] }} {{ $st['text'] }}">
                                    {{ $st['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('recruitment.onboarding.show', $onboarding->id) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                                        Lihat
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">badge</span>
                                <h3 class="font-jakarta text-base font-semibold" style="color: var(--dash-text-heading);">Tidak ada data onboarding ditemukan</h3>
                                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Data onboarding otomatis dibuat setelah offering letter diterima oleh kandidat.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($onboardings->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $onboardings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
