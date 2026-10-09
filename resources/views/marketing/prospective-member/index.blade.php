@extends('layouts.dashboard')

@section('title', 'Prospective Member')

@section('content')
@php
    $statusColors = [
        'ALMOST' => 'bg-yellow-100 text-yellow-700 [.mode-dark_&]:bg-yellow-500/10 [.mode-dark_&]:text-yellow-400',
        'YES'    => 'bg-blue-100 text-blue-700 [.mode-dark_&]:bg-blue-500/10 [.mode-dark_&]:text-blue-400',
        'FIXED'  => 'bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 text-emerald-700 [.mode-dark_&]:text-emerald-400 [.mode-dark_&]:bg-green-500/10 [.mode-dark_&]:text-green-400',
        'NO'     => 'bg-red-100 text-red-700 [.mode-dark_&]:text-red-400 [.mode-dark_&]:bg-red-500/10 [.mode-dark_&]:text-red-400',
    ];
    $statusLabels = ['ALMOST' => 'Hampir', 'YES' => 'Berminat', 'FIXED' => 'Pasti', 'NO' => 'Tidak'];
    $toggleTargets = [
        'ALMOST' => ['?', 'border-yellow-300 text-yellow-600 hover:bg-yellow-50 [.mode-dark_&]:border-yellow-500/30 [.mode-dark_&]:text-yellow-400 [.mode-dark_&]:hover:bg-yellow-500/10'],
        'YES'    => ['✓', 'border-green-300 text-emerald-700 [.mode-dark_&]:text-emerald-400 hover:bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 [.mode-dark_&]:border-green-500/30 [.mode-dark_&]:text-green-400 [.mode-dark_&]:hover:bg-green-500/10'],
        'NO'     => ['✕', 'border-red-300 [.mode-dark_&]:border-red-500/20 text-red-500 hover:bg-red-50 [.mode-dark_&]:bg-red-500/10 [.mode-dark_&]:border-red-500/30 [.mode-dark_&]:text-red-400 [.mode-dark_&]:hover:bg-red-500/10'],
    ];

    // Sort helper: build URL with sort/dir toggle
    $currentSort = request('sort', 'created_at');
    $currentDir  = request('dir', 'desc');
    $sortUrl = fn(string $col) => request()->fullUrlWithQuery([
        'sort' => $col,
        'dir'  => ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc',
        'page' => 1,
    ]);
    $sortIcon = fn(string $col) => $currentSort === $col
        ? ($currentDir === 'asc' ? '↑' : '↓')
        : '';
@endphp

