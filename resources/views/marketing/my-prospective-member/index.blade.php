@extends('layouts.dashboard')

@section('title', 'Calon Member Saya')

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
    $currentSort = request('sort', 'created_at');
    $currentDir  = request('dir', 'desc');
    $sortUrl = fn(string $col) => request()->fullUrlWithQuery([
        'sort' => $col,
        'dir'  => ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc',
        'page' => 1,
    ]);
    $sortIcon = fn(string $col) => $currentSort === $col ? ($currentDir === 'asc' ? '↑' : '↓') : '';
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight text-[var(--dash-text-heading)]">Calon Member Saya</h1>
            <p class="mt-1 text-sm text-[var(--dash-text-muted)]">{{ $leads->total() }} total prospek member</p>
        </div>
        @can('marketing.prospective_member.create')
            <a href="{{ route('prospective-members.create') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-white bg-[var(--dash-primary)] rounded-lg hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Tambah Prospek
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="flex gap-3 px-4 py-3 text-sm rounded-lg" style="border: 1px solid #a7f3d0; background-color: #ecfdf5; color: #065f46;">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    {{-- Filter --}}
    <form method="GET" action="{{ route('my-prospective-members.index') }}"
        class="bg-[var(--dash-card-bg)] rounded-2xl border border-[var(--dash-card-border)] p-5">
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
                    @foreach ($statusLabels as $val => $label)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-3 py-2 text-sm font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-800">Cari</button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('my-prospective-members.index') }}"
                        class="px-3 py-2 text-sm text-[var(--dash-text-muted)] bg-[var(--dash-input-bg)] rounded-lg hover:bg-gray-200">Reset</a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-2xl">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-[var(--dash-card-border)]">
                    <th class="px-4 py-3 text-left">
                        <a href="{{ $sortUrl('full_name') }}" class="text-xs font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide hover:text-[var(--dash-text-muted)]">
                            Nama {{ $sortIcon('full_name') }}
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide">WhatsApp</th>
                    <th class="px-4 py-3 text-left">
                        <a href="{{ $sortUrl('status') }}" class="text-xs font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide hover:text-[var(--dash-text-muted)]">
                            Status {{ $sortIcon('status') }}
                        </a>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide hidden md:table-cell">Sosialisasi</th>
                    <th class="px-4 py-3 text-left hidden sm:table-cell">
                        <a href="{{ $sortUrl('created_at') }}" class="text-xs font-semibold text-[var(--dash-text-faint)] uppercase tracking-wide hover:text-[var(--dash-text-muted)]">
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
                            @if ($lead->institution || $lead->institution_name)
                                <p class="text-xs text-[var(--dash-text-faint)]">{{ $lead->institution?->institution_name ?? $lead->institution_name }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if ($waNum)
                                <div class="relative" x-data="waPicker({
                                    wa: '{{ $waNum }}',
                                    nama: @js($lead->full_name),
                                    institusi: @js($lead->institution?->institution_name ?? $lead->institution_name ?? ''),
                                    sosialisasi: @js($lead->socialization?->institution?->institution_name ?? $lead->socialization?->location_name ?? ''),
                                    status: '{{ $lead->status }}'
                                })" @click.away="open = false">
                                    <button @click="open = !open"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-emerald-50 [.mode-dark_&]:bg-emerald-500/10 focus:outline-none transition-colors"
                                        title="{{ $lead->whatsapp_number }}">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 362" fill="none" aria-label="WhatsApp"><path fill="#25D366" fill-rule="evenodd" d="M307.546 52.566C273.709 18.684 228.706.017 180.756 0 81.951 0 1.538 80.404 1.504 179.235c-.017 31.594 8.242 62.432 23.928 89.609L0 361.736l95.024-24.925c26.179 14.285 55.659 21.805 85.655 21.814h.077c98.788 0 179.21-80.413 179.244-179.244.017-47.898-18.608-92.926-52.454-126.807v-.008Zm-126.79 275.788h-.06c-26.73-.008-52.952-7.194-75.831-20.765l-5.44-3.231-56.391 14.791 15.05-54.981-3.542-5.638c-14.912-23.721-22.793-51.139-22.776-79.286.035-82.14 66.867-148.973 149.051-148.973 39.793.017 77.198 15.53 105.328 43.695 28.131 28.157 43.61 65.596 43.593 105.398-.035 82.149-66.867 148.982-148.982 148.982v.008Zm81.719-111.577c-4.478-2.243-26.497-13.073-30.606-14.568-4.108-1.496-7.09-2.243-10.073 2.243-2.982 4.487-11.568 14.577-14.181 17.559-2.613 2.991-5.226 3.361-9.704 1.117-4.477-2.243-18.908-6.97-36.02-22.226-13.313-11.878-22.304-26.54-24.916-31.027-2.613-4.486-.275-6.91 1.959-9.136 2.011-2.011 4.478-5.234 6.721-7.847 2.244-2.613 2.983-4.486 4.478-7.469 1.496-2.991.748-5.603-.369-7.847-1.118-2.243-10.073-24.289-13.812-33.253-3.636-8.732-7.331-7.546-10.073-7.692-2.613-.13-5.595-.155-8.586-.155-2.991 0-7.839 1.118-11.947 5.604-4.108 4.486-15.677 15.324-15.677 37.361s16.047 43.344 18.29 46.335c2.243 2.991 31.585 48.225 76.51 67.632 10.684 4.615 19.029 7.374 25.535 9.437 10.727 3.412 20.49 2.931 28.208 1.779 8.604-1.289 26.498-10.838 30.228-21.298 3.73-10.46 3.73-19.433 2.613-21.298-1.117-1.865-4.108-2.991-8.586-5.234l.008-.017Z" clip-rule="evenodd"/></svg>
                                    </button>
                                    <div x-show="open" x-transition
                                        class="absolute left-0 top-7 z-20 w-56 bg-[var(--dash-card-bg)] border border-[var(--dash-card-border)] rounded-xl shadow-lg py-1">
                                        <button @click="send(null)" class="w-full text-left px-4 py-2 text-xs text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)]">Kirim langsung</button>
                                        <template x-if="templates.length > 0">
                                            <div>
                                                <p class="px-4 py-1 text-[10px] font-semibold text-[var(--dash-text-faint)] uppercase tracking-widest border-t border-[var(--dash-card-border)] mt-1">Template</p>
                                                <template x-for="t in templates" :key="t.id">
                                                    <button @click="send(t)" class="w-full text-left px-4 py-2 text-xs text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] hover:text-[var(--dash-primary)]" x-text="t.name"></button>
                                                </template>
                                            </div>
                                        </template>
                                        <p x-show="templates.length === 0" class="px-4 py-2 text-[10px] text-[var(--dash-text-faint)] border-t border-[var(--dash-card-border)] mt-1">
                                            Belum ada template.
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
                                
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            @if ($lead->socialization)
                                @php
                                    $socName = $lead->socialization->institution?->institution_name ?? $lead->socialization->location_name ?? '—';
                                @endphp
                                <span class="text-xs text-[var(--dash-text-muted)] line-clamp-1">{{ $socName }}</span>
                            @else
                                <span class="text-xs text-[var(--dash-text-faint)]">Walk-in</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-[var(--dash-text-faint)] hidden sm:table-cell whitespace-nowrap">
                            {{ $lead->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('prospective-members.show', $lead) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-primary)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-body)'">
                                Detail <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-[var(--dash-text-faint)]">
                            Belum ada prospective member.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if ($leads->hasPages())
            <div class="px-4 py-3 border-t border-[var(--dash-card-border)]">{{ $leads->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
