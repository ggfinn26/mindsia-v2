@extends('layouts.dashboard')

@section('title', 'Review Izin, Sakit, & Cuti - MINDSIA')
@section('header_title', 'Review Izin, Sakit, & Cuti')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{ showRejectModal: false, rejectActionUrl: '' }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Presensi</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Review Izin, Sakit, &amp; Cuti</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Verifikasi dan tinjau pengajuan ketidakhadiran pegawai.
            </p>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- Filter Form --}}
    <form method="GET"
          class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Status</label>
            <select name="status"
                    class="px-3 py-2 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Review</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                Filter
            </button>
            @if(request()->filled('status'))
            <a href="{{ route('leave-requests.manage') }}"
               class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                Reset
            </a>
            @endif
        </div>
    </form>

    {{-- Table Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Jenis</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Alasan</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        @php
                            $statusMap = [
                                'approved'  => ['Disetujui',  'background-color: #f0fdf4; color: #059669; border: 1px solid #bbf7d0;'],
                                'rejected'  => ['Ditolak',    'background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca;'],
                                'cancelled' => ['Dibatalkan', 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);'],
                                'pending'   => ['Menunggu',   'background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a;'],
                            ];
                            [$statusLabel, $badgeStyle] = $statusMap[$req->status] ?? [ucfirst($req->status), 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);'];
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5">
                                <div class="font-medium" style="color: var(--dash-text-heading);">
                                    {{ $req->employee->full_name ?? '-' }}
                                </div>
                                <div class="text-xs mt-0.5" style="color: var(--dash-text-faint);">
                                    {{ $req->employee->employee_code ?? '' }}
                                </div>
                            </td>
                            <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-body);">
                                {{ $req->leaveTypeLabel() ?? ucfirst(str_replace('_', ' ', $req->leave_type)) }}
                            </td>
                            <td class="px-5 py-3.5 text-xs" style="color: var(--dash-text-muted);">
                                {{ $req->start_date->translatedFormat('d M Y') }}
                                @if($req->start_date != $req->end_date)
                                    s/d {{ $req->end_date->translatedFormat('d M Y') }}
                                @endif
                            </td>
                            <td class="px-5 py-3.5 max-w-xs truncate" style="color: var(--dash-text-body);" title="{{ $req->reason }}">
                                {{ $req->reason }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      style="{{ $badgeStyle }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                @if($req->status === 'pending')
                                    <form action="{{ route('leave-requests.approve', $req->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity mr-1.5"
                                                style="background-color: #059669;">
                                            <span class="material-symbols-outlined text-[14px]">check</span>
                                            Setujui
                                        </button>
                                    </form>
                                    <button type="button"
                                            @click="showRejectModal = true; rejectActionUrl = '{{ route('leave-requests.reject', $req->id) }}'"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                            style="background-color: #dc2626;">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                        Tolak
                                    </button>
                                @else
                                    <span style="color: var(--dash-text-faint);">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">event_busy</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Tidak ada pengajuan izin/cuti.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    {{-- Reject Modal --}}
    <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-reject-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <!-- Backdrop -->
            <div x-show="showRejectModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/60 transition-opacity"
                 @click="showRejectModal = false"
                 aria-hidden="true"></div>

            <!-- Panel -->
            <div x-show="showRejectModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative inline-block rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-md w-full z-10"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <form :action="rejectActionUrl" method="POST">
                    @csrf
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full"
                                 style="background-color: #fee2e2;">
                                <span class="material-symbols-outlined text-[22px]" style="color: #dc2626;">cancel</span>
                            </div>
                            <h3 class="font-jakarta text-base font-bold"
                                style="color: var(--dash-text-heading);"
                                id="modal-reject-title">
                                Tolak Pengajuan
                            </h3>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                                Alasan Penolakan <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="rejection_reason" rows="3" required
                                      class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                      style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                      onfocus="this.style.borderColor='var(--dash-primary)'"
                                      onblur="this.style.borderColor='var(--dash-input-border)'"
                                      placeholder="Tuliskan alasan penolakan..."></textarea>
                        </div>
                    </div>
                    <div class="px-6 py-4 flex items-center justify-end gap-3"
                         style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <button type="button" @click="showRejectModal = false"
                                class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: #dc2626;">
                            Tolak Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

