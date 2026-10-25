@extends('layouts.dashboard')

@section('title', 'Detail Onboarding')
@section('header_title', 'Detail Onboarding')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{
        showCompleteModal: false,
        showReject: false
     }">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('recruitment.onboarding.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Onboarding
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Rekrutmen</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Detail Onboarding</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Tinjau data kandidat, penempatan kerja, dan verifikasi pembuatan akun karyawan baru.
        </p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Status mapping --}}
    @php
        $statusColors = [
            'draft'          => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-700 dark:text-slate-300', 'label' => 'Draft'],
            'pending_review' => ['bg' => 'bg-amber-100 dark:bg-amber-950/40', 'text' => 'text-amber-700 dark:text-amber-400', 'label' => 'Pending Review'],
            'approved'       => ['bg' => 'bg-blue-100 dark:bg-blue-950/40', 'text' => 'text-blue-700 dark:text-blue-400', 'label' => 'Approved'],
            'completed'      => ['bg' => 'bg-emerald-100 dark:bg-emerald-950/40', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'Completed'],
            'rejected'       => ['bg' => 'bg-red-100 dark:bg-red-950/40', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'Rejected'],
        ];
        $st = $statusColors[$employeeOnboarding->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700'];
        $st['label'] = \App\Support\EnumLabels::get('onboarding_status', $employeeOnboarding->status);
        $candidateName = $employeeOnboarding->application->applicant->full_name ?? '-';
        $branchName = $employeeOnboarding->branch->branch_name ?? '-';
        $positionName = $employeeOnboarding->position->position_name ?? '-';
    @endphp

    {{-- Main Onboarding Card --}}
    <div class="rounded-2xl p-6 sm:p-7 shadow-sm space-y-6"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 gap-3"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div>
                <h2 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">
                    Onboarding #{{ $employeeOnboarding->id }}
                </h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                    Kandidat: <span class="font-semibold" style="color: var(--dash-primary-text);">{{ $candidateName }}</span>
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full shrink-0 {{ $st['bg'] }} {{ $st['text'] }}">
                {{ $st['label'] }}
            </span>
        </div>

        {{-- Section 1: Informasi Kandidat --}}
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-3" style="color: var(--dash-text-faint);">Informasi Kandidat</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-xs" style="color: var(--dash-text-muted);">Nama Lengkap</span>
                    <p class="font-semibold mt-0.5" style="color: var(--dash-text-heading);">{{ $candidateName }}</p>
                </div>
                <div>
                    <span class="text-xs" style="color: var(--dash-text-muted);">Email</span>
                    <p class="font-medium mt-0.5" style="color: var(--dash-text-heading);">{{ $employeeOnboarding->application->applicant->email ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs" style="color: var(--dash-text-muted);">Nomor WhatsApp</span>
                    <p class="font-medium mt-0.5" style="color: var(--dash-text-heading);">{{ $employeeOnboarding->application->applicant->whatsapp_number ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Section 2: Detail Penempatan --}}
        <div class="pt-5" style="border-top: 1px solid var(--dash-card-border);">
            <p class="text-[11px] font-semibold uppercase tracking-wider mb-3" style="color: var(--dash-text-faint);">Detail Penempatan Kerja</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-xs" style="color: var(--dash-text-muted);">Cabang</span>
                    <p class="font-semibold mt-0.5" style="color: var(--dash-text-heading);">{{ $branchName }}</p>
                </div>
                <div>
                    <span class="text-xs" style="color: var(--dash-text-muted);">Posisi</span>
                    <p class="font-semibold mt-0.5" style="color: var(--dash-primary-text);">{{ $positionName }}</p>
                </div>
                <div>
                    <span class="text-xs" style="color: var(--dash-text-muted);">Tipe Karyawan</span>
                    <p class="font-medium mt-0.5" style="color: var(--dash-text-heading);">{{ \App\Support\EnumLabels::get('employment_type', $employeeOnboarding->employment_type) }}</p>
                </div>
                <div>
                    <span class="text-xs" style="color: var(--dash-text-muted);">Tanggal Mulai</span>
                    <p class="font-medium mt-0.5" style="color: var(--dash-text-heading);">{{ $employeeOnboarding->start_date?->format('d M Y') ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Section 3: Hasil Review --}}
        @if($employeeOnboarding->reviewed_at)
            <div class="pt-5" style="border-top: 1px solid var(--dash-card-border);">
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-3" style="color: var(--dash-text-faint);">Hasil Tinjauan / Review</p>
                <div class="p-4 rounded-xl space-y-1.5 text-xs"
                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <p style="color: var(--dash-text-body);">Direview oleh: <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $employeeOnboarding->reviewedBy->full_name ?? '-' }}</span></p>
                    <p style="color: var(--dash-text-muted);">Waktu: {{ $employeeOnboarding->reviewed_at->format('d M Y H:i') }}</p>
                    @if($employeeOnboarding->review_notes)
                        <p class="pt-1 italic" style="color: var(--dash-text-body);">Catatan: "{{ $employeeOnboarding->review_notes }}"</p>
                    @endif
                    @if($employeeOnboarding->rejection_reason)
                        <p class="pt-1 font-semibold" style="color: var(--dash-danger, #dc2626);">Alasan Penolakan: {{ $employeeOnboarding->rejection_reason }}</p>
                    @endif
                </div>
            </div>
        @endif

        {{-- Section 4: Akun Employee Baru --}}
        @if($employeeOnboarding->employee)
            <div class="p-4 rounded-xl flex items-center gap-3"
                 style="background-color: color-mix(in srgb, #16a34a 10%, transparent); border: 1px solid color-mix(in srgb, #16a34a 25%, transparent);">
                <span class="material-symbols-outlined text-[24px]" style="color: #16a34a;">badge</span>
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider" style="color: #15803d;">Akun Karyawan Berhasil Dibuat</h3>
                    <p class="text-sm font-semibold mt-0.5" style="color: var(--dash-text-heading);">
                        {{ $employeeOnboarding->employee->full_name }} &bull; {{ $employeeOnboarding->employee->employee_code }}
                    </p>
                </div>
            </div>
        @endif

        {{-- Section 5: Actions Panel --}}
        <div class="pt-5" style="border-top: 1px solid var(--dash-card-border);">
            {{-- Form Review Direksi/Board (pending_review) --}}
            @if($employeeOnboarding->status === 'pending_review')
                @can('recruitment.onboarding.review')
                    <div class="space-y-4">
                        <div class="pb-2">
                            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Form Review Onboarding</h3>
                            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Berikan persetujuan atau catatan penolakan untuk kandidat ini</p>
                        </div>

                        <form action="{{ route('recruitment.onboarding.review', $employeeOnboarding) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Catatan Review</label>
                                <textarea name="review_notes" rows="2"
                                          placeholder="Tuliskan catatan evaluasi..."
                                          class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                          onfocus="this.style.borderColor='var(--dash-primary)'"
                                          onblur="this.style.borderColor='var(--dash-input-border)'"></textarea>
                            </div>

                            <div x-show="showReject" style="display: none;" class="space-y-1">
                                <label class="block text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Alasan Penolakan <span class="text-red-500">*</span></label>
                                <textarea name="rejection_reason" rows="2"
                                          placeholder="Wajib diisi jika menolak onboarding..."
                                          class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"></textarea>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 pt-2">
                                <button type="submit" name="decision" value="approved"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                    Setujui Onboarding
                                </button>
                                <button type="button" @click="showReject = !showReject"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[16px]">block</span>
                                    <span x-text="showReject ? 'Batal Tolak' : 'Tolak'"></span>
                                </button>
                                <button type="submit" name="decision" value="rejected" x-show="showReject" style="display: none;"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                    Konfirmasi Tolak
                                </button>
                            </div>
                        </form>
                    </div>
                @endcan
            @endif

            {{-- Complete (approved -> completed) --}}
            @if($employeeOnboarding->status === 'approved')
                @can('recruitment.onboarding.update')
                    <div class="flex items-center gap-3">
                        <button type="button" @click="showCompleteModal = true"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                                style="background-color: #16a34a;">
                            <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
                            Selesaikan Onboarding - Buat Akun Karyawan
                        </button>
                    </div>
                @endcan
            @endif
        </div>
    </div>

    {{-- Modal Konfirmasi Selesaikan Onboarding (Alpine.js, No confirm(), No overflow-hidden) --}}
    <template x-teleport="body">
        <div x-show="showCompleteModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showCompleteModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showCompleteModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showCompleteModal" @click.away="showCompleteModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-950/40 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[22px]">how_to_reg</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Selesaikan Onboarding?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Sistem akan membuat akun profil karyawan resmi dan menandai onboarding selesai.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showCompleteModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <form action="{{ route('recruitment.onboarding.complete', $employeeOnboarding) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: #16a34a;">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                    Ya, Selesaikan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