<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-5">
        <div>
            <h1 class="text-xl font-bold text-[var(--dash-text-heading)]">
                {{ $canViewNational ? 'Calon Member Nasional' : 'Calon Member Area' }}
            </h1>
            <p class="text-sm text-[var(--dash-text-faint)] mt-0.5">{{ $leads->total() }} total</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 border border-emerald-200 [.mode-dark_&]:border-emerald-500/20 text-emerald-800 [.mode-dark_&]:text-emerald-400 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('prospective-members.index') }}"
        class="bg-[var(--dash-card-bg)] rounded-xl border border-[var(--dash-card-border)] shadow-sm p-4 mb-5">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-[11px] font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nama atau nomor WA..."
                    class="w-full bg-[var(--dash-page-bg)] border border-[var(--dash-card-border)] rounded-lg text-sm px-3 py-2 outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 transition-colors">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide mb-1">Status</label>
                <select name="status" onchange="this.form.submit()"
                    class="bg-[var(--dash-page-bg)] border border-[var(--dash-card-border)] rounded-lg text-sm px-3 py-2 outline-none focus:border-indigo-400 transition-colors">
                    <option value="">Semua Status</option>
                    @foreach (['ALMOST' => 'Hampir', 'YES' => 'Berminat', 'FIXED' => 'Pasti', 'NO' => 'Tidak'] as $val => $label)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @if ($branches->isNotEmpty())
                <div>
                    <label class="block text-[11px] font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide mb-1">Cabang</label>
                    <select name="branch_id" onchange="this.form.submit()"
                        class="bg-[var(--dash-page-bg)] border border-[var(--dash-card-border)] rounded-lg text-sm px-3 py-2 outline-none focus:border-indigo-400 transition-colors">
                        <option value="">Semua Cabang</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            @if ($employees->isNotEmpty())
                <div>
                    <label class="block text-[11px] font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide mb-1">Marketer</label>
                    <select name="employee_id" onchange="this.form.submit()"
                        class="bg-[var(--dash-page-bg)] border border-[var(--dash-card-border)] rounded-lg text-sm px-3 py-2 outline-none focus:border-indigo-400 transition-colors">
                        <option value="">Semua Marketer</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="flex gap-2">
                <button type="submit"
                    class="px-3 py-2 text-sm font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-800">Cari</button>
                @if (request()->hasAny(['search', 'status', 'branch_id', 'employee_id']))
                    <a href="{{ route('prospective-members.index') }}"
                        class="px-3 py-2 text-sm text-[var(--dash-text-muted)] bg-[var(--dash-input-bg)] rounded-lg hover:bg-gray-200">Reset</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[var(--dash-card-border)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--dash-card-border)]">
                        <th class="px-4 py-3 text-left">
                            <a href="{{ $sortUrl('full_name') }}"
                                class="text-xs font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide hover:text-[var(--dash-text-muted)] focus:outline-none">
                                Nama {{ $sortIcon('full_name') }}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide">WhatsApp</th>
                        <th class="px-4 py-3 text-left">
                            <a href="{{ $sortUrl('status') }}"
                                class="text-xs font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide hover:text-[var(--dash-text-muted)] focus:outline-none">
                                Status {{ $sortIcon('status') }}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide hidden md:table-cell">Sosialisasi</th>
                        @if (true)
                            <th class="px-4 py-3 text-left text-xs font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide hidden lg:table-cell">Marketer</th>
                        @endif
                        <th class="px-4 py-3 text-left">
                            <a href="{{ $sortUrl('created_at') }}"
                                class="text-xs font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide hover:text-[var(--dash-text-muted)] focus:outline-none hidden sm:inline">
                                Tanggal {{ $sortIcon('created_at') }}
                            </a>
                        </th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($leads as $lead)
                        @php
                            $waNum = preg_replace('/[^0-9]/', '', $lead->whatsapp_number ?? '');
                            if (str_starts_with($waNum, '0')) $waNum = '62' . substr($waNum, 1);
                            elseif ($waNum && ! str_starts_with($waNum, '62')) $waNum = '62' . $waNum;
                        @endphp
                        <tr class="hover:bg-[var(--dash-page-bg)]/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('prospective-members.show', $lead) }}"
                                    class="font-medium text-[var(--dash-text-heading)] hover:text-[var(--dash-primary)] focus:outline-none focus:underline">
                                    {{ $lead->full_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                @if ($waNum)
                                    <div class="relative" x-data="waPicker({
                                        wa: '{{ $waNum }}',
                                        nama: @js($lead->full_name),
                                        institusi: @js($lead->institution?->institution_name ?? $lead->institution_name ?? ''),
                                        sosialisasi: @js($lead->socialization?->location_name ?? ''),
                                        status: '{{ $lead->status }}'
                                    })" @click.away="open = false">
                                        <button @click="open = !open"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 focus:outline-none transition-colors"
                                            title="{{ $lead->whatsapp_number }}">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 362" fill="none" aria-label="WhatsApp"><path fill="#25D366" fill-rule="evenodd" d="M307.546 52.566C273.709 18.684 228.706.017 180.756 0 81.951 0 1.538 80.404 1.504 179.235c-.017 31.594 8.242 62.432 23.928 89.609L0 361.736l95.024-24.925c26.179 14.285 55.659 21.805 85.655 21.814h.077c98.788 0 179.21-80.413 179.244-179.244.017-47.898-18.608-92.926-52.454-126.807v-.008Zm-126.79 275.788h-.06c-26.73-.008-52.952-7.194-75.831-20.765l-5.44-3.231-56.391 14.791 15.05-54.981-3.542-5.638c-14.912-23.721-22.793-51.139-22.776-79.286.035-82.14 66.867-148.973 149.051-148.973 39.793.017 77.198 15.53 105.328 43.695 28.131 28.157 43.61 65.596 43.593 105.398-.035 82.149-66.867 148.982-148.982 148.982v.008Zm81.719-111.577c-4.478-2.243-26.497-13.073-30.606-14.568-4.108-1.496-7.09-2.243-10.073 2.243-2.982 4.487-11.568 14.577-14.181 17.559-2.613 2.991-5.226 3.361-9.704 1.117-4.477-2.243-18.908-6.97-36.02-22.226-13.313-11.878-22.304-26.54-24.916-31.027-2.613-4.486-.275-6.91 1.959-9.136 2.011-2.011 4.478-5.234 6.721-7.847 2.244-2.613 2.983-4.486 4.478-7.469 1.496-2.991.748-5.603-.369-7.847-1.118-2.243-10.073-24.289-13.812-33.253-3.636-8.732-7.331-7.546-10.073-7.692-2.613-.13-5.595-.155-8.586-.155-2.991 0-7.839 1.118-11.947 5.604-4.108 4.486-15.677 15.324-15.677 37.361s16.047 43.344 18.29 46.335c2.243 2.991 31.585 48.225 76.51 67.632 10.684 4.615 19.029 7.374 25.535 9.437 10.727 3.412 20.49 2.931 28.208 1.779 8.604-1.289 26.498-10.838 30.228-21.298 3.73-10.46 3.73-19.433 2.613-21.298-1.117-1.865-4.108-2.991-8.586-5.234l.008-.017Z" clip-rule="evenodd"/></svg>
                                        </button>
                                        <div x-show="open" x-transition
                                            class="absolute left-0 top-7 z-20 w-56 bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-xl shadow-lg py-1">
                                            <button @click="send(null)"
                                                class="w-full text-left px-4 py-2 text-xs text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)]">
                                                Kirim langsung (tanpa template)
                                            </button>
                                            <template x-if="templates.length > 0">
                                                <div>
                                                    <p class="px-4 py-1 text-[10px] font-semibold text-[var(--dash-text-faint)] uppercase tracking-widest border-t border-[var(--dash-card-border)] mt-1">Template</p>
                                                    <template x-for="t in templates" :key="t.id">
                                                        <button @click="send(t)"
                                                            class="w-full text-left px-4 py-2 text-xs text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] hover:text-[var(--dash-primary)]"
                                                            x-text="t.name">
                                                        </button>
                                                    </template>
                                                </div>
                                            </template>
                                            <p x-show="templates.length === 0"
                                                class="px-4 py-2 text-[10px] text-[var(--dash-text-faint)] border-t border-[var(--dash-card-border)] mt-1">
                                                Belum ada template. Buat di pengaturan WA.
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[var(--dash-text-faint)] text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $statusColors[$lead->status] ?? 'bg-[var(--dash-input-bg)] text-[var(--dash-text-muted)]' }}">
                                        {{ $statusLabels[$lead->status] ?? $lead->status }}
                                    </span>
                                    {{-- Quick toggle --}}
                                    @can('marketing.prospective_member.update')
                                        @if ($lead->status !== 'FIXED')
                                            @foreach ($toggleTargets as $targetStatus => [$icon, $cls])
                                                @if ($targetStatus !== $lead->status)
                                                    <form method="POST"
                                                        action="{{ route('prospective-members.update-status', $lead) }}"
                                                        class="inline">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="{{ $targetStatus }}">
                                                        <button type="submit"
                                                            title="{{ $statusLabels[$targetStatus] }}"
                                                            class="w-5 h-5 rounded-full text-[9px] font-bold border transition-colors focus:outline-none focus:ring-1 {{ $cls }}">
                                                            {{ $icon }}
                                                        </button>
                                                    </form>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-[10px] text-emerald-700 [.mode-dark_&]:text-emerald-400 font-medium">Terdaftar ✓</span>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                @if ($lead->socialization)
                                    <a href="{{ route('socializations.show', $lead->socialization_id) }}"
                                        class="text-xs text-[var(--dash-text-muted)] hover:text-[var(--dash-primary)] focus:outline-none focus:underline line-clamp-1">
                                        {{ $lead->socialization->location_name }}
                                    </a>
                                    @if ($lead->socialization->branch)
                                        <p class="text-[10px] text-[var(--dash-text-faint)]">{{ $lead->socialization->branch->branch_name }}</p>
                                    @endif
                                @else
                                    <span class="text-xs text-[var(--dash-text-faint)]">Walk-in</span>
                                @endif
                            </td>
                            @if (true)
                                <td class="px-4 py-3 hidden lg:table-cell text-xs text-[var(--dash-text-muted)]">
                                    {{ $lead->capturedBy?->full_name ?? '-' }}
                                </td>
                            @endif
                            <td class="px-4 py-3 hidden sm:table-cell text-xs text-[var(--dash-text-faint)] whitespace-nowrap">
                                {{ $lead->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('prospective-members.show', $lead) }}"
                                    class="text-xs text-[var(--dash-primary)] hover:text-[var(--dash-primary)] hover:opacity-80 focus:outline-none focus:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center">
                                <span class="material-symbols-outlined text-3xl text-[var(--dash-text-faint)] block mb-2">group_add</span>
                                <p class="text-sm text-[var(--dash-text-muted)]">
                                    @if (request()->hasAny(['search', 'status', 'branch_id']))
                                        Tidak ada prospective member yang cocok dengan filter.
                                    @else
                                        Belum ada data prospek member.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leads->hasPages())
            <div class="px-4 py-4 border-t border-[var(--dash-card-border)]">
                {{ $leads->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
