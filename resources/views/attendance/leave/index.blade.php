@extends('layouts.dashboard')

@section('title', 'Izin, Sakit, & Cuti - MINDSIA')
@section('header_title', 'Izin, Sakit, & Cuti')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
        showConfirmModal: false,
        confirmTitle: '',
        confirmMessage: '',
        confirmActionUrl: '',
        confirmMethod: 'POST',
        confirmActionText: 'Konfirmasi'
     }">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Personal Dashboard</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Izin, Sakit, &amp; Cuti</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola riwayat ketidakhadiran dan ajukan permohonan baru.
            </p>
        </div>

        <a href="{{ route('leave-requests.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Ajukan Permohonan
        </a>
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

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">No</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Tipe &amp; Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Keterangan</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $leave)
                        @php
                            $typeLabel = match($leave->leave_type) {
                                'sick' => 'Sakit',
                                'permission' => 'Izin',
                                'leave' => 'Cuti',
                                default => \App\Support\EnumLabels::get('leave_type', $leave->leave_type),
                            };

                            $statusMap = [
                                'pending'   => ['Menunggu',   'background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a;'],
                                'approved'  => ['Disetujui',  'background-color: #f0fdf4; color: #059669; border: 1px solid #bbf7d0;'],
                                'rejected'  => ['Ditolak',    'background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca;'],
                                'cancelled' => ['Dibatalkan', 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);'],
                            ];
                            [$statusText, $badgeStyle] = $statusMap[$leave->status] ?? [\App\Support\EnumLabels::get('request_status', $leave->status), 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);'];
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5 text-sm" style="color: var(--dash-text-faint);">
                                {{ $loop->iteration + $requests->firstItem() - 1 }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                                    {{ $typeLabel }}
                                </div>
                                <div class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                    {{ \Carbon\Carbon::parse($leave->start_date)->translatedFormat('d M Y') }}
                                    @if($leave->start_date !== $leave->end_date)
                                        - {{ \Carbon\Carbon::parse($leave->end_date)->translatedFormat('d M Y') }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 max-w-md truncate" style="color: var(--dash-text-body);" title="{{ $leave->reason }}">
                                {{ $leave->reason }}
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      style="{{ $badgeStyle }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if($leave->status === 'pending')
                                    <button type="button"
                                            @click="showConfirmModal = true; confirmTitle = 'Batalkan Permohonan'; confirmMessage = 'Apakah Anda yakin ingin membatalkan permohonan ini? Tindakan ini tidak dapat diurungkan.'; confirmActionUrl = '{{ route('leave-requests.cancel', $leave->id) }}'; confirmMethod = 'POST'; confirmActionText = 'Batalkan Permohonan'"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                            style="background-color: #dc2626;">
                                        <span class="material-symbols-outlined text-[14px]">cancel</span>
                                        Batalkan
                                    </button>
                                @else
                                    <span style="color: var(--dash-text-faint);">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">event_busy</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada riwayat permohonan</p>
                                <p class="text-xs mt-1 mb-4" style="color: var(--dash-text-faint);">
                                    Ajukan permohonan izin, sakit, atau cuti pertama Anda.
                                </p>
                                <a href="{{ route('leave-requests.create') }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                   style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    Ajukan Permohonan
                                </a>
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

    {{-- Confirmation Modal --}}
    <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <!-- Background overlay -->
            <div x-show="showConfirmModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/60 transition-opacity"
                 @click="showConfirmModal = false"
                 aria-hidden="true"></div>

            <!-- Modal panel -->
            <div x-show="showConfirmModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative inline-block rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:max-w-md w-full z-10"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full"
                             style="background-color: #fee2e2;">
                            <span class="material-symbols-outlined text-[22px]" style="color: #dc2626;">warning</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-jakarta text-base font-bold"
                                style="color: var(--dash-text-heading);"
                                id="modal-title"
                                x-text="confirmTitle"></h3>
                            <p class="text-sm mt-1.5"
                               style="color: var(--dash-text-muted);"
                               x-text="confirmMessage"></p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 flex items-center justify-end gap-3"
                     style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <button type="button"
                            class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"
                            @click="showConfirmModal = false">
                        Batal
                    </button>
                    <form :action="confirmActionUrl" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="_method" :value="confirmMethod">
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: #dc2626;"
                                x-text="confirmActionText">
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

