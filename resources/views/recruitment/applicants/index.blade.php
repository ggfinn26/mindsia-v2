@extends('layouts.dashboard')

@section('title', 'Daftar Pelamar')
@section('header_title', 'Daftar Pelamar')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">
    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Rekrutmen</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Daftar Pelamar</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Database master riwayat profil kandidat dan pelamar di seluruh cabang.
            </p>
        </div>
    </div>

    {{-- Search Bar Mengikuti Standar DESIGN.md --}}
    <form method="GET" action="{{ route('recruitment.applicants.index') }}"
          class="p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="w-full sm:w-80">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Pencarian Pelamar</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                          style="color: var(--dash-text-muted);">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama, email, atau WA..."
                           class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('recruitment.applicants.index') }}"
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
                            style="color: var(--dash-text-muted);">Nama & Kontak Pelamar</th>
                        <th class="w-1/4 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Domisili & CV</th>
                        <th class="w-1/6 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-center"
                            style="color: var(--dash-text-muted);">Total Lamaran</th>
                        <th class="w-1/5 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($applicants as $applicant)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3 text-center text-xs" style="color: var(--dash-text-muted);">
                                {{ $applicants->firstItem() + $loop->index }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('recruitment.applicants.show', $applicant) }}"
                                   class="font-semibold block hover:underline transition-colors text-xs sm:text-sm truncate"
                                   style="color: var(--dash-primary-text);">
                                    {{ $applicant->full_name }}
                                    @if($applicant->gender)
                                        <span class="text-[11px] font-normal" style="color: var(--dash-text-muted);">({{ $applicant->gender === 'male' ? 'L' : 'P' }})</span>
                                    @endif
                                </a>
                                <p class="text-[11px] mt-0.5 flex items-center gap-1.5 truncate" style="color: var(--dash-text-muted);">
                                    <span>{{ $applicant->email ?? '-' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $applicant->whatsapp_number ?? '-' }}</span>
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <div class="font-medium" style="color: var(--dash-text-heading);">
                                    {{ $applicant->city ?? '-' }}
                                </div>
                                <div class="mt-1">
                                    @if($applicant->cv_path)
                                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">
                                            <span class="material-symbols-outlined text-[12px]">check</span> CV Ada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-500">
                                            Belum Ada CV
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                      style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                                    {{ $applicant->applications_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('recruitment.applicants.show', $applicant) }}"
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
                                      style="color: var(--dash-text-faint);">person_off</span>
                                <h3 class="font-jakarta text-base font-semibold" style="color: var(--dash-text-heading);">Tidak ada pelamar ditemukan</h3>
                                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                                    {{ request('search') ? 'Tidak ditemukan data dengan kata kunci tersebut. Coba kata kunci lain.' : 'Belum ada data pelamar di sistem.' }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applicants->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $applicants->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
