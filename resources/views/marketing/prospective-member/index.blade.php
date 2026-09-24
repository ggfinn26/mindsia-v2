@extends('layouts.dashboard')

@section('title', 'Prospective Member')

@section('content')
@php
    $statusColors = [
        'ALMOST' => 'bg-yellow-100 text-yellow-700',
        'YES'    => 'bg-blue-100 text-blue-700',
        'FIXED'  => 'bg-green-100 text-green-700',
        'NO'     => 'bg-red-100 text-red-700',
    ];
    $statusLabels = ['ALMOST' => 'Hampir', 'YES' => 'Berminat', 'FIXED' => 'Pasti', 'NO' => 'Tidak'];
    $toggleTargets = [
        'ALMOST' => ['?', 'border-yellow-300 text-yellow-600 hover:bg-yellow-50'],
        'YES'    => ['✓', 'border-green-300 text-green-600 hover:bg-green-50'],
        'NO'     => ['✕', 'border-red-300 text-red-500 hover:bg-red-50'],
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
        <h1 class="text-xl font-bold text-gray-900">Prospective Member</h1>
        @can('marketing.prospective_member.create')
            <a href="{{ route('prospective-members.create') }}"
                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <span class="material-symbols-outlined text-[16px]">person_add</span>
                Tambah Prospective Member
            </a>
        @endcan
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tab: Milik Saya / Semua Data --}}
    @if ($canViewWide)
        <div class="flex gap-1 mb-4 border-b border-gray-200">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'mine', 'page' => 1]) }}"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors focus:outline-none
                    {{ $currentView === 'mine' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Milik Saya
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'all', 'page' => 1]) }}"
                class="px-4 py-2 text-sm font-medium border-b-2 transition-colors focus:outline-none
                    {{ $currentView === 'all' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Semua Data
            </a>
        </div>
    @endif

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('prospective-members.index') }}"
        class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
        {{-- Preserve tab view --}}
        @if ($canViewWide)
            <input type="hidden" name="view" value="{{ $currentView }}">
        @endif

        <div class="flex flex-wrap gap-3 items-end">
            {{-- Search --}}
            <div class="flex-1 min-w-[180px]">
                <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nama atau nomor WA..."
                    class="w-full bg-gray-50 border border-gray-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-indigo-400 focus:ring-1 focus:ring-indigo-200 transition-colors">
            </div>

            {{-- Filter Status --}}
            <div>
                <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Status</label>
                <select name="status" onchange="this.form.submit()"
                    class="bg-gray-50 border border-gray-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-indigo-400 transition-colors">
                    <option value="">Semua Status</option>
                    @foreach (['ALMOST' => 'Hampir', 'YES' => 'Berminat', 'FIXED' => 'Pasti', 'NO' => 'Tidak'] as $val => $label)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Cabang (area/national only) --}}
            @if ($canViewWide && $branches->isNotEmpty())
                <div>
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Cabang</label>
                    <select name="branch_id" onchange="this.form.submit()"
                        class="bg-gray-50 border border-gray-200 rounded-lg text-sm px-3 py-2 outline-none focus:border-indigo-400 transition-colors">
                        <option value="">Semua Cabang</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Tombol submit + reset --}}
            <div class="flex gap-2">
                <button type="submit"
                    class="px-3 py-2 text-sm font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cari
                </button>
                @if (request()->hasAny(['search', 'status', 'branch_id', 'socialization_id']))
                    <a href="{{ route('prospective-members.index', array_filter(['view' => $canViewWide ? $currentView : null])) }}"
                        class="px-3 py-2 text-sm text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="px-4 py-3 text-left">
                            <a href="{{ $sortUrl('full_name') }}"
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide hover:text-gray-600 focus:outline-none">
                                Nama {{ $sortIcon('full_name') }}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">WhatsApp</th>
                        <th class="px-4 py-3 text-left">
                            <a href="{{ $sortUrl('status') }}"
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide hover:text-gray-600 focus:outline-none">
                                Status {{ $sortIcon('status') }}
                            </a>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden md:table-cell">Sosialisasi</th>
                        @if ($canViewWide && $currentView === 'all')
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide hidden lg:table-cell">PIC</th>
                        @endif
                        <th class="px-4 py-3 text-left">
                            <a href="{{ $sortUrl('created_at') }}"
                                class="text-xs font-semibold text-gray-400 uppercase tracking-wide hover:text-gray-600 focus:outline-none hidden sm:inline">
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
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('prospective-members.show', $lead) }}"
                                    class="font-medium text-gray-900 hover:text-indigo-600 focus:outline-none focus:underline">
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
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-green-50 focus:outline-none transition-colors"
                                            title="{{ $lead->whatsapp_number }}">
                                            <img src="{{ asset('img/whatsapp.svg') }}" class="w-5 h-5" alt="WhatsApp">
                                        </button>
                                        <div x-show="open" x-transition
                                            class="absolute left-0 top-7 z-20 w-56 bg-white border border-gray-200 rounded-xl shadow-lg py-1">
                                            <button @click="send(null)"
                                                class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">
                                                Kirim langsung (tanpa template)
                                            </button>
                                            <template x-if="templates.length > 0">
                                                <div>
                                                    <p class="px-4 py-1 text-[10px] font-semibold text-gray-400 uppercase tracking-widest border-t border-gray-100 mt-1">Template</p>
                                                    <template x-for="t in templates" :key="t.id">
                                                        <button @click="send(t)"
                                                            class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-indigo-50 hover:text-indigo-700"
                                                            x-text="t.name">
                                                        </button>
                                                    </template>
                                                </div>
                                            </template>
                                            <p x-show="templates.length === 0"
                                                class="px-4 py-2 text-[10px] text-gray-400 border-t border-gray-100 mt-1">
                                                Belum ada template. Buat di pengaturan WA.
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $statusColors[$lead->status] ?? 'bg-gray-100 text-gray-600' }}">
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
                                            <span class="text-[10px] text-green-600 font-medium">Terdaftar ✓</span>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                @if ($lead->socialization)
                                    <a href="{{ route('socializations.show', $lead->socialization_id) }}"
                                        class="text-xs text-gray-600 hover:text-indigo-600 focus:outline-none focus:underline line-clamp-1">
                                        {{ $lead->socialization->location_name }}
                                    </a>
                                    @if ($lead->socialization->branch)
                                        <p class="text-[10px] text-gray-400">{{ $lead->socialization->branch->branch_name }}</p>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">Walk-in</span>
                                @endif
                            </td>
                            @if ($canViewWide && $currentView === 'all')
                                <td class="px-4 py-3 hidden lg:table-cell text-xs text-gray-500">
                                    {{ $lead->capturedBy?->full_name ?? '-' }}
                                </td>
                            @endif
                            <td class="px-4 py-3 hidden sm:table-cell text-xs text-gray-400 whitespace-nowrap">
                                {{ $lead->created_at->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('prospective-members.show', $lead) }}"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 focus:outline-none focus:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center">
                                <span class="material-symbols-outlined text-3xl text-gray-300 block mb-2">group_add</span>
                                <p class="text-sm text-gray-500">
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
            <div class="px-4 py-4 border-t border-gray-100">
                {{ $leads->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
