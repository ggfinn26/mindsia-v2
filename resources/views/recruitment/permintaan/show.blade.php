@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto" x-data="{
    showModal: false,
    modalTitle: '',
    modalMessage: '',
    actionUrl: '',
    actionMethod: 'POST',
    actionText: 'Konfirmasi',
    actionClass: 'bg-indigo-500 hover:bg-indigo-600',
    showNotesField: false,
    decisionValue: ''
}">
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('recruitment.permintaan.index') }}" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined text-xl">arrow_back</span>
            </a>
            <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Job Permintaan</h1>
        </div>
        <div class="flex gap-2">
            @if($jobPermintaan->status === 'draft')
                @can('recruitment.job_permintaan.update')
                    <a href="{{ route('recruitment.permintaan.edit', $jobPermintaan) }}"
                        class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">
                        Edit
                    </a>
                @endcan
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-md text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Status Badge --}}
    @php
        $statusColors = [
            'draft'                  => 'bg-gray-100 text-gray-800',
            'pending_hr_review'      => 'bg-yellow-100 text-yellow-800',
            'pending_ops_approval'   => 'bg-blue-100 text-blue-800',
            'approved'               => 'bg-green-100 text-green-800',
            'rejected'               => 'bg-red-100 text-red-800',
            'fulfilled'              => 'bg-purple-100 text-purple-800',
        ];
        $statusLabels = [
            'draft'                  => 'Draft',
            'pending_hr_review'      => 'Menunggu Review HR',
            'pending_ops_approval'   => 'Menunggu Persetujuan Ops',
            'approved'               => 'Disetujui',
            'rejected'               => 'Ditolak',
            'fulfilled'              => 'Terpenuhi',
        ];
        $statusColor = $statusColors[$jobPermintaan->status] ?? 'bg-gray-100 text-gray-800';
        $statusLabel = $statusLabels[$jobPermintaan->status] ?? $jobPermintaan->status;
    @endphp

    {{-- Main info card --}}
    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 mb-5">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-semibold text-gray-700">Informasi Permintaan</h2>
            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                {{ $statusLabel }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Cabang</p>
                <p class="text-sm text-gray-900">{{ $jobPermintaan->branch->branch_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Diajukan oleh</p>
                <p class="text-sm text-gray-900">{{ $jobPermintaan->requestedBy->full_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Posisi</p>
                <p class="text-sm text-gray-900">{{ $jobPermintaan->detail?->position?->position_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jenis Permintaan</p>
                <p class="text-sm text-gray-900">
                    @if($jobPermintaan->detail?->request_type === 'new_position')
                        Posisi Baru
                    @elseif($jobPermintaan->detail?->request_type === 'replacement')
                        Penggantian
                    @else
                        -
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tipe Pekerjaan</p>
                <p class="text-sm text-gray-900 capitalize">{{ $jobPermintaan->detail?->employment_type ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Headcount</p>
                <p class="text-sm text-gray-900">
                    {{ $jobPermintaan->detail?->current_headcount ?? '?' }} saat ini /
                    {{ $jobPermintaan->detail?->headcount ?? '-' }} dibutuhkan
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Target Mulai</p>
                <p class="text-sm text-gray-900">
                    {{ $jobPermintaan->detail?->target_start_date?->format('d M Y') ?? '-' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Dibuat</p>
                <p class="text-sm text-gray-900">{{ $jobPermintaan->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>

        @if($jobPermintaan->detail?->job_description)
            <div class="mt-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Deskripsi Pekerjaan</p>
                <div class="text-sm text-gray-800 bg-gray-50 rounded-md p-4 border border-gray-100 whitespace-pre-wrap">{{ $jobPermintaan->detail->job_description }}</div>
            </div>
        @endif
    </div>

    {{-- New Position Guidance (only when approved + new_position) --}}
    @if($jobPermintaan->status === 'approved' && $jobPermintaan->detail?->request_type === 'new_position')
        <div class="bg-amber-50 border border-amber-200 rounded-sm p-5 mb-5">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-500 mt-0.5">info</span>
                <div>
                    <h3 class="text-sm font-semibold text-amber-800 mb-2">Panduan: Posisi Baru Disetujui</h3>
                    <ol class="text-sm text-amber-700 space-y-1 list-decimal list-inside">
                        <li>Buat posisi baru di <strong>Kelola Divisi dan Posisi</strong> (jika perlu buat divisi baru dulu)</li>
                        <li>Set permission untuk posisi baru via panel permission</li>
                        <li>Set widget dashboard untuk posisi baru</li>
                        <li>Konfigurasi absensi, payroll, dan aturan lain untuk posisi baru</li>
                        <li>Jika fitur belum ada &rarr; hubungi developer</li>
                    </ol>
                </div>
            </div>
        </div>
    @endif

    {{-- Action Panel --}}
    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-5 mb-5">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">Tindakan</h2>

        @if($jobPermintaan->status === 'draft' && auth()->user()->employee?->id === $jobPermintaan->requested_by_employee_id)
            <form action="{{ route('recruitment.permintaan.submit', $jobPermintaan) }}" method="POST"
                onsubmit="return confirm('Yakin ingin mengajukan permintaan ini ke HR?')">
                @csrf
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white text-sm">
                    <span class="material-symbols-outlined text-base mr-1">send</span>
                    Ajukan ke HR
                </button>
            </form>
        @endif

        @if($jobPermintaan->status === 'pending_hr_review')
            @can('recruitment.job_permintaan.review')
                <form action="{{ route('recruitment.permintaan.hr-review', $jobPermintaan) }}" method="POST"
                    class="space-y-3" x-data="{ decision: '' }">
                    @csrf
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="decision" value="approved" x-model="decision" required />
                            <span class="text-sm text-green-700 font-medium">Setujui ke Ops</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="decision" value="rejected" x-model="decision" />
                            <span class="text-sm text-red-700 font-medium">Tolak</span>
                        </label>
                    </div>
                    <textarea name="notes" rows="2" class="form-textarea w-full text-sm"
                        placeholder="Catatan review HR (opsional)"></textarea>
                    <button type="submit" class="btn bg-yellow-500 hover:bg-yellow-600 text-white text-sm"
                        :disabled="!decision" onclick="return confirm('Konfirmasi keputusan HR?')">
                        Simpan Review HR
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-500">Menunggu review dari HRD.</p>
            @endcan
        @endif

        @if($jobPermintaan->status === 'pending_ops_approval')
            @can('recruitment.job_permintaan.approve')
                <form action="{{ route('recruitment.permintaan.ops-approve', $jobPermintaan) }}" method="POST"
                    class="space-y-3" x-data="{ decision: '' }">
                    @csrf
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="decision" value="approved" x-model="decision" required />
                            <span class="text-sm text-green-700 font-medium">Setujui</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="decision" value="rejected" x-model="decision" />
                            <span class="text-sm text-red-700 font-medium">Tolak</span>
                        </label>
                    </div>
                    <textarea name="notes" rows="2" class="form-textarea w-full text-sm"
                        placeholder="Catatan approval Ops (opsional)"></textarea>
                    <button type="submit" class="btn bg-blue-500 hover:bg-blue-600 text-white text-sm"
                        :disabled="!decision" onclick="return confirm('Konfirmasi keputusan Ops?')">
                        Simpan Keputusan Ops
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-500">Menunggu persetujuan COO/Direktur Operasional.</p>
            @endcan
        @endif

        @if($jobPermintaan->status === 'approved')
            @can('recruitment.job_permintaan.approve')
                <form action="{{ route('recruitment.permintaan.fulfill', $jobPermintaan) }}" method="POST"
                    onsubmit="return confirm('Tandai permintaan ini sebagai Terpenuhi?')">
                    @csrf
                    <button type="submit" class="btn bg-purple-500 hover:bg-purple-600 text-white text-sm">
                        Tandai Terpenuhi
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-500">
                    Permintaan disetujui. Posting lowongan kerja dapat dibuat dari menu
                    <a href="{{ route('recruitment.postings.index') }}" class="text-indigo-600 hover:underline">Job Posting</a>.
                </p>
            @endcan
        @endif

        @if(in_array($jobPermintaan->status, ['rejected', 'fulfilled']))
            <p class="text-sm text-gray-500">Tidak ada tindakan tersedia untuk status ini.</p>
        @endif
    </div>

    {{-- Approval History --}}
    @if($jobPermintaan->approvals->count())
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Riwayat Persetujuan</h2>
            <div class="space-y-3">
                @foreach($jobPermintaan->approvals->sortBy('acted_at') as $approval)
                    <div class="flex items-start gap-3 text-sm">
                        <span class="mt-0.5 material-symbols-outlined text-base
                            {{ $approval->decision === 'approved' ? 'text-green-500' : 'text-red-500' }}">
                            {{ $approval->decision === 'approved' ? 'check_circle' : 'cancel' }}
                        </span>
                        <div class="flex-1">
                            <span class="font-medium text-gray-800">{{ $approval->actedBy->full_name ?? '-' }}</span>
                            <span class="text-gray-500 mx-1">—</span>
                            <span class="{{ $approval->decision === 'approved' ? 'text-green-700' : 'text-red-700' }} font-medium">
                                {{ $approval->decision === 'approved' ? 'Disetujui' : 'Ditolak' }}
                            </span>
                            <span class="text-gray-400 ml-2 text-xs">
                                ({{ $approval->approval_step === 'hrd_review' ? 'Review HR' : 'Persetujuan Ops' }})
                            </span>
                            <span class="text-gray-400 ml-2 text-xs">
                                {{ \Carbon\Carbon::parse($approval->acted_at)->format('d M Y H:i') }}
                            </span>
                            @if($approval->notes)
                                <p class="text-gray-600 mt-0.5 italic text-xs">{{ $approval->notes }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
