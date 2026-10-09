@extends('layouts.dashboard')

@section('title', 'Review Izin / Sakit / Cuti - MINDSIA')
@section('header_title', 'Review Izin / Sakit / Cuti')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6"
     x-data="{ showRejectModal: false, rejectActionUrl: '' }">

    {{-- Page header --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Absensi</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Review Izin / Sakit / Cuti</h1>
        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
            Tinjau dan setujui permohonan ketidakhadiran pegawai.
        </p>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Table card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        {{-- Filter bar --}}
        <div class="px-5 py-3" style="border-bottom: 1px solid var(--dash-card-border);">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-xs font-medium" style="color: var(--dash-text-muted);">Status</label>
                    <select name="status"
                            class="rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body); --tw-ring-color: var(--dash-primary);">
                        <option value="">Semua</option>
                        <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Menunggu Review</option>
                        <option value="approved"  {{ request('status') === 'approved'  ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Ditolak</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    Filter
                </button>
                @if(request('status'))
                    <a href="{{ route('leave-requests.manage') }}"
                       class="inline-flex items-center gap-1 text-xs font-medium transition-opacity hover:opacity-75"
                       style="color: var(--dash-text-muted);">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-fixed">
                <colgroup>
                    <col class="w-[24%]">
                    <col class="hidden sm:table-column w-[10%]">
                    <col class="hidden md:table-column w-[18%]">
                    <col class="hidden lg:table-column">{{-- auto: sisa width --}}
                    <col class="w-[14%]">
                    <col class="w-[10%]">
                </colgroup>
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="hidden sm:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Jenis</th>
                        <th class="hidden md:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Tanggal</th>
                        <th class="hidden lg:table-cell px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Alasan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-5 py-4">
                                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                                    {{ $req->employee?->full_name ?? '—' }}
                                </p>
                                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                    {{ $req->employee?->employee_code ?? '' }}
                                </p>
                                {{-- Mobile: jenis + tanggal inline --}}
                                <p class="sm:hidden text-xs mt-1" style="color: var(--dash-text-muted);">
                                    @php
                                        $typeLabel = match($req->leave_type ?? '') {
                                            'sick'       => 'Sakit',
                                            'permission' => 'Izin',
                                            'leave'      => 'Cuti',
                                            default      => ucfirst(str_replace('_', ' ', $req->leave_type ?? '')),
                                        };
                                    @endphp
                                    {{ $typeLabel }} ·
                                    {{ $req->start_date?->format('d M Y') }}
                                    @if($req->start_date?->format('Y-m-d') !== $req->end_date?->format('Y-m-d'))
                                        s/d {{ $req->end_date?->format('d M Y') }}
                                    @endif
                                </p>
                            </td>
                            <td class="hidden sm:table-cell px-5 py-4 text-sm"
                                style="color: var(--dash-text-body);">
                                @php
                                    $typeLabel = match($req->leave_type ?? '') {
                                        'sick'       => 'Sakit',
                                        'permission' => 'Izin',
                                        'leave'      => 'Cuti',
                                        default      => ucfirst(str_replace('_', ' ', $req->leave_type ?? '')),
                                    };
                                @endphp
                                {{ $typeLabel }}
                            </td>
                            <td class="hidden md:table-cell px-5 py-4 text-sm"
                                style="color: var(--dash-text-muted);">
                                {{ $req->start_date?->format('d M Y') }}
                                @if($req->start_date?->format('Y-m-d') !== $req->end_date?->format('Y-m-d'))
                                    <br><span style="color: var(--dash-text-faint);">s/d</span> {{ $req->end_date?->format('d M Y') }}
                                @endif
                            </td>
                            <td class="hidden lg:table-cell px-5 py-4 text-sm truncate"
                                style="color: var(--dash-text-body);"
                                title="{{ $req->reason }}">
                                {{ $req->reason }}
                            </td>
                            <td class="px-5 py-4">
                                @if($req->status === 'pending')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                          style="background-color: #fef3c7; color: #d97706; border-color: #fcd34d;">
                                        <span class="material-symbols-outlined text-[12px]">schedule</span>
                                        Menunggu
                                    </span>
                                @elseif($req->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                          style="background-color: #f0fdf4; color: #059669; border-color: #bbf7d0;">
                                        <span class="material-symbols-outlined text-[12px]">check_circle</span>
                                        Disetujui
                                    </span>
                                @elseif($req->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                          style="background-color: #fee2e2; color: #dc2626; border-color: #fca5a5;">
                                        <span class="material-symbols-outlined text-[12px]">cancel</span>
                                        Ditolak
                                    </span>
                                @elseif($req->status === 'cancelled')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border-color: var(--dash-card-border);">
                                        <span class="material-symbols-outlined text-[12px]">remove_circle</span>
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border-color: var(--dash-card-border);">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-4 text-right">
                                @if($req->status === 'pending')
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <form action="{{ route('leave-requests.approve', $req->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    title="Setujui"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                                    style="background-color: #f0fdf4; color: #059669; border: 1px solid #bbf7d0;"
                                                    onmouseover="this.style.backgroundColor='#dcfce7'"
                                                    onmouseout="this.style.backgroundColor='#f0fdf4'">
                                                <span class="material-symbols-outlined text-[16px]">check</span>
                                            </button>
                                        </form>
                                        <button type="button"
                                                title="Tolak"
                                                @click="showRejectModal = true; rejectActionUrl = '{{ route('leave-requests.reject', $req->id) }}'"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
                                                style="background-color: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;"
                                                onmouseover="this.style.backgroundColor='#fecaca'"
                                                onmouseout="this.style.backgroundColor='#fee2e2'">
                                            <span class="material-symbols-outlined text-[16px]">close</span>
                                        </button>
                                    </div>
                                @else
                                    <span style="color: var(--dash-text-faint);">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">event_available</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
                                    Tidak ada pengajuan yang perlu ditinjau.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="px-5 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    {{-- Reject modal --}}
    <div x-show="showRejectModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/50"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showRejectModal = false"></div>

        {{-- Panel --}}
        <div class="relative rounded-2xl p-6 max-w-md w-full shadow-xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <div class="flex items-start gap-4 mb-5">
                <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full"
                     style="background-color: #fee2e2;">
                    <span class="material-symbols-outlined text-[20px]" style="color: #dc2626;">block</span>
                </div>
                <div>
                    <h3 class="text-base font-semibold" style="color: var(--dash-text-heading);">Tolak Pengajuan</h3>
                    <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">Berikan alasan penolakan untuk pegawai.</p>
                </div>
            </div>

            <form :action="rejectActionUrl" method="POST">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-medium mb-2" style="color: var(--dash-text-body);">
                        Alasan Penolakan <span style="color: #dc2626;">*</span>
                    </label>
                    <textarea name="rejection_reason" rows="3" required
                              class="block w-full rounded-lg text-sm focus:outline-none focus:ring-2"
                              style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body); padding: 0.625rem 0.75rem; --tw-ring-color: #dc2626;"
                              placeholder="Tuliskan alasan penolakan..."></textarea>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button"
                            @click="showRejectModal = false"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: #dc2626;">
                        Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
