@extends('layouts.dashboard')

@section('title', 'Calon Anggota Saya - MINDSIA')
@section('header_title', 'Calon Anggota Saya')

@section('content')
@php
    $statusConfig = [
        'ALMOST' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'Hampir'],
        'YES'    => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'label' => 'Berminat'],
        'FIXED'  => ['bg' => '#f0fdf4', 'text' => '#166534', 'label' => 'Pasti'],
        'NO'     => ['bg' => '#fef2f2', 'text' => '#991b1b', 'label' => 'Tidak'],
    ];
    $toggleTargets = [
        'ALMOST' => ['?', '#d97706'],
        'YES'    => ['✓', '#059669'],
        'NO'     => ['✕', '#dc2626'],
    ];
    $currentSort = request('sort', 'created_at');
    $currentDir  = request('dir', 'desc');
    $sortUrl = fn(string $col) => request()->fullUrlWithQuery([
        'sort' => $col,
        'dir'  => ($currentSort === $col && $currentDir === 'asc') ? 'desc' : 'asc',
        'page' => 1,
    ]);
    $sortIcon = fn(string $col) => $currentSort === $col ? ($currentDir === 'asc' ? ' ↑' : ' ↓') : '';
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Pemasaran</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Calon Anggota Saya</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Total {{ $leads->total() }} calon anggota yang Anda tangani.
            </p>
        </div>
        @can('marketing.prospective_member.create')
            <a href="{{ route('prospective-members.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Tambah Calon Anggota
            </a>
        @endcan
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Filter Card --}}
    <form method="GET" action="{{ route('my-prospective-members.index') }}"
          class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        {{-- Search --}}
        <div class="flex-1 min-w-[200px]">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Cari Calon</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                      style="color: var(--dash-text-muted);">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nama atau nomor WhatsApp..."
                       class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                       style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
            </div>
        </div>

        {{-- Status --}}
        <div class="w-full sm:w-44">
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Status</label>
            <select name="status" onchange="this.form.submit()"
                    class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'">
                <option value="">Semua Status</option>
                @foreach($statusConfig as $val => $cfg)
                    <option value="{{ $val }}" @selected(request('status') === $val)>{{ $cfg['label'] }}</option>
                @endforeach
            </select>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('my-prospective-members.index') }}"
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

    {{-- Tabel Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left">
                            <a href="{{ $sortUrl('full_name') }}"
                               class="text-xs font-semibold uppercase tracking-wide hover:underline focus:outline-none"
                               style="color: var(--dash-text-muted);">
                                Nama {{ $sortIcon('full_name') }}
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">WhatsApp</th>
                        <th class="px-5 py-3 text-left">
                            <a href="{{ $sortUrl('status') }}"
                               class="text-xs font-semibold uppercase tracking-wide hover:underline focus:outline-none"
                               style="color: var(--dash-text-muted);">
                                Status {{ $sortIcon('status') }}
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide hidden md:table-cell"
                            style="color: var(--dash-text-muted);">Sosialisasi</th>
                        <th class="px-5 py-3 text-left hidden sm:table-cell">
                            <a href="{{ $sortUrl('created_at') }}"
                               class="text-xs font-semibold uppercase tracking-wide hover:underline focus:outline-none"
                               style="color: var(--dash-text-muted);">
                                Tanggal {{ $sortIcon('created_at') }}
                            </a>
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $lead)
                    @php
                        $waNum = preg_replace('/[^0-9]/', '', $lead->whatsapp_number ?? '');
                        if (str_starts_with($waNum, '0')) $waNum = '62' . substr($waNum, 1);
                        elseif ($waNum && !str_starts_with($waNum, '62')) $waNum = '62' . $waNum;
                        $sc = $statusConfig[$lead->status] ?? ['bg' => '#f8fafc', 'text' => '#475569', 'label' => $lead->status];
                    @endphp
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">

                        {{-- Nama --}}
                        <td class="px-5 py-3.5">
                            <a href="{{ route('prospective-members.show', $lead) }}"
                               class="font-semibold hover:underline"
                               style="color: var(--dash-text-heading);">
                                {{ $lead->full_name }}
                            </a>
                            @if($lead->institution || $lead->institution_name)
                                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                    {{ $lead->institution?->institution_name ?? $lead->institution_name }}
                                </p>
                            @endif
                        </td>

                        {{-- WhatsApp --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            @if($waNum)
                                <div class="relative" x-data="waPicker({
                                    wa: '{{ $waNum }}',
                                    nama: @js($lead->full_name),
                                    institusi: @js($lead->institution?->institution_name ?? $lead->institution_name ?? ''),
                                    sosialisasi: @js($lead->socialization?->institution?->institution_name ?? $lead->socialization?->location_name ?? ''),
                                    status: '{{ $lead->status }}'
                                })" @click.away="open = false">
                                    <button type="button" @click="open = !open"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full hover:bg-emerald-50 focus:outline-none transition-colors"
                                            title="{{ $lead->whatsapp_number }}">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 362" fill="none"><path fill="#25D366" fill-rule="evenodd" d="M307.546 52.566C273.709 18.684 228.706.017 180.756 0 81.951 0 1.538 80.404 1.504 179.235c-.017 31.594 8.242 62.432 23.928 89.609L0 361.736l95.024-24.925c26.179 14.285 55.659 21.805 85.655 21.814h.077c98.788 0 179.21-80.413 179.244-179.244.017-47.898-18.608-92.926-52.454-126.807v-.008Zm-126.79 275.788h-.06c-26.73-.008-52.952-7.194-75.831-20.765l-5.44-3.231-56.391 14.791 15.05-54.981-3.542-5.638c-14.912-23.721-22.793-51.139-22.776-79.286.035-82.14 66.867-148.973 149.051-148.973 39.793.017 77.198 15.53 105.328 43.695 28.131 28.157 43.61 65.596 43.593 105.398-.035 82.149-66.867 148.982-148.982 148.982v.008Zm81.719-111.577c-4.478-2.243-26.497-13.073-30.606-14.568-4.108-1.496-7.09-2.243-10.073 2.243-2.982 4.487-11.568 14.577-14.181 17.559-2.613 2.991-5.226 3.361-9.704 1.117-4.477-2.243-18.908-6.97-36.02-22.226-13.313-11.878-22.304-26.54-24.916-31.027-2.613-4.486-.275-6.91 1.959-9.136 2.011-2.011 4.478-5.234 6.721-7.847 2.244-2.613 2.983-4.486 4.478-7.469 1.496-2.991.748-5.603-.369-7.847-1.118-2.243-10.073-24.289-13.812-33.253-3.636-8.732-7.331-7.546-10.073-7.692-2.613-.13-5.595-.155-8.586-.155-2.991 0-7.839 1.118-11.947 5.604-4.108 4.486-15.677 15.324-15.677 37.361s16.047 43.344 18.29 46.335c2.243 2.991 31.585 48.225 76.51 67.632 10.684 4.615 19.029 7.374 25.535 9.437 10.727 3.412 20.49 2.931 28.208 1.779 8.604-1.289 26.498-10.838 30.228-21.298 3.73-10.46 3.73-19.433 2.613-21.298-1.117-1.865-4.108-2.991-8.586-5.234l.008-.017Z" clip-rule="evenodd"/></svg>
                                    </button>
                                    {{-- WA dropdown --}}
                                    <div x-show="open" x-transition
                                         class="absolute left-0 top-8 z-20 w-56 rounded-xl shadow-xl py-1.5"
                                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                        <button type="button" @click="send(null)"
                                                class="w-full text-left px-4 py-2 text-xs font-medium transition-colors"
                                                style="color: var(--dash-text-body);"
                                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                onmouseout="this.style.backgroundColor='transparent'">
                                            Kirim langsung
                                        </button>
                                        <template x-if="templates.length > 0">
                                            <div>
                                                <p class="px-4 py-1 text-[10px] font-semibold uppercase tracking-wider mt-1 border-t"
                                                   style="border-color: var(--dash-card-border); color: var(--dash-text-faint);">Template Pesan</p>
                                                <template x-for="t in templates" :key="t.id">
                                                    <button type="button" @click="send(t)"
                                                            class="w-full text-left px-4 py-2 text-xs font-medium transition-colors"
                                                            style="color: var(--dash-text-heading);"
                                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                            onmouseout="this.style.backgroundColor='transparent'"
                                                            x-text="t.name"></button>
                                                </template>
                                            </div>
                                        </template>
                                        <p x-show="templates.length === 0"
                                           class="px-4 py-2 text-[10px] mt-1 border-t"
                                           style="border-color: var(--dash-card-border); color: var(--dash-text-faint);">
                                            Belum ada template.
                                        </p>
                                    </div>
                                </div>
                            @else
                                <span style="color: var(--dash-text-faint);">-</span>
                            @endif
                        </td>

                        {{-- Status + toggle --}}
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full"
                                      style="background-color: {{ $sc['bg'] }}; color: {{ $sc['text'] }};">
                                    {{ $sc['label'] }}
                                </span>
                                @if($lead->status !== 'FIXED')
                                    @foreach($toggleTargets as $targetStatus => [$icon, $color])
                                        @if($targetStatus !== $lead->status)
                                            <form method="POST" action="{{ route('my-prospective-members.update-status', $lead) }}" class="inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $targetStatus }}">
                                                <button type="submit"
                                                        title="{{ $statusConfig[$targetStatus]['label'] }}"
                                                        class="w-5 h-5 rounded-full text-[9px] font-bold border transition-colors focus:outline-none"
                                                        style="border-color: {{ $color }}; color: {{ $color }};">
                                                    {{ $icon }}
                                                </button>
                                            </form>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-[10px] font-bold text-emerald-600">Terdaftar ✓</span>
                                @endif
                            </div>
                        </td>

                        {{-- Sosialisasi --}}
                        <td class="px-5 py-3.5 hidden md:table-cell">
                            @if($lead->socialization)
                                @php $socName = $lead->socialization->institution?->institution_name ?? $lead->socialization->location_name ?? '-'; @endphp
                                <span class="text-xs line-clamp-1" style="color: var(--dash-text-body);">{{ $socName }}</span>
                            @else
                                <span class="text-xs" style="color: var(--dash-text-muted);">Walk-in</span>
                            @endif
                        </td>

                        {{-- Tanggal --}}
                        <td class="px-5 py-3.5 text-xs hidden sm:table-cell whitespace-nowrap"
                            style="color: var(--dash-text-muted);">
                            {{ $lead->created_at->format('d M Y') }}
                        </td>

                        {{-- Aksi --}}
                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                            <a href="{{ route('prospective-members.show', $lead) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                Lihat
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                  style="color: var(--dash-text-faint);">person_search</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada calon anggota.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
        <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
            {{ $leads->withQueryString()->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
