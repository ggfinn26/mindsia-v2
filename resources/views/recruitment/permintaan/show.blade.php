@extends('layouts.dashboard')

@section('title', 'Detail Job Permintaan')
@section('header_title', 'Detail Job Permintaan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{
        showConfirmModal: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmFormId: '',
        confirmBtnText: 'Konfirmasi',
        confirmIsDanger: false,
        promptConfirm(title, message, formId, btnText, isDanger = false) {
            this.confirmTitle = title;
            this.confirmMessage = message;
            this.confirmFormId = formId;
            this.confirmBtnText = btnText || 'Konfirmasi';
            this.confirmIsDanger = isDanger;
            this.showConfirmModal = true;
        },
        submitConfirmed() {
            if (this.confirmFormId) {
                document.getElementById(this.confirmFormId).submit();
            }
            this.showConfirmModal = false;
        }
     }">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <a href="{{ route('recruitment.permintaan.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Permintaan
            </a>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Rekrutmen</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Detail Job Permintaan</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Tinjau rincian kebutuhan dan tahapan persetujuan tenaga kerja cabang.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if($jobPermintaan->status === 'draft')
                @can('recruitment.job_permintaan.update')
                    <a href="{{ route('recruitment.permintaan.edit', $jobPermintaan) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors"
                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[14px]">edit</span>
                        Edit Permintaan
                    </a>
                @endcan
            @endif
        </div>
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
            'draft'                => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-700 dark:text-slate-300', 'label' => 'Draft'],
            'pending_hr_review'    => ['bg' => 'bg-amber-100 dark:bg-amber-950/40', 'text' => 'text-amber-700 dark:text-amber-400', 'label' => 'Menunggu Review HR'],
            'pending_ops_approval' => ['bg' => 'bg-blue-100 dark:bg-blue-950/40', 'text' => 'text-blue-700 dark:text-blue-400', 'label' => 'Menunggu Persetujuan Ops'],
            'approved'             => ['bg' => 'bg-emerald-100 dark:bg-emerald-950/40', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'Disetujui'],
            'rejected'             => ['bg' => 'bg-red-100 dark:bg-red-950/40', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'Ditolak'],
            'fulfilled'            => ['bg' => 'bg-purple-100 dark:bg-purple-950/40', 'text' => 'text-purple-700 dark:text-purple-400', 'label' => 'Terpenuhi'],
        ];
        $st = $statusColors[$jobPermintaan->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'label' => ucfirst($jobPermintaan->status)];
    @endphp

    {{-- Main Info Card --}}
    <div class="rounded-2xl p-6 sm:p-7 shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-center justify-between pb-5 mb-5"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div>
                <h2 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Informasi Permintaan</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Detail form kebutuhan sumber daya manusia</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $st['bg'] }} {{ $st['text'] }}">
                {{ $st['label'] }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Cabang</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $jobPermintaan->branch->branch_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Diajukan Oleh</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">{{ $jobPermintaan->requestedBy->full_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Posisi yang Diminta</p>
                <p class="text-sm font-semibold" style="color: var(--dash-primary-text);">
                    {{ $jobPermintaan->detail?->position?->position_name ?? $jobPermintaan->detail?->position?->name ?? '-' }}
                </p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Jenis Permintaan</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">
                    @if($jobPermintaan->detail?->request_type === 'new_position')
                        Posisi Baru (New Position)
                    @elseif($jobPermintaan->detail?->request_type === 'replacement')
                        Penggantian (Replacement)
                    @else
                        -
                    @endif
                </p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Tipe Pekerjaan</p>
                <p class="text-sm font-medium capitalize" style="color: var(--dash-text-heading);">
                    {{ str_replace('_', ' ', $jobPermintaan->detail?->employment_type ?? '-') }}
                </p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Headcount Kebutuhan</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">
                    {{ $jobPermintaan->detail?->current_headcount ?? 0 }} saat ini /
                    <span class="font-bold" style="color: var(--dash-primary-text);">{{ $jobPermintaan->detail?->headcount ?? 0 }}</span> dibutuhkan
                </p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Target Tanggal Mulai</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">
                    {{ $jobPermintaan->detail?->target_start_date?->format('d M Y') ?? '-' }}
                </p>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">Tanggal Dibuat</p>
                <p class="text-sm font-medium" style="color: var(--dash-text-heading);">
                    {{ $jobPermintaan->created_at?->format('d M Y H:i') ?? '-' }}
                </p>
            </div>
        </div>

        @if($jobPermintaan->detail?->job_description)
            <div class="mt-6 pt-5" style="border-top: 1px solid var(--dash-card-border);">
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-2" style="color: var(--dash-text-faint);">Deskripsi Pekerjaan</p>
                <div class="text-sm p-4 rounded-xl whitespace-pre-wrap leading-relaxed"
                     style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body);">
                    {{ $jobPermintaan->detail->job_description }}
                </div>
            </div>
        @endif
    </div>

    {{-- Guidance: Posisi Baru Disetujui --}}
    @if($jobPermintaan->status === 'approved' && $jobPermintaan->detail?->request_type === 'new_position')
        <div class="rounded-2xl p-5"
             style="background-color: color-mix(in srgb, #d97706 10%, transparent); border: 1px solid color-mix(in srgb, #d97706 25%, transparent);">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-[22px] shrink-0" style="color: #d97706;">lightbulb</span>
                <div>
                    <h3 class="text-sm font-bold" style="color: #b45309;">Panduan Tindak Lanjut: Posisi Baru Disetujui</h3>
                    <ol class="text-xs mt-2 space-y-1.5 list-decimal list-inside" style="color: var(--dash-text-body);">
                        <li>Pastikan divisi dan posisi telah terdaftar di menu Kelola Posisi.</li>
                        <li>Atur permission dan hak akses dashboard untuk posisi baru.</li>
                        <li>Konfigurasikan widget dashboard dan jadwal presensi posisi baru.</li>
                        <li>Hubungi tim developer jika terdapat modul khusus untuk posisi ini.</li>
                    </ol>
                </div>
            </div>
        </div>
    @endif

    {{-- Action Panel Card --}}
    <div class="rounded-2xl p-6 shadow-sm space-y-4"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="pb-3" style="border-bottom: 1px solid var(--dash-card-border);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Tindakan Permintaan</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Aksi persetujuan dan pengelolaan tahapan rekrutmen</p>
        </div>

        {{-- Aksi: Submit ke HR (Draft oleh pemohon) --}}
        @if($jobPermintaan->status === 'draft' && auth()->user()->employee?->id === $jobPermintaan->requested_by_employee_id)
            <form id="formSubmitHr" action="{{ route('recruitment.permintaan.submit', $jobPermintaan) }}" method="POST">
                @csrf
                <button type="button"
                        @click="promptConfirm('Ajukan ke HR?', 'Permintaan ini akan dikirimkan ke HRD untuk ditinjau kelayakannya.', 'formSubmitHr', 'Ajukan ke HR')"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    Ajukan ke HR
                </button>
            </form>
        @endif

        {{-- Aksi: Review HR --}}
        @if($jobPermintaan->status === 'pending_hr_review')
            @can('recruitment.job_permintaan.review')
                <form id="formHrReview" action="{{ route('recruitment.permintaan.hr-review', $jobPermintaan) }}" method="POST"
                      class="space-y-4" x-data="{ hrDecision: '' }">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                               style="color: var(--dash-text-faint);">Keputusan Review HR <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium"
                                   style="color: var(--dash-text-heading);">
                                <input type="radio" name="decision" value="approved" x-model="hrDecision" required class="accent-emerald-600" />
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Setujui & Teruskan ke Ops</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium"
                                   style="color: var(--dash-text-heading);">
                                <input type="radio" name="decision" value="rejected" x-model="hrDecision" class="accent-red-600" />
                                <span class="text-red-600 dark:text-red-400 font-semibold">Tolak Permintaan</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1"
                               style="color: var(--dash-text-faint);">Catatan Review HR (opsional)</label>
                        <textarea name="notes" rows="2"
                                  placeholder="Tuliskan catatan pertimbangan review..."
                                  class="w-full px-4 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                  style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                  onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                  onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"></textarea>
                    </div>

                    <button type="button"
                            :disabled="!hrDecision"
                            @click="promptConfirm('Konfirmasi Review HR', 'Apakah Anda yakin ingin menyimpan keputusan review HR ini?', 'formHrReview', 'Simpan Review', hrDecision === 'rejected')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
                        Simpan Review HR
                    </button>
                </form>
            @else
                <p class="text-sm" style="color: var(--dash-text-muted);">Sedang menunggu review dan verifikasi dari HRD.</p>
            @endcan
        @endif

        {{-- Aksi: Persetujuan Ops --}}
        @if($jobPermintaan->status === 'pending_ops_approval')
            @can('recruitment.job_permintaan.approve')
                <form id="formOpsApprove" action="{{ route('recruitment.permintaan.ops-approve', $jobPermintaan) }}" method="POST"
                      class="space-y-4" x-data="{ opsDecision: '' }">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-2"
                               style="color: var(--dash-text-faint);">Keputusan Direktur Operasional <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium"
                                   style="color: var(--dash-text-heading);">
                                <input type="radio" name="decision" value="approved" x-model="opsDecision" required class="accent-emerald-600" />
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Setujui Permintaan</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium"
                                   style="color: var(--dash-text-heading);">
                                <input type="radio" name="decision" value="rejected" x-model="opsDecision" class="accent-red-600" />
                                <span class="text-red-600 dark:text-red-400 font-semibold">Tolak Permintaan</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1"
                               style="color: var(--dash-text-faint);">Catatan Keputusan Ops (opsional)</label>
                        <textarea name="notes" rows="2"
                                  placeholder="Tuliskan catatan keputusan persetujuan..."
                                  class="w-full px-4 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                  style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                  onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                  onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"></textarea>
                    </div>

                    <button type="button"
                            :disabled="!opsDecision"
                            @click="promptConfirm('Konfirmasi Keputusan Ops', 'Apakah Anda yakin ingin mencatat keputusan operasional ini?', 'formOpsApprove', 'Simpan Keputusan Ops', opsDecision === 'rejected')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">verified</span>
                        Simpan Keputusan Ops
                    </button>
                </form>
            @else
                <p class="text-sm" style="color: var(--dash-text-muted);">Menunggu keputusan persetujuan dari COO atau Direktur Operasional.</p>
            @endcan
        @endif

        {{-- Aksi: Buat Job Posting & Tandai Terpenuhi (Approved) --}}
        @if($jobPermintaan->status === 'approved')
            <div class="flex flex-wrap items-center gap-3">
                @can('recruitment.job_posting.create')
                    <a href="{{ route('recruitment.postings.create', ['job_permintaan_id' => $jobPermintaan->id]) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                       style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        Buat Job Posting
                    </a>
                @endcan

                @can('recruitment.job_permintaan.approve')
                    <form id="formFulfill" action="{{ route('recruitment.permintaan.fulfill', $jobPermintaan) }}" method="POST" class="m-0">
                        @csrf
                        <button type="button"
                                @click="promptConfirm('Tandai Terpenuhi?', 'Permintaan ini akan ditandai telah terpenuhi seluruh kuotanya.', 'formFulfill', 'Tandai Terpenuhi')"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90"
                                style="background-color: #7e22ce;">
                            <span class="material-symbols-outlined text-[16px]">task_alt</span>
                            Tandai Terpenuhi
                        </button>
                    </form>
                @endcan
            </div>
        @endif

        @if(in_array($jobPermintaan->status, ['rejected', 'fulfilled']))
            <p class="text-sm" style="color: var(--dash-text-muted);">Permintaan ini telah selesai diproses. Tidak ada tindakan lanjutan yang diperlukan.</p>
        @endif
    </div>

    {{-- Approval History Card --}}
    @if($jobPermintaan->approvals->count())
        <div class="rounded-2xl p-6 shadow-sm"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="pb-3 mb-4" style="border-bottom: 1px solid var(--dash-card-border);">
                <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Riwayat Persetujuan</h2>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Jejak audit keputusan tahapan permintaan</p>
            </div>

            <div class="space-y-4">
                @foreach($jobPermintaan->approvals->sortBy('acted_at') as $approval)
                    @php
                        $isApproved = $approval->decision === 'approved';
                        $stepLabel = $approval->approval_step === 'hrd_review' ? 'Review HR' : 'Persetujuan Ops';
                    @endphp
                    <div class="flex items-start gap-3 p-3.5 rounded-xl text-sm"
                         style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                        <span class="material-symbols-outlined text-[20px] shrink-0 mt-0.5"
                              style="color: {{ $isApproved ? '#16a34a' : 'var(--dash-danger, #dc2626)' }};">
                            {{ $isApproved ? 'check_circle' : 'cancel' }}
                        </span>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-bold" style="color: var(--dash-text-heading);">
                                    {{ $approval->actedBy->full_name ?? '-' }}
                                </span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-semibold {{ $isApproved ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $isApproved ? 'Disetujui' : 'Ditolak' }}
                                </span>
                                <span class="text-xs font-medium" style="color: var(--dash-text-muted);">
                                    ({{ $stepLabel }})
                                </span>
                                <span class="text-xs ml-auto" style="color: var(--dash-text-faint);">
                                    {{ \Carbon\Carbon::parse($approval->acted_at)->format('d M Y H:i') }}
                                </span>
                            </div>
                            @if($approval->notes)
                                <p class="text-xs mt-2 italic" style="color: var(--dash-text-body);">
                                    "{{ $approval->notes }}"
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Modal Konfirmasi Kustom Alpine.js (Zero Browser Confirm, Zero Overflow-Hidden) --}}
    <template x-teleport="body">
        <div x-show="showConfirmModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showConfirmModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showConfirmModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showConfirmModal" @click.away="showConfirmModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                     :class="confirmIsDanger ? 'bg-red-100 dark:bg-red-950/40' : 'bg-blue-100 dark:bg-blue-950/40'">
                                    <span class="material-symbols-outlined text-[22px]"
                                          :class="confirmIsDanger ? 'text-red-600 dark:text-red-400' : 'text-blue-600 dark:text-blue-400'"
                                          x-text="confirmIsDanger ? 'warning' : 'help_outline'"></span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showConfirmModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <button type="button" @click="submitConfirmed()"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                    :style="confirmIsDanger ? 'background-color: var(--dash-danger, #dc2626);' : 'background-color: var(--dash-primary);'">
                                <span class="material-symbols-outlined text-[16px]" x-text="confirmIsDanger ? 'close' : 'check'"></span>
                                <span x-text="confirmBtnText"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
