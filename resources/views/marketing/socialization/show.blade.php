@extends('layouts.dashboard')

@section('title', 'Detail Sosialisasi - MINDSIA')
@section('header_title', 'Detail Sosialisasi')

@section('content')
@php
    $statusBorderLeft = [
        'draft'                => 'border-l-amber-400',
        'scheduled'            => 'border-l-blue-500',
        'awaiting_validation'  => 'border-l-purple-500',
        'completed'            => 'border-l-emerald-500',
        'cancelled'            => 'border-l-red-400',
    ];
    $statusBadge = [
        'draft'                => 'bg-amber-50 text-amber-700 border-amber-200',
        'scheduled'            => 'bg-blue-50 text-blue-700 border-blue-200',
        'awaiting_validation'  => 'bg-purple-50 text-purple-700 border-purple-200',
        'completed'            => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'cancelled'            => 'bg-red-50 text-red-700 border-red-200',
    ];
    $statusLabels = [
        'draft'                => 'Draft',
        'scheduled'            => 'Terjadwal',
        'awaiting_validation'  => 'Menunggu Validasi',
        'completed'            => 'Selesai',
        'cancelled'            => 'Dibatalkan',
    ];
    $feeStatusBadge = [
        'none'    => 'bg-gray-100 text-gray-600 border-gray-200',
        'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
        'paid'    => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    ];
    $feeStatusLabels = ['none' => 'Tidak Ada', 'pending' => 'Belum Bayar', 'paid' => 'Sudah Bayar'];
    $leadStatusColors = [
        'ALMOST' => 'bg-yellow-100 text-yellow-700',
        'YES'    => 'bg-blue-100 text-blue-700',
        'FIXED'  => 'bg-emerald-100 text-emerald-700',
        'NO'     => 'bg-red-100 text-red-700',
    ];
    $leadStatusLabels = ['ALMOST' => 'Hampir', 'YES' => 'Berminat', 'FIXED' => 'Pasti', 'NO' => 'Tidak'];
    $toggleTargets = [
        'ALMOST' => ['?', 'border-yellow-300 text-yellow-600 hover:bg-yellow-50'],
        'YES'    => ['✓', 'border-emerald-300 text-emerald-600 hover:bg-emerald-50'],
        'NO'     => ['✕', 'border-red-300 text-red-500 hover:bg-red-50'],
    ];

    $displayName = $socialization->institution?->institution_name ?? $socialization->location_name ?? '-';
    $totalLeads   = $leads->total();
    $countAlmost  = $socialization->prospectiveMembers->where('status', 'ALMOST')->count();
    $countYes     = $socialization->prospectiveMembers->where('status', 'YES')->count();
    $countFixed   = $socialization->prospectiveMembers->where('status', 'FIXED')->count();
    $countNo      = $socialization->prospectiveMembers->where('status', 'NO')->count();
    $totalKelas   = $socialization->employeeSocializations->sum('classes_obtained');

    $pmSort    = request('pm_sort', 'created_at');
    $pmDir     = request('pm_dir', 'desc');
    $pmSortUrl = fn(string $col) => request()->fullUrlWithQuery([
        'pm_sort' => $col,
        'pm_dir'  => ($pmSort === $col && $pmDir === 'asc') ? 'desc' : 'asc',
        'page'    => 1,
    ]);
    $pmSortIcon = fn(string $col) => $pmSort === $col ? ($pmDir === 'asc' ? ' ↑' : ' ↓') : '';
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto space-y-6"
    x-data="{
        showAssignModal: false,
        showFeeModal: false,
        showCancelModal: false,
        showForceCancelModal: false,
        showCompleteModal: false,
        showScheduleModal: false,
        showDeleteModal: false
    }">

    {{-- Page Header Pola B3 --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <a href="{{ route('socializations.index') }}" class="dash-back mb-3">
                <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Sosialisasi
            </a>
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusBadge[$socialization->status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                    {{ $statusLabels[$socialization->status] ?? $socialization->status }}
                </span>
                @if ($socialization->area)
                    <span class="text-xs font-medium" style="color: var(--dash-text-muted);">{{ $socialization->area->name }}</span>
                @endif
            </div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">{{ $displayName }}</h1>
            @if ($socialization->institution && $socialization->location_name)
                <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">{{ $socialization->location_name }}</p>
            @endif
            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Dibuat oleh {{ $socialization->createdBy->full_name ?? '-' }}</p>
        </div>

        {{-- Actions header --}}
        <div class="flex items-center gap-2 shrink-0 flex-wrap">
            @can('marketing.socialization.update')
                @if ($socialization->status === 'draft')
                    <a href="{{ route('socializations.edit', $socialization) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="color: var(--dash-text-body); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                        Edit
                    </a>
                    <button type="button" @click="showScheduleModal = true"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">event_available</span>
                        Jadwalkan
                    </button>
                @endif
                @if (in_array($socialization->status, ['draft', 'scheduled']))
                    <button type="button" @click="showCancelModal = true"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="color: #dc2626; border: 1px solid #fecaca; background-color: #fef2f2;">
                        <span class="material-symbols-outlined text-[16px]">cancel</span>
                        Batalkan
                    </button>
                @endif
                @if (in_array($socialization->status, ['scheduled', 'awaiting_validation']))
                    <button type="button" @click="showCompleteModal = true"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: #059669;">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        Selesaikan
                    </button>
                @endif
            @endcan
            @can('marketing.socialization.force_cancel')
                @if ($socialization->status === 'awaiting_validation')
                    <button type="button" @click="showForceCancelModal = true"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                        style="color: #dc2626; border: 1px solid #fecaca; background-color: #fef2f2;">
                        <span class="material-symbols-outlined text-[16px]">cancel</span>
                        Batalkan Paksa
                    </button>
                @endif
            @endcan
            @can('marketing.socialization.delete')
                @if ($socialization->status === 'draft')
                    <button type="button" @click="showDeleteModal = true"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-danger, #dc2626);">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        Hapus
                    </button>
                @endif
            @endcan
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="rounded-2xl p-4 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-2xl font-bold" style="color: var(--dash-text-heading);">{{ $totalLeads }}</p>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Total PM</p>
        </div>
        <div class="rounded-2xl p-4 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-2xl font-bold text-yellow-600">{{ $countAlmost }}</p>
            <p class="text-xs text-yellow-600/80 mt-1">Hampir</p>
        </div>
        <div class="rounded-2xl p-4 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-2xl font-bold text-blue-600">{{ $countYes }}</p>
            <p class="text-xs text-blue-600/80 mt-1">Berminat</p>
        </div>
        <div class="rounded-2xl p-4 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-2xl font-bold text-emerald-600">{{ $countFixed }}</p>
            <p class="text-xs text-emerald-600/80 mt-1">Pasti</p>
        </div>
        <div class="rounded-2xl p-4 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-2xl font-bold text-red-500">{{ $countNo }}</p>
            <p class="text-xs text-red-500/80 mt-1">Tidak</p>
        </div>
        <div class="rounded-2xl p-4 text-center"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <p class="text-2xl font-bold" style="color: var(--dash-text-heading);">{{ $totalKelas }}</p>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted);">Total Kelas</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Per-employee progress (MA/PIC view) --}}
            @can('marketing.socialization.update')
            <div class="rounded-2xl"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Progress Marketer</h2>
                    @if (!in_array($socialization->status, ['completed', 'cancelled']) && $eligibleEmployees->isNotEmpty())
                        <button type="button" @click="showAssignModal = true"
                            class="inline-flex items-center gap-1 text-xs font-semibold hover:underline"
                            style="color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[14px]">person_add</span>
                            Tugaskan
                        </button>
                    @endif
                </div>

                @if ($socialization->employeeSocializations->isEmpty())
                    <div class="py-8 text-center">
                        <span class="material-symbols-outlined text-[40px] block mx-auto mb-2" style="color: var(--dash-text-faint);">badge</span>
                        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada marketer yang ditugaskan.</p>
                    </div>
                @else
                    <div class="divide-y" style="border-color: var(--dash-card-border);">
                        @foreach ($socialization->employeeSocializations as $es)
                            @php
                                $leadCount = $socialization->prospectiveMembers
                                    ->where('captured_by_employee_id', $es->employee_id)->count();
                                $fixedCount = $socialization->prospectiveMembers
                                    ->where('captured_by_employee_id', $es->employee_id)
                                    ->where('status', 'FIXED')->count();
                            @endphp
                            <div class="px-6 py-4" x-data="{ editKelas: false }">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $es->employee->full_name ?? '-' }}</p>
                                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                            {{ $leadCount }} PM · {{ $fixedCount }} Pasti
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <div class="text-right">
                                            <p class="text-lg font-bold" style="color: var(--dash-text-heading);">{{ $es->classes_obtained ?? 0 }}</p>
                                            <p class="text-[10px]" style="color: var(--dash-text-faint);">kelas</p>
                                        </div>
                                        <span class="text-xs px-2.5 py-0.5 rounded-full shrink-0 font-medium
                                            {{ $es->status === 'confirmed' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $es->status === 'confirmed' ? 'Dikonfirmasi' : 'Belum' }}
                                        </span>
                                        @if (!in_array($socialization->status, ['completed', 'cancelled']))
                                            <button type="button" @click="editKelas = !editKelas"
                                                class="p-1 rounded hover:opacity-80 transition-opacity"
                                                style="color: var(--dash-text-muted);">
                                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Override form --}}
                                <div x-show="editKelas" x-cloak class="mt-3 pt-3 border-t" style="border-color: var(--dash-card-border);">
                                    <p class="text-xs mb-2 font-medium" style="color: #d97706;">Perhatian: Override data marketer tersimpan dalam riwayat audit sistem.</p>
                                    <form action="{{ route('socializations.override-kelas', [$socialization, $es]) }}" method="POST"
                                        class="flex gap-2 items-end">
                                        @csrf @method('PATCH')
                                        <div class="flex-1">
                                            <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Jumlah kelas (override)</label>
                                            <input type="number" name="classes_obtained"
                                                value="{{ $es->classes_obtained ?? 0 }}"
                                                min="0" required
                                                class="w-full px-3 py-2 rounded-lg text-sm focus:outline-none transition-all"
                                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                                onblur="this.style.borderColor='var(--dash-input-border)'">
                                        </div>
                                        <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                            style="background-color: var(--dash-primary);">
                                            <span class="material-symbols-outlined text-[14px]">save</span>
                                            Simpan
                                        </button>
                                        <button type="button" @click="editKelas = false"
                                            class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            Batal
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endcan

            {{-- Filter lead --}}
            <form method="GET" action="{{ route('socializations.show', $socialization) }}"
                class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex-1 min-w-[180px]">
                    <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Cari PM</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                              style="color: var(--dash-text-muted);">search</span>
                        <input type="text" name="pm_search" value="{{ request('pm_search') }}"
                            placeholder="Nama atau WhatsApp..."
                            class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                            style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    </div>
                </div>
                <div class="w-full sm:w-40">
                    <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Status</label>
                    <select name="pm_status" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Semua Status</option>
                        @foreach (['ALMOST' => 'Hampir', 'YES' => 'Berminat', 'FIXED' => 'Pasti', 'NO' => 'Tidak'] as $val => $label)
                            <option value="{{ $val }}" {{ request('pm_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">search</span>
                        Cari
                    </button>
                    @if (request()->hasAny(['pm_search', 'pm_status']))
                        <a href="{{ route('socializations.show', $socialization) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors"
                           style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Lead list --}}
            <div class="rounded-2xl"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-center justify-between px-6 py-4 rounded-t-2xl"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                        Daftar Calon Anggota ({{ $totalLeads }})
                    </p>
                    <div class="flex items-center gap-3 text-xs" style="color: var(--dash-text-muted);">
                        <a href="{{ $pmSortUrl('full_name') }}" class="hover:underline">Nama{{ $pmSortIcon('full_name') }}</a>
                        <span>·</span>
                        <a href="{{ $pmSortUrl('status') }}" class="hover:underline">Status{{ $pmSortIcon('status') }}</a>
                        <span>·</span>
                        <a href="{{ $pmSortUrl('created_at') }}" class="hover:underline">Terbaru{{ $pmSortIcon('created_at') }}</a>
                    </div>
                </div>

                @if ($leads->isEmpty())
                    <div class="py-12 text-center">
                        <span class="material-symbols-outlined text-[48px] block mx-auto mb-3" style="color: var(--dash-text-faint);">group_add</span>
                        <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
                            {{ request()->hasAny(['pm_search', 'pm_status']) ? 'Tidak ada calon anggota yang cocok.' : 'Belum ada data calon anggota.' }}
                        </p>
                    </div>
                @else
                    <div class="divide-y" style="border-color: var(--dash-card-border);">
                        @foreach ($leads as $lead)
                            @php
                                $waNum = preg_replace('/[^0-9]/', '', $lead->whatsapp_number ?? '');
                                if (str_starts_with($waNum, '0')) $waNum = '62' . substr($waNum, 1);
                                elseif ($waNum && ! str_starts_with($waNum, '62')) $waNum = '62' . $waNum;
                            @endphp
                            <div class="flex items-center gap-3 px-6 py-3.5 transition-colors"
                                 onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                 onmouseout="this.style.backgroundColor='transparent'">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('prospective-members.show', $lead) }}"
                                            class="text-sm font-semibold hover:underline"
                                            style="color: var(--dash-text-heading);">
                                            {{ $lead->full_name }}
                                        </a>
                                        @if ($waNum)
                                            <div class="relative" x-data="waPicker({
                                                wa: '{{ $waNum }}',
                                                nama: @js($lead->full_name),
                                                institusi: @js($lead->institution?->institution_name ?? $lead->institution_name ?? ''),
                                                sosialisasi: @js($displayName),
                                                status: '{{ $lead->status }}'
                                            })" @click.away="open = false">
                                                <button type="button" @click="open = !open"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded-full hover:bg-emerald-50 focus:outline-none transition-colors"
                                                    title="{{ $lead->whatsapp_number }}">
                                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 362" fill="none" aria-label="WhatsApp"><path fill="#25D366" fill-rule="evenodd" d="M307.546 52.566C273.709 18.684 228.706.017 180.756 0 81.951 0 1.538 80.404 1.504 179.235c-.017 31.594 8.242 62.432 23.928 89.609L0 361.736l95.024-24.925c26.179 14.285 55.659 21.805 85.655 21.814h.077c98.788 0 179.21-80.413 179.244-179.244.017-47.898-18.608-92.926-52.454-126.807v-.008Zm-126.79 275.788h-.06c-26.73-.008-52.952-7.194-75.831-20.765l-5.44-3.231-56.391 14.791 15.05-54.981-3.542-5.638c-14.912-23.721-22.793-51.139-22.776-79.286.035-82.14 66.867-148.973 149.051-148.973 39.793.017 77.198 15.53 105.328 43.695 28.131 28.157 43.61 65.596 43.593 105.398-.035 82.149-66.867 148.982-148.982 148.982v.008Zm81.719-111.577c-4.478-2.243-26.497-13.073-30.606-14.568-4.108-1.496-7.09-2.243-10.073 2.243-2.982 4.487-11.568 14.577-14.181 17.559-2.613 2.991-5.226 3.361-9.704 1.117-4.477-2.243-18.908-6.97-36.02-22.226-13.313-11.878-22.304-26.54-24.916-31.027-2.613-4.486-.275-6.91 1.959-9.136 2.011-2.011 4.478-5.234 6.721-7.847 2.244-2.613 2.983-4.486 4.478-7.469 1.496-2.991.748-5.603-.369-7.847-1.118-2.243-10.073-24.289-13.812-33.253-3.636-8.732-7.331-7.546-10.073-7.692-2.613-.13-5.595-.155-8.586-.155-2.991 0-7.839 1.118-11.947 5.604-4.108 4.486-15.677 15.324-15.677 37.361s16.047 43.344 18.29 46.335c2.243 2.991 31.585 48.225 76.51 67.632 10.684 4.615 19.029 7.374 25.535 9.437 10.727 3.412 20.49 2.931 28.208 1.779 8.604-1.289 26.498-10.838 30.228-21.298 3.73-10.46 3.73-19.433 2.613-21.298-1.117-1.865-4.108-2.991-8.586-5.234l.008-.017Z" clip-rule="evenodd"/></svg>
                                                </button>
                                                <div x-show="open" x-transition
                                                    class="absolute left-0 top-8 z-20 w-56 rounded-xl shadow-xl py-1.5"
                                                    style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                                    <button type="button" @click="send(null)"
                                                        class="w-full text-left px-3.5 py-2 text-xs font-medium transition-colors"
                                                        style="color: var(--dash-text-body);"
                                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                        onmouseout="this.style.backgroundColor='transparent'">
                                                        Kirim langsung
                                                    </button>
                                                    <template x-if="templates.length > 0">
                                                        <div>
                                                            <p class="px-3.5 py-1 text-[10px] font-semibold uppercase tracking-wider border-t mt-1"
                                                               style="color: var(--dash-text-faint); border-color: var(--dash-card-border);">Template Pesan</p>
                                                            <template x-for="t in templates" :key="t.id">
                                                                <button type="button" @click="send(t)"
                                                                    class="w-full text-left px-3.5 py-2 text-xs font-medium transition-colors"
                                                                    style="color: var(--dash-text-heading);"
                                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                                    onmouseout="this.style.backgroundColor='transparent'"
                                                                    x-text="t.name"></button>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                        {{ $lead->capturedBy?->full_name ?? '-' }}
                                        @if ($lead->institution || $lead->institution_name)
                                            · {{ $lead->institution?->institution_name ?? $lead->institution_name }}
                                        @endif
                                    </p>
                                </div>

                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full shrink-0 {{ $leadStatusColors[$lead->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $leadStatusLabels[$lead->status] ?? $lead->status }}
                                </span>

                                @if ($lead->status === 'FIXED')
                                    <span class="text-[10px] text-emerald-600 shrink-0 font-bold">Terdaftar ✓</span>
                                @else
                                    @can('marketing.prospective_member.update')
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            @foreach ($toggleTargets as $targetStatus => [$icon, $cls])
                                                @if ($targetStatus !== $lead->status)
                                                    <form method="POST" action="{{ route('prospective-members.update-status', $lead) }}" class="inline">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="{{ $targetStatus }}">
                                                        <button type="submit" title="{{ $leadStatusLabels[$targetStatus] }}"
                                                            class="w-6 h-6 rounded-full text-[10px] font-bold border transition-colors focus:outline-none {{ $cls }}">
                                                            {{ $icon }}
                                                        </button>
                                                    </form>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endcan
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if ($leads->hasPages())
                        <div class="px-6 py-4 rounded-b-2xl border-t" style="border-color: var(--dash-card-border);">
                            {{ $leads->withQueryString()->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Jadwal Card --}}
            @if ($socialization->schedules->isNotEmpty())
                <div class="rounded-2xl"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="px-6 py-4 rounded-t-2xl"
                         style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Jadwal Kegiatan</h2>
                    </div>
                    <div class="p-6">
                        <ul class="space-y-3">
                            @foreach ($socialization->schedules as $schedule)
                                <li class="text-sm flex items-center justify-between" style="color: var(--dash-text-body);">
                                    <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $schedule->schedule_date->format('d M Y') }}</span>
                                    <span style="color: var(--dash-text-muted);">
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Fee Partner Card --}}
            @if ($socialization->partner_fee_amount)
                <div class="rounded-2xl"
                     style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                    <div class="px-6 py-4 rounded-t-2xl flex items-center justify-between"
                         style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <h2 class="font-semibold text-sm" style="color: var(--dash-text-heading);">Fee Partner</h2>
                        @can('marketing.socialization.partner_fee.update')
                            <button type="button" @click="showFeeModal = true"
                                class="text-xs font-semibold hover:underline"
                                style="color: var(--dash-primary);">
                                Update
                            </button>
                        @endcan
                    </div>
                    <div class="p-6">
                        <p class="text-2xl font-bold" style="color: var(--dash-text-heading);">
                            Rp {{ number_format($socialization->partner_fee_amount, 0, ',', '.') }}
                        </p>
                        <div class="mt-2.5 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                {{ $feeStatusBadge[$socialization->partner_fee_status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                {{ $feeStatusLabels[$socialization->partner_fee_status] ?? '-' }}
                            </span>
                            @if ($socialization->partner_fee_due_date)
                                <span class="text-xs" style="color: var(--dash-text-muted);">tenggat {{ $socialization->partner_fee_due_date->format('d M Y') }}</span>
                            @endif
                        </div>
                        @if ($socialization->partner_fee_paid_at)
                            <p class="text-xs mt-2" style="color: var(--dash-text-faint);">Dibayar {{ $socialization->partner_fee_paid_at->format('d M Y') }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL: Jadwalkan Sosialisasi (Custom Alpine Dialog) --}}
    <template x-teleport="body">
        <div x-show="showScheduleModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showScheduleModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showScheduleModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showScheduleModal" @click.away="showScheduleModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"
                                     style="background-color: color-mix(in srgb, var(--dash-primary) 15%, transparent); color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[22px]">event_available</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Jadwalkan Sosialisasi?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Status kegiatan akan berubah menjadi Terjadwal dan pegawai terkait akan dapat melihat tugasnya.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showScheduleModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <form action="{{ route('socializations.schedule', $socialization) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">event_available</span>
                                    Ya, Jadwalkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL: Hapus Sosialisasi (Custom Alpine Dialog) --}}
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
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-red-600 text-[22px]">warning</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Hapus Sosialisasi?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Data sosialisasi yang dihapus tidak dapat dikembalikan. Apakah Anda yakin ingin menghapus data ini?
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
                            <form action="{{ route('socializations.destroy', $socialization) }}" method="POST" class="m-0">
                                @csrf @method('DELETE')
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

    {{-- MODAL: Batalkan Sosialisasi --}}
    <template x-teleport="body">
        <div x-show="showCancelModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showCancelModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showCancelModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showCancelModal" @click.away="showCancelModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-amber-600 text-[22px]">cancel</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Batalkan Sosialisasi?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Status kegiatan tidak dapat dikembalikan lagi setelah dibatalkan.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showCancelModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <form action="{{ route('socializations.cancel', $socialization) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                    Ya, Batalkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL: Batalkan Paksa (hanya status Menunggu Validasi, guarded permission) --}}
    @can('marketing.socialization.force_cancel')
    <template x-teleport="body">
        <div x-show="showForceCancelModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showForceCancelModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showForceCancelModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showForceCancelModal" @click.away="showForceCancelModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                        <div class="rounded-t-2xl px-6 py-5"
                             style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-red-600 text-[22px]">cancel</span>
                                </div>
                                <div>
                                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Batalkan Paksa Sosialisasi?</h3>
                                    <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                        Sosialisasi ini sudah Menunggu Validasi. Pembatalan paksa hanya untuk kendala lapangan yang lupa dibatalkan dan tidak dapat dikembalikan.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                             style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                            <button type="button" @click="showForceCancelModal = false"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                    style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                Batal
                            </button>
                            <form action="{{ route('socializations.force-cancel', $socialization) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-danger, #dc2626);">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                    Ya, Batalkan Paksa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
    @endcan

    {{-- MODAL: Selesaikan Sosialisasi --}}
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
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                        <form action="{{ route('socializations.complete', $socialization) }}" method="POST">
                            @csrf
                            <div class="rounded-t-2xl px-6 py-5"
                                 style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <div class="flex items-start gap-3.5">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-emerald-600 text-[22px]">check_circle</span>
                                    </div>
                                    <div>
                                        <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Selesaikan Sosialisasi</h3>
                                        <p class="mt-1 text-sm leading-relaxed" style="color: var(--dash-text-muted);">
                                            Konfirmasi jumlah kelas per marketer. Data tidak dapat diubah setelah diselesaikan.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                @if ($socialization->employeeSocializations->isEmpty())
                                    <p class="text-sm font-medium" style="color: #d97706;">Belum ada marketer ditugaskan - sosialisasi tetap dapat diselesaikan.</p>
                                @else
                                    <div class="divide-y" style="border-color: var(--dash-card-border);">
                                        @foreach ($socialization->employeeSocializations as $es)
                                        <div class="flex items-center justify-between py-3 gap-4">
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $es->employee->full_name ?? '-' }}</p>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <label class="text-xs font-semibold" style="color: var(--dash-text-muted);">Kelas</label>
                                                <input type="hidden" name="results[{{ $loop->index }}][employee_socialization_id]" value="{{ $es->id }}">
                                                <input type="number" name="results[{{ $loop->index }}][classes_obtained]"
                                                    value="{{ $es->classes_obtained ?? 0 }}" min="0"
                                                    class="w-20 text-center px-2 py-1.5 rounded-lg text-sm focus:outline-none transition-all"
                                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
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
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: #059669;">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                    Selesaikan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL: Tugaskan Marketer --}}
    <template x-teleport="body">
        <div x-show="showAssignModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showAssignModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showAssignModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showAssignModal" @click.away="showAssignModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                         x-data="{
                            selected: [],
                            search: '',
                            employees: @js($eligibleEmployees->map(fn($e) => ['id' => $e->id, 'name' => $e->full_name])->values()->all()),
                            get filtered() {
                                if (!this.search) return this.employees;
                                const s = this.search.toLowerCase();
                                return this.employees.filter(e => e.name.toLowerCase().includes(s));
                            },
                            get allSelected() { return this.filtered.length > 0 && this.filtered.every(e => this.selected.includes(e.id)); },
                            toggleAll() {
                                const ids = this.filtered.map(e => e.id);
                                const allChecked = ids.every(id => this.selected.includes(id));
                                if (allChecked) this.selected = this.selected.filter(id => !ids.includes(id));
                                else this.selected = [...new Set([...this.selected, ...ids])];
                            }
                         }">
                        <form action="{{ route('socializations.assign-employee', $socialization) }}" method="POST">
                            @csrf
                            <div class="rounded-t-2xl px-6 py-5 flex items-center justify-between"
                                 style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Tugaskan Marketer</h3>
                                <button type="button" @click="toggleAll()"
                                    class="text-xs font-semibold focus:outline-none hover:underline"
                                    style="color: var(--dash-primary);"
                                    x-text="allSelected ? 'Batal Semua' : 'Pilih Semua'"></button>
                            </div>
                            <div class="p-6">
                                @if ($eligibleEmployees->isEmpty())
                                    <p class="text-sm font-medium" style="color: var(--dash-text-muted);">Belum ada marketer di area ini.</p>
                                @else
                                    <div class="relative mb-3">
                                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                                              style="color: var(--dash-text-muted);">search</span>
                                        <input type="text" x-model="search" placeholder="Cari nama marketer..."
                                            class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                            style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                            onfocus="this.style.borderColor='var(--dash-primary)'"
                                            onblur="this.style.borderColor='var(--dash-input-border)'">
                                    </div>
                                    <div class="rounded-xl max-h-52 overflow-y-auto divide-y mb-3"
                                         style="border: 1px solid var(--dash-card-border); border-color: var(--dash-card-border);">
                                        <template x-for="emp in filtered" :key="emp.id">
                                            <label class="flex items-center gap-3 px-4 py-2.5 cursor-pointer transition-colors"
                                                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                   onmouseout="this.style.backgroundColor='transparent'">
                                                <input type="checkbox" name="employee_ids[]" :value="emp.id" x-model="selected"
                                                    class="w-4 h-4 rounded"
                                                    style="accent-color: var(--dash-primary);">
                                                <span class="text-sm" style="color: var(--dash-text-heading);" x-text="emp.name"></span>
                                            </label>
                                        </template>
                                        <p x-show="filtered.length === 0" class="px-4 py-3 text-sm text-center" style="color: var(--dash-text-faint);">
                                            Tidak ditemukan.
                                        </p>
                                    </div>
                                    <p class="text-xs font-medium" style="color: var(--dash-text-muted);" x-show="selected.length > 0">
                                        <span x-text="selected.length" class="font-bold"></span> marketer dipilih
                                    </p>
                                    <template x-for="id in selected" :key="id">
                                        <input type="hidden" name="employee_ids[]" :value="id">
                                    </template>
                                @endif
                            </div>
                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showAssignModal = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <button type="submit" :disabled="selected.length === 0"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity disabled:opacity-40 disabled:cursor-not-allowed"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">person_add</span>
                                    Tugaskan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- MODAL: Update Status Fee Partner --}}
    <template x-teleport="body">
        <div x-show="showFeeModal" style="display: none;" class="relative z-50" role="dialog" aria-modal="true">
            <div x-show="showFeeModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                 @click="showFeeModal = false"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="showFeeModal" @click.away="showFeeModal = false"
                         x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform rounded-2xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md"
                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                        <form action="{{ route('socializations.partner-fee', $socialization) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="rounded-t-2xl px-6 py-5"
                                 style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Update Status Fee Partner</h3>
                            </div>
                            <div class="p-6 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Status Pembayaran</label>
                                    <select name="partner_fee_status" required
                                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'">
                                        <option value="none" {{ $socialization->partner_fee_status === 'none' ? 'selected' : '' }}>Tidak Ada</option>
                                        <option value="pending" {{ $socialization->partner_fee_status === 'pending' ? 'selected' : '' }}>Belum Bayar</option>
                                        <option value="paid" {{ $socialization->partner_fee_status === 'paid' ? 'selected' : '' }}>Sudah Bayar</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Tanggal Bayar</label>
                                    <input type="date" name="partner_fee_paid_at"
                                        value="{{ $socialization->partner_fee_paid_at?->format('Y-m-d') }}"
                                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                        onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                        onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                                </div>
                            </div>
                            <div class="rounded-b-2xl px-6 py-4 flex justify-end gap-3"
                                 style="background-color: var(--dash-card-bg); border-top: 1px solid var(--dash-card-border);">
                                <button type="button" @click="showFeeModal = false"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                        style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[16px]">save</span>
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection
