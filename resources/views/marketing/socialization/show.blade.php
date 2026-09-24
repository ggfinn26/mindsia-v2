@extends('layouts.dashboard')

@section('title', 'Detail Sosialisasi')

@section('content')
@php
    $statusBorderLeft = [
        'draft'     => 'border-l-amber-400',
        'scheduled' => 'border-l-blue-500',
        'completed' => 'border-l-green-500',
        'cancelled' => 'border-l-red-400',
    ];
    $statusBadge = [
        'draft'     => 'bg-amber-50 text-amber-700 border-amber-200',
        'scheduled' => 'bg-blue-50 text-blue-700 border-blue-200',
        'completed' => 'bg-green-50 text-green-700 border-green-200',
        'cancelled' => 'bg-red-50 text-red-700 border-red-200',
    ];
    $statusLabels = [
        'draft'     => 'Draft',
        'scheduled' => 'Terjadwal',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
    $feeStatusBadge = [
        'none'    => 'bg-gray-100 text-gray-600 border-gray-200',
        'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
        'paid'    => 'bg-green-50 text-green-700 border-green-200',
    ];
    $feeStatusLabels = ['none' => 'Tidak Ada', 'pending' => 'Belum Bayar', 'paid' => 'Sudah Bayar'];
    $leadStatusColors = [
        'ALMOST' => 'bg-yellow-100 text-yellow-700',
        'YES'    => 'bg-blue-100 text-blue-700',
        'FIXED'  => 'bg-green-100 text-green-700',
        'NO'     => 'bg-red-100 text-red-700',
    ];
    $leadStatusLabels = ['ALMOST' => 'Hampir', 'YES' => 'Berminat', 'FIXED' => 'Pasti', 'NO' => 'Tidak'];

    // Quick toggle targets per status (status => [label, color classes])
    $toggleTargets = [
        'ALMOST' => ['?', 'border-yellow-300 text-yellow-600 hover:bg-yellow-50'],
        'YES'    => ['✓', 'border-green-300 text-green-600 hover:bg-green-50'],
        'NO'     => ['✕', 'border-red-300 text-red-500 hover:bg-red-50'],
    ];

    $totalLeads = $leads->total();
    $fixedLeads = $socialization->prospectiveMembers->where('status', 'FIXED')->count();
    $totalKelas = $viewMode === 'mine' && $myEs
        ? $myEs->classes_obtained
        : $socialization->employeeSocializations->sum('classes_obtained');

    $pmSort = request('pm_sort', 'created_at');
    $pmDir  = request('pm_dir', 'desc');
    $pmSortUrl = fn(string $col) => request()->fullUrlWithQuery([
        'pm_sort' => $col,
        'pm_dir'  => ($pmSort === $col && $pmDir === 'asc') ? 'desc' : 'asc',
        'page'    => 1,
    ]);
    $pmSortIcon = fn(string $col) => $pmSort === $col ? ($pmDir === 'asc' ? ' ↑' : ' ↓') : '';
@endphp

<div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8"
    x-data="{ showAssignModal: false, showFeeModal: false, showCancelModal: false }">

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Header — focal point, left border encodes status (information, not decoration) --}}
    <div class="bg-white border border-gray-100 border-l-4 {{ $statusBorderLeft[$socialization->status] ?? 'border-l-gray-300' }} rounded-xl shadow-sm mb-5 p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="flex items-start gap-3 min-w-0">
                <a href="{{ route('socializations.index') }}"
                    class="mt-0.5 text-gray-400 hover:text-gray-600 shrink-0 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
                    aria-label="Kembali ke daftar sosialisasi">
                    <span class="material-symbols-outlined text-xl">arrow_back</span>
                </a>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border
                            {{ $statusBadge[$socialization->status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                            {{ $statusLabels[$socialization->status] ?? $socialization->status }}
                        </span>
                        @if ($socialization->area)
                            <span class="text-xs text-gray-400">{{ $socialization->area->name }}</span>
                        @endif
                    </div>
                    <h1 class="text-xl font-bold text-gray-900 leading-snug">{{ $socialization->location_name }}</h1>
                    @if ($socialization->institution)
                        <p class="text-sm text-gray-500 mt-0.5">{{ $socialization->institution->institution_name }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">Dibuat oleh {{ $socialization->createdBy->full_name ?? '-' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                @can('marketing.socialization.update')
                    @if ($socialization->status === 'draft')
                        <a href="{{ route('socializations.edit', $socialization) }}"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Edit
                        </a>
                    @endif
                    @if (in_array($socialization->status, ['draft', 'scheduled']))
                        <button @click="showCancelModal = true"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Batalkan
                        </button>
                    @endif
                @endcan
                @can('marketing.socialization.delete')
                    @if ($socialization->status === 'draft')
                        <form action="{{ route('socializations.destroy', $socialization) }}" method="POST" class="inline"
                            onsubmit="return confirm('Yakin hapus sosialisasi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 bg-white border border-red-200 rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                                Hapus
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-3 gap-3 mb-5">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $totalLeads }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Total Lead</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $fixedLeads }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Sudah FIXED</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $totalKelas }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Total Kelas</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Calon Member — main column --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Filter bar --}}
            <form method="GET" action="{{ route('socializations.show', $socialization) }}"
                class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[140px]">
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Cari</label>
                    <input type="text" name="pm_search" value="{{ request('pm_search') }}"
                        placeholder="Nama atau WA..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 transition-colors">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Status</label>
                    <select name="pm_status" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-indigo-400 transition-colors">
                        <option value="">Semua</option>
                        @foreach (['ALMOST' => 'Hampir', 'YES' => 'Berminat', 'FIXED' => 'Pasti', 'NO' => 'Tidak'] as $val => $label)
                            <option value="{{ $val }}" {{ request('pm_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-3 py-2 text-sm font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-800 focus:outline-none">
                        Cari
                    </button>
                    @if (request()->hasAny(['pm_search', 'pm_status', 'pm_sort', 'pm_dir']))
                        <a href="{{ route('socializations.show', $socialization) }}"
                            class="px-3 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Lead list --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">

                {{-- Table header --}}
                <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">
                        Calon Member ({{ $totalLeads }})
                    </p>
                    <div class="flex items-center gap-3 text-xs text-gray-400">
                        <a href="{{ $pmSortUrl('full_name') }}" class="hover:text-gray-600 focus:outline-none">
                            Nama{{ $pmSortIcon('full_name') }}
                        </a>
                        <a href="{{ $pmSortUrl('status') }}" class="hover:text-gray-600 focus:outline-none">
                            Status{{ $pmSortIcon('status') }}
                        </a>
                        <a href="{{ $pmSortUrl('created_at') }}" class="hover:text-gray-600 focus:outline-none">
                            Terbaru{{ $pmSortIcon('created_at') }}
                        </a>
                    </div>
                </div>

                @if ($leads->isEmpty())
                    <div class="py-8 text-center">
                        <span class="material-symbols-outlined text-3xl text-gray-300 block mb-2">group_add</span>
                        <p class="text-sm text-gray-500">
                            {{ request()->hasAny(['pm_search', 'pm_status']) ? 'Tidak ada prospective member yang cocok.' : ($viewMode === 'mine' ? 'Kamu belum menambahkan prospective member.' : 'Belum ada prospective member.') }}
                        </p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach ($leads as $lead)
                            @php
                                $waNum = preg_replace('/[^0-9]/', '', $lead->whatsapp_number ?? '');
                                if (str_starts_with($waNum, '0')) $waNum = '62' . substr($waNum, 1);
                                elseif ($waNum && ! str_starts_with($waNum, '62')) $waNum = '62' . $waNum;
                            @endphp
                            <div class="flex items-center gap-2 px-5 py-3 hover:bg-gray-50/50 transition-colors">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('prospective-members.show', $lead) }}"
                                            class="text-sm font-semibold text-gray-800 hover:text-indigo-600 focus:outline-none focus:underline">
                                            {{ $lead->full_name }}
                                        </a>
                                        @if ($waNum)
                                            <div class="relative" x-data="waPicker({
                                                wa: '{{ $waNum }}',
                                                nama: @js($lead->full_name),
                                                institusi: @js($lead->institution?->institution_name ?? $lead->institution_name ?? ''),
                                                sosialisasi: @js($socialization->location_name),
                                                status: '{{ $lead->status }}'
                                            })" @click.away="open = false">
                                                <button @click="open = !open"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded-full hover:bg-green-50 focus:outline-none transition-colors"
                                                    title="{{ $lead->whatsapp_number }}">
                                                    <img src="{{ asset('img/whatsapp.svg') }}" class="w-4 h-4" alt="WhatsApp">
                                                </button>
                                                <div x-show="open" x-transition
                                                    class="absolute left-0 top-6 z-20 w-52 bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                                                    <button @click="send(null)"
                                                        class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-gray-50">
                                                        Kirim langsung
                                                    </button>
                                                    <template x-if="templates.length > 0">
                                                        <div>
                                                            <p class="px-3 py-1 text-[10px] font-semibold text-gray-400 uppercase tracking-widest border-t border-gray-100 mt-1">Template</p>
                                                            <template x-for="t in templates" :key="t.id">
                                                                <button @click="send(t)"
                                                                    class="w-full text-left px-3 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700"
                                                                    x-text="t.name">
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($lead->institution || $lead->institution_name)
                                            <span class="text-xs text-gray-400 hidden sm:inline">
                                                {{ $lead->institution?->institution_name ?? $lead->institution_name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full shrink-0 {{ $leadStatusColors[$lead->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $leadStatusLabels[$lead->status] ?? $lead->status }}
                                </span>

                                @if ($lead->status === 'FIXED')
                                    <span class="text-[10px] text-green-600 shrink-0 font-medium">Terdaftar ✓</span>
                                @else
                                    @can('marketing.prospective_member.update')
                                        <div class="flex items-center gap-1 shrink-0">
                                            @foreach ($toggleTargets as $targetStatus => [$icon, $cls])
                                                @if ($targetStatus !== $lead->status)
                                                    <form method="POST"
                                                        action="{{ route('prospective-members.update-status', $lead) }}"
                                                        class="inline">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="{{ $targetStatus }}">
                                                        <button type="submit"
                                                            title="{{ $leadStatusLabels[$targetStatus] }}"
                                                            class="w-6 h-6 rounded-full text-[10px] font-bold border transition-colors focus:outline-none focus:ring-1 focus:ring-offset-1 {{ $cls }}">
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
                        <div class="px-5 py-3 border-t border-gray-50">
                            {{ $leads->withQueryString()->links() }}
                        </div>
                    @endif
                @endif

                @can('marketing.prospective_member.create')
                    <div class="px-5 py-3 border-t border-gray-50">
                        <a href="{{ route('prospective-members.create', ['socialization_id' => $socialization->id]) }}"
                            class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus:underline">
                            <span class="material-symbols-outlined text-[16px]">person_add</span>
                            Tambah Prospective Member
                        </a>
                    </div>
                @endcan
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Marketer --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Marketer</p>
                    @can('marketing.socialization.update')
                        @if (!in_array($socialization->status, ['completed', 'cancelled']) && $eligibleEmployees->isNotEmpty())
                            <button @click="showAssignModal = true"
                                class="text-xs font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus:underline">
                                + Tugaskan
                            </button>
                        @endif
                    @endcan
                </div>
                @if ($socialization->employeeSocializations->isEmpty())
                    <div class="py-4 text-center">
                        <span class="material-symbols-outlined text-2xl text-gray-300 block mb-1">badge</span>
                        <p class="text-xs text-gray-400">Belum ada marketer.</p>
                    </div>
                @else
                    <ul class="divide-y divide-gray-50">
                        @foreach ($socialization->employeeSocializations as $es)
                            <li class="flex items-center justify-between py-2.5 first:pt-0 last:pb-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $es->employee->full_name ?? '-' }}</p>
                                    @if ($es->classes_obtained)
                                        <p class="text-xs text-gray-400">{{ $es->classes_obtained }} kelas</p>
                                    @endif
                                </div>
                                <span class="text-xs px-2 py-0.5 rounded-full
                                    {{ $es->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $es->status === 'confirmed' ? 'Dikonfirmasi' : ucfirst($es->status) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Fee Partner — hanya di full view --}}
            @if ($viewMode !== 'mine' && $socialization->partner_fee_amount)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Fee Partner</p>
                        @can('marketing.socialization.partner_fee.update')
                            <button @click="showFeeModal = true"
                                class="text-xs font-medium text-gray-500 hover:text-gray-700 focus:outline-none focus:underline">
                                Update
                            </button>
                        @endcan
                    </div>
                    <p class="text-xl font-bold text-gray-900">Rp {{ number_format($socialization->partner_fee_amount, 0, ',', '.') }}</p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border
                            {{ $feeStatusBadge[$socialization->partner_fee_status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                            {{ $feeStatusLabels[$socialization->partner_fee_status] ?? '-' }}
                        </span>
                        @if ($socialization->partner_fee_due_date)
                            <span class="text-xs text-gray-400">tenggat {{ $socialization->partner_fee_due_date->format('d M Y') }}</span>
                        @endif
                    </div>
                    @if ($socialization->partner_fee_paid_at)
                        <p class="text-xs text-gray-400 mt-1.5">Dibayar {{ $socialization->partner_fee_paid_at->format('d M Y') }}</p>
                    @endif
                </div>
            @endif

            {{-- Jadwal — hanya di full view --}}
            @if ($viewMode !== 'mine' && $socialization->schedules->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Jadwal</p>
                    <ul class="space-y-2">
                        @foreach ($socialization->schedules as $schedule)
                            <li class="text-sm text-gray-700">
                                <span class="font-medium">{{ $schedule->schedule_date->format('d M Y') }}</span>
                                <span class="text-gray-400 ml-1">
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                    &ndash;
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal: Tugaskan Marketer --}}
    <div x-show="showAssignModal" x-transition.opacity
        @keydown.escape.window="showAssignModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;">
        <div @click="showAssignModal = false" class="absolute inset-0 bg-gray-900/50" aria-hidden="true"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full z-10"
            x-data="{
                selected: [],
                search: '',
                employees: @js($eligibleEmployees->map(fn($e) => ['id' => $e->id, 'name' => $e->full_name])->values()->all()),
                get filtered() {
                    if (!this.search) return this.employees;
                    const s = this.search.toLowerCase();
                    return this.employees.filter(e => e.name.toLowerCase().includes(s));
                },
                get allSelected() {
                    return this.filtered.length > 0 && this.filtered.every(e => this.selected.includes(e.id));
                },
                toggleAll() {
                    const ids = this.filtered.map(e => e.id);
                    const allChecked = ids.every(id => this.selected.includes(id));
                    if (allChecked) {
                        this.selected = this.selected.filter(id => !ids.includes(id));
                    } else {
                        this.selected = [...new Set([...this.selected, ...ids])];
                    }
                }
            }">
            <form action="{{ route('socializations.assign-employee', $socialization) }}" method="POST" class="p-6">
                @csrf
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-gray-900">Tugaskan Marketer</h3>
                    <button type="button" @click="toggleAll()"
                        class="text-xs font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none"
                        x-text="allSelected ? 'Batal Semua' : 'Pilih Semua'">
                    </button>
                </div>

                @if ($eligibleEmployees->isEmpty())
                    <p class="text-sm text-gray-500 mb-4">Belum ada employee di area ini.</p>
                @else
                    <div class="relative mb-2">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[16px]">search</span>
                        <input type="text" x-model="search" placeholder="Cari nama..."
                            style="padding-left: 2rem !important"
                            class="w-full pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 transition-colors">
                    </div>

                    <div class="border border-gray-200 rounded-xl max-h-52 overflow-y-auto divide-y divide-gray-50 mb-3">
                        <template x-for="emp in filtered" :key="emp.id">
                            <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="employee_ids[]"
                                    :value="emp.id"
                                    x-model="selected"
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="text-sm text-gray-700" x-text="emp.name"></span>
                            </label>
                        </template>
                        <p x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-400 text-center">
                            Tidak ditemukan.
                        </p>
                    </div>

                    <p class="text-xs text-gray-400 mb-4" x-show="selected.length > 0">
                        <span x-text="selected.length"></span> marketer dipilih
                    </p>

                    {{-- Hidden inputs untuk yang sudah dipilih tapi tidak di filtered (tetap tersubmit) --}}
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="employee_ids[]" :value="id">
                    </template>
                @endif

                <div class="flex justify-end gap-3">
                    <button type="button" @click="showAssignModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none">
                        Batal
                    </button>
                    <button type="submit" :disabled="selected.length === 0"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none disabled:opacity-40 disabled:cursor-not-allowed">
                        Tugaskan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Update Fee Status --}}
    <div x-show="showFeeModal" x-transition.opacity
        @keydown.escape.window="showFeeModal = false"
        x-init="$watch('showFeeModal', v => v && $nextTick(() => document.querySelectorAll('#feeModal select:not([data-enhanced])').forEach(window.enhanceSelect)))"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;">
        <div @click="showFeeModal = false" class="absolute inset-0 bg-gray-900/50" aria-hidden="true"></div>
        <div id="feeModal" class="relative bg-white rounded-2xl shadow-xl max-w-md w-full z-10">
            <form action="{{ route('socializations.partner-fee', $socialization) }}" method="POST" class="p-6">
                @csrf @method('PATCH')
                <h3 class="text-base font-semibold text-gray-900 mb-4">Update Status Fee Partner</h3>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status Pembayaran</label>
                    <select name="partner_fee_status" required
                        class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="none" {{ $socialization->partner_fee_status === 'none' ? 'selected' : '' }}>Tidak Ada</option>
                        <option value="pending" {{ $socialization->partner_fee_status === 'pending' ? 'selected' : '' }}>Belum Bayar</option>
                        <option value="paid" {{ $socialization->partner_fee_status === 'paid' ? 'selected' : '' }}>Sudah Bayar</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Bayar</label>
                    <input type="date" name="partner_fee_paid_at"
                        value="{{ $socialization->partner_fee_paid_at?->format('Y-m-d') }}"
                        class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showFeeModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Konfirmasi Batalkan --}}
    <div x-show="showCancelModal" x-transition.opacity
        @keydown.escape.window="showCancelModal = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;">
        <div @click="showCancelModal = false" class="absolute inset-0 bg-gray-900/50" aria-hidden="true"></div>
        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full z-10 p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-2">Batalkan sosialisasi ini?</h3>
            <p class="text-sm text-gray-500 mb-5">Status tidak bisa dikembalikan setelah dibatalkan.</p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showCancelModal = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Kembali
                </button>
                <form action="{{ route('socializations.cancel', $socialization) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Ya, batalkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
