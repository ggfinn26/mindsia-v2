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
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 362" fill="none" aria-label="WhatsApp"><path fill="#25D366" fill-rule="evenodd" d="M307.546 52.566C273.709 18.684 228.706.017 180.756 0 81.951 0 1.538 80.404 1.504 179.235c-.017 31.594 8.242 62.432 23.928 89.609L0 361.736l95.024-24.925c26.179 14.285 55.659 21.805 85.655 21.814h.077c98.788 0 179.21-80.413 179.244-179.244.017-47.898-18.608-92.926-52.454-126.807v-.008Zm-126.79 275.788h-.06c-26.73-.008-52.952-7.194-75.831-20.765l-5.44-3.231-56.391 14.791 15.05-54.981-3.542-5.638c-14.912-23.721-22.793-51.139-22.776-79.286.035-82.14 66.867-148.973 149.051-148.973 39.793.017 77.198 15.53 105.328 43.695 28.131 28.157 43.61 65.596 43.593 105.398-.035 82.149-66.867 148.982-148.982 148.982v.008Zm81.719-111.577c-4.478-2.243-26.497-13.073-30.606-14.568-4.108-1.496-7.09-2.243-10.073 2.243-2.982 4.487-11.568 14.577-14.181 17.559-2.613 2.991-5.226 3.361-9.704 1.117-4.477-2.243-18.908-6.97-36.02-22.226-13.313-11.878-22.304-26.54-24.916-31.027-2.613-4.486-.275-6.91 1.959-9.136 2.011-2.011 4.478-5.234 6.721-7.847 2.244-2.613 2.983-4.486 4.478-7.469 1.496-2.991.748-5.603-.369-7.847-1.118-2.243-10.073-24.289-13.812-33.253-3.636-8.732-7.331-7.546-10.073-7.692-2.613-.13-5.595-.155-8.586-.155-2.991 0-7.839 1.118-11.947 5.604-4.108 4.486-15.677 15.324-15.677 37.361s16.047 43.344 18.29 46.335c2.243 2.991 31.585 48.225 76.51 67.632 10.684 4.615 19.029 7.374 25.535 9.437 10.727 3.412 20.49 2.931 28.208 1.779 8.604-1.289 26.498-10.838 30.228-21.298 3.73-10.46 3.73-19.433 2.613-21.298-1.117-1.865-4.108-2.991-8.586-5.234l.008-.017Z" clip-rule="evenodd"/></svg>
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
