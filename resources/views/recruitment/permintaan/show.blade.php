@extends('layouts.dashboard')

@section('title', 'Detail Job Permintaan')
@section('header_title', 'Detail Job Permintaan')

@section('content')
@php
    $statusColors = [
        'draft'                => 'bg-gray-100 text-gray-700',
        'pending_hr_review'    => 'bg-amber-100 text-amber-700',
        'pending_ops_approval' => 'bg-blue-100 text-blue-700',
        'approved'             => 'bg-emerald-100 text-emerald-700',
        'rejected'             => 'bg-red-100 text-red-700',
        'fulfilled'            => 'bg-purple-100 text-purple-700',
    ];
    $statusLabels = [
        'draft'                => 'Draft',
        'pending_hr_review'    => 'Menunggu Review HR',
        'pending_ops_approval' => 'Menunggu Persetujuan Ops',
        'approved'             => 'Disetujui',
        'rejected'             => 'Ditolak',
        'fulfilled'            => 'Terpenuhi',
    ];
    $statusColor = $statusColors[$jobPermintaan->status] ?? 'bg-gray-100 text-gray-700';
    $statusLabel = $statusLabels[$jobPermintaan->status] ?? $jobPermintaan->status;
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div class="flex items-start gap-3">
            <a href="{{ route('recruitment.permintaan.index') }}"
               class="mt-1 hover:opacity-70 transition-opacity shrink-0"
               style="color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Rekrutmen</p>
                <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Detail Job Permintaan</h1>
            </div>
        </div>
        @if($jobPermintaan->status === 'draft')
            @can('recruitment.job_permintaan.update')
            <a href="{{ route('recruitment.permintaan.edit', $jobPermintaan) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-lg transition-colors shrink-0"
               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">edit</span> Edit
            </a>
            @endcan
        @endif
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Info Card --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Informasi Permintaan</h2>
            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
        </div>

        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            @foreach([
                'Cabang'           => $jobPermintaan->branch->branch_name ?? '-',
                'Diajukan oleh'    => $jobPermintaan->requestedBy->full_name ?? '-',
                'Posisi'           => $jobPermintaan->detail?->position?->position_name ?? '-',
                'Jenis Permintaan' => $jobPermintaan->detail?->request_type === 'new_position' ? 'Posisi Baru' : ($jobPermintaan->detail?->request_type === 'replacement' ? 'Penggantian' : '-'),
                'Tipe Pekerjaan'   => ucfirst($jobPermintaan->detail?->employment_type ?? '-'),
                'Headcount'        => ($jobPermintaan->detail?->current_headcount ?? '?') . ' saat ini / ' . ($jobPermintaan->detail?->headcount ?? '-') . ' dibutuhkan',
                'Target Mulai'     => $jobPermintaan->detail?->target_start_date?->format('d M Y') ?? '-',
                'Dibuat'           => $jobPermintaan->created_at->format('d M Y H:i'),
            ] as $label => $value)
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-faint);">{{ $label }}</p>
                <p class="text-sm" style="color: var(--dash-text-heading);">{{ $value }}</p>
            </div>
            @endforeach
        </div>

        @if($jobPermintaan->detail?->job_description)
            <div class="px-6 pb-5" style="border-top: 1px solid var(--dash-card-border);">
                <p class="text-xs font-semibold uppercase tracking-wider mb-2 mt-4" style="color: var(--dash-text-faint);">Deskripsi Pekerjaan</p>
                <div class="text-sm rounded-xl p-4 whitespace-pre-wrap"
                     style="color: var(--dash-text-body); background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">{{ $jobPermintaan->detail->job_description }}</div>
            </div>
        @endif
    </div>

    {{-- New Position Guidance --}}
    @if($jobPermintaan->status === 'approved' && $jobPermintaan->detail?->request_type === 'new_position')
        <div class="rounded-xl p-5 flex items-start gap-3"
             style="background-color: rgba(217,119,6,0.08); border: 1px solid rgba(217,119,6,0.3);">
            <span class="material-symbols-outlined text-amber-500 mt-0.5 shrink-0">info</span>
            <div>
                <h3 class="text-sm font-semibold text-amber-700 mb-2">Panduan: Posisi Baru Disetujui</h3>
                <ol class="text-sm text-amber-700 space-y-1 list-decimal list-inside">
                    <li>Buat posisi baru di <strong>Kelola Divisi dan Posisi</strong> (jika perlu buat divisi baru dulu)</li>
                    <li>Set permission untuk posisi baru via panel permission</li>
                    <li>Set widget dashboard untuk posisi baru</li>
                    <li>Konfigurasi absensi, payroll, dan aturan lain untuk posisi baru</li>
                    <li>Jika fitur belum ada &rarr; hubungi developer</li>
                </ol>
            </div>
        </div>
    @endif

    {{-- Action Panel --}}
    <div class="rounded-2xl p-5" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <h2 class="text-sm font-semibold mb-4" style="color: var(--dash-text-heading);">Tindakan</h2>

        @if($jobPermintaan->status === 'draft' && auth()->user()->employee?->id === $jobPermintaan->requested_by_employee_id)
            <form action="{{ route('recruitment.permintaan.submit', $jobPermintaan) }}" method="POST"
                onsubmit="return confirm('Yakin ingin mengajukan permintaan ini ke HR?')">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    Ajukan ke HR
                </button>
            </form>
        @endif

        @if($jobPermintaan->status === 'pending_hr_review')
            @can('recruitment.job_permintaan.review')
                <form action="{{ route('recruitment.permintaan.hr-review', $jobPermintaan) }}" method="POST"
                    class="space-y-3" x-data="{ decision: '' }">
                    @csrf
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-emerald-700">
                            <input type="radio" name="decision" value="approved" x-model="decision" required class="accent-emerald-600" />
                            Setujui ke Ops
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-red-700">
                            <input type="radio" name="decision" value="rejected" x-model="decision" class="accent-red-600" />
                            Tolak
                        </label>
                    </div>
                    <textarea name="notes" rows="2"
                              class="w-full rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                              onfocus="this.style.borderColor='var(--dash-primary)'"
                              onblur="this.style.borderColor='var(--dash-input-border)'"
                              placeholder="Catatan review HR (opsional)"></textarea>
                    <button type="submit"
                            :disabled="!decision"
                            onclick="return confirm('Konfirmasi keputusan HR?')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50"
                            style="background-color: #d97706;">
                        Simpan Review HR
                    </button>
                </form>
            @else
                <p class="text-sm" style="color: var(--dash-text-muted);">Menunggu review dari HRD.</p>
            @endcan
        @endif

        @if($jobPermintaan->status === 'pending_ops_approval')
            @can('recruitment.job_permintaan.approve')
                <form action="{{ route('recruitment.permintaan.ops-approve', $jobPermintaan) }}" method="POST"
                    class="space-y-3" x-data="{ decision: '' }">
                    @csrf
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-emerald-700">
                            <input type="radio" name="decision" value="approved" x-model="decision" required class="accent-emerald-600" />
                            Setujui
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-red-700">
                            <input type="radio" name="decision" value="rejected" x-model="decision" class="accent-red-600" />
                            Tolak
                        </label>
                    </div>
                    <textarea name="notes" rows="2"
                              class="w-full rounded-lg text-sm px-3 py-2 focus:outline-none transition-colors"
                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                              onfocus="this.style.borderColor='var(--dash-primary)'"
                              onblur="this.style.borderColor='var(--dash-input-border)'"
                              placeholder="Catatan approval Ops (opsional)"></textarea>
                    <button type="submit"
                            :disabled="!decision"
                            onclick="return confirm('Konfirmasi keputusan Ops?')"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50"
                            style="background-color: var(--dash-primary);">
                        Simpan Keputusan Ops
                    </button>
                </form>
            @else
                <p class="text-sm" style="color: var(--dash-text-muted);">Menunggu persetujuan COO/Direktur Operasional.</p>
            @endcan
        @endif

        @if($jobPermintaan->status === 'approved')
            @can('recruitment.job_permintaan.approve')
                <form action="{{ route('recruitment.permintaan.fulfill', $jobPermintaan) }}" method="POST"
                    onsubmit="return confirm('Tandai permintaan ini sebagai Terpenuhi?')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: #7c3aed;">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        Tandai Terpenuhi
                    </button>
                </form>
            @else
                <p class="text-sm" style="color: var(--dash-text-muted);">
                    Permintaan disetujui. Posting lowongan kerja dapat dibuat dari menu
                    <a href="{{ route('recruitment.postings.index') }}" class="hover:underline" style="color: var(--dash-primary);">Job Posting</a>.
                </p>
            @endcan

            @can('recruitment.job_posting.create')
                <a href="{{ route('recruitment.postings.create', ['job_permintaan_id' => $jobPermintaan->id]) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity mt-3"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Buat Job Posting
                </a>
            @endcan
        @endif

        @if(in_array($jobPermintaan->status, ['rejected', 'fulfilled']))
            <p class="text-sm" style="color: var(--dash-text-muted);">Tidak ada tindakan tersedia untuk status ini.</p>
        @endif
    </div>

    {{-- Approval History --}}
    @if($jobPermintaan->approvals->count())
        <div class="rounded-2xl p-5" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <h2 class="text-sm font-semibold mb-4" style="color: var(--dash-text-heading);">Riwayat Persetujuan</h2>
            <div class="space-y-3">
                @foreach($jobPermintaan->approvals->sortBy('acted_at') as $approval)
                    <div class="flex items-start gap-3 text-sm">
                        <span class="mt-0.5 material-symbols-outlined text-base
                            {{ $approval->decision === 'approved' ? 'text-emerald-500' : 'text-red-500' }}">
                            {{ $approval->decision === 'approved' ? 'check_circle' : 'cancel' }}
                        </span>
                        <div class="flex-1">
                            <span class="font-medium" style="color: var(--dash-text-heading);">{{ $approval->actedBy->full_name ?? '-' }}</span>
                            <span class="mx-1" style="color: var(--dash-text-faint);">—</span>
                            <span class="{{ $approval->decision === 'approved' ? 'text-emerald-700' : 'text-red-700' }} font-medium">
                                {{ $approval->decision === 'approved' ? 'Disetujui' : 'Ditolak' }}
                            </span>
                            <span class="ml-2 text-xs" style="color: var(--dash-text-faint);">
                                ({{ $approval->approval_step === 'hrd_review' ? 'Review HR' : 'Persetujuan Ops' }})
                            </span>
                            <span class="ml-2 text-xs" style="color: var(--dash-text-faint);">
                                {{ \Carbon\Carbon::parse($approval->acted_at)->format('d M Y H:i') }}
                            </span>
                            @if($approval->notes)
                                <p class="mt-0.5 italic text-xs" style="color: var(--dash-text-muted);">{{ $approval->notes }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
