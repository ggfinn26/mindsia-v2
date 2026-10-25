@extends('layouts.dashboard')

@section('title', 'Job Permintaan')
@section('header_title', 'Job Permintaan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
        showDeleteModal: false,
        deleteActionUrl: '',
        deleteItemName: '',
        openDelete(url, name) {
            this.deleteActionUrl = url;
            this.deleteItemName = name;
            this.showDeleteModal = true;
        }
     }">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Rekrutmen</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Job Permintaan</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola pengajuan kebutuhan tenaga kerja dan status persetujuan cabang.
            </p>
        </div>

        @can('recruitment.job_permintaan.create')
            <a href="{{ route('recruitment.permintaan.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white transition-opacity hover:opacity-90 shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Permintaan
            </a>
        @endcan
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
    <form method="GET" action="{{ route('recruitment.permintaan.index') }}"
          class="p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="w-full sm:w-64">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Status Permintaan</label>
                <select name="status"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Semua Status</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="pending_hr_review" @selected(request('status') === 'pending_hr_review')>Pending HR Review</option>
                    <option value="pending_ops_approval" @selected(request('status') === 'pending_ops_approval')>Pending Ops Approval</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                    <option value="fulfilled" @selected(request('status') === 'fulfilled')>Fulfilled</option>
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
                    <a href="{{ route('recruitment.permintaan.index') }}"
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
                            style="color: var(--dash-text-muted);">Posisi & Cabang</th>
                        <th class="w-1/4 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Jenis & Headcount</th>
                        <th class="w-1/6 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="w-1/5 px-4 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($permintaans as $permintaan)
                        @php
                            $statusMap = [
                                'draft' => ['bg' => 'bg-slate-100 dark:bg-slate-800', 'text' => 'text-slate-700 dark:text-slate-300', 'label' => 'Draft'],
                                'pending_hr_review' => ['bg' => 'bg-amber-100 dark:bg-amber-950/40', 'text' => 'text-amber-700 dark:text-amber-400', 'label' => 'Pending HR Review'],
                                'pending_ops_approval' => ['bg' => 'bg-blue-100 dark:bg-blue-950/40', 'text' => 'text-blue-700 dark:text-blue-400', 'label' => 'Pending Ops Approval'],
                                'approved' => ['bg' => 'bg-emerald-100 dark:bg-emerald-950/40', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'Approved'],
                                'rejected' => ['bg' => 'bg-red-100 dark:bg-red-950/40', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'Rejected'],
                                'fulfilled' => ['bg' => 'bg-purple-100 dark:bg-purple-950/40', 'text' => 'text-purple-700 dark:text-purple-400', 'label' => 'Fulfilled'],
                            ];
                            $st = $statusMap[$permintaan->status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700'];
                            $st['label'] = \App\Support\EnumLabels::get('job_request_status', $permintaan->status);
                            $posName = optional($permintaan->detail)->position->position_name ?? optional($permintaan->detail)->position->name ?? '-';
                            $branchName = $permintaan->branch->branch_name ?? '-';
                            $requestType = \App\Support\EnumLabels::get('request_type', optional($permintaan->detail)->request_type);
                            $empType = \App\Support\EnumLabels::get('employment_type', optional($permintaan->detail)->employment_type);
                            $currentHc = optional($permintaan->detail)->current_headcount ?? 0;
                            $targetHc = optional($permintaan->detail)->headcount ?? 0;
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3 text-center text-xs" style="color: var(--dash-text-muted);">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('recruitment.permintaan.show', $permintaan->id) }}"
                                   class="font-semibold block hover:underline transition-colors text-xs sm:text-sm truncate"
                                   style="color: var(--dash-primary-text);">
                                    {{ $posName }}
                                </a>
                                <p class="text-[11px] mt-0.5 flex items-center gap-1.5 truncate" style="color: var(--dash-text-muted);">
                                    <span class="material-symbols-outlined text-[13px]">location_on</span>
                                    <span>{{ $branchName }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $permintaan->created_at?->format('d M Y') ?? '-' }}</span>
                                </p>
                            </td>
                            <td class="px-4 py-3 text-xs">
                                <div class="font-medium" style="color: var(--dash-text-heading);">
                                    {{ $requestType }}
                                </div>
                                <p class="text-[11px] mt-0.5" style="color: var(--dash-text-muted);">
                                    Kuota: <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $currentHc }} / {{ $targetHc }}</span> orang ({{ $empType }})
                                </p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 text-[11px] font-semibold rounded-full {{ $st['bg'] }} {{ $st['text'] }}">
                                    {{ $st['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('recruitment.permintaan.show', $permintaan->id) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                                        Lihat
                                    </a>

                                    @if($permintaan->status === 'draft')
                                        @can('recruitment.job_permintaan.update')
                                            <a href="{{ route('recruitment.permintaan.edit', $permintaan->id) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">edit</span>
                                                Edit
                                            </a>
                                        @endcan

                                        @can('recruitment.job_permintaan.create')
                                            <button type="button"
                                                    @click="openDelete('{{ route('recruitment.permintaan.destroy', $permintaan->id) }}', '{{ addslashes($posName) }} ({{ addslashes($branchName) }})')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg transition-opacity hover:opacity-90"
                                                    style="background-color: var(--dash-danger, #dc2626);">
                                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                                Hapus
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">work_history</span>
                                <h3 class="font-jakarta text-base font-semibold" style="color: var(--dash-text-heading);">Tidak ada job permintaan ditemukan</h3>
                                <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Coba ubah filter status atau buat permintaan baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($permintaans->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $permintaans->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Konfirmasi Hapus Kustom Alpine.js (Zero Browser Confirm, Zero Overflow-Hidden) --}}
    <template x-teleport="body">
        <div x-show="showDeleteModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showDeleteModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showDeleteModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showDeleteModal" @click.away="showDeleteModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-950/40 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-[22px]">warning</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Job Permintaan?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Permintaan untuk <span class="font-semibold" style="color: var(--dash-text-heading);" x-text="deleteItemName"></span> akan dihapus permanen.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showDeleteModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <form method="POST" :action="deleteActionUrl" class="m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                    Ya, Hapus
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
