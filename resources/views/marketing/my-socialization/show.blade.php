@extends('layouts.dashboard')

@section('title', 'Sosialisasi Saya — Detail')

@section('content')
@php
    $soc = $mySocialization->socialization;
    $schedule = $soc->schedules->first();

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
    $leadStatusColors = [
        'ALMOST' => 'bg-yellow-100 text-yellow-700',
        'YES'    => 'bg-blue-100 text-blue-700',
        'FIXED'  => 'bg-green-100 text-green-700',
        'NO'     => 'bg-red-100 text-red-700',
    ];
    $leadStatusLabels = ['ALMOST' => 'Hampir', 'YES' => 'Berminat', 'FIXED' => 'Pasti', 'NO' => 'Tidak'];
    $toggleTargets = [
        'ALMOST' => ['?', 'border-yellow-300 text-yellow-600 hover:bg-yellow-50'],
        'YES'    => ['✓', 'border-green-300 text-green-600 hover:bg-green-50'],
        'NO'     => ['✕', 'border-red-300 text-red-500 hover:bg-red-50'],
    ];
    $confirmed = $mySocialization->status === 'confirmed';
    $pmSort = request('pm_sort', 'full_name');
    $pmDir  = request('pm_dir', 'asc');
    $pmSortUrl = fn(string $col) => request()->fullUrlWithQuery([
        'pm_sort' => $col,
        'pm_dir'  => ($pmSort === $col && $pmDir === 'asc') ? 'desc' : 'asc',
        'page'    => 1,
    ]);
    $pmSortIcon = fn(string $col) => $pmSort === $col ? ($pmDir === 'asc' ? ' ↑' : ' ↓') : '';
@endphp

<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

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

    {{-- Header --}}
    <div class="bg-white border border-gray-100 border-l-4 {{ $statusBorderLeft[$soc->status] ?? 'border-l-gray-300' }} rounded-xl shadow-sm mb-5 p-5 sm:p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-1 flex-wrap">
                    <a href="{{ route('my-socializations.index') }}"
                        class="text-sm text-gray-400 hover:text-gray-600 focus:outline-none">← Sosialisasi Saya</a>
                    <span class="text-gray-300">/</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusBadge[$soc->status] ?? 'bg-gray-100 text-gray-600 border-gray-200' }}">
                        {{ $statusLabels[$soc->status] ?? $soc->status }}
                    </span>
                </div>
                <h1 class="text-xl font-bold text-gray-900">
                    {{ $soc->institution?->institution_name ?? $soc->location_name ?? '—' }}
                </h1>
                @if ($schedule)
                    <p class="text-sm text-gray-500 mt-1">
                        {{ \Carbon\Carbon::parse($schedule->schedule_date)->translatedFormat('l, d F Y') }}
                        @if ($schedule->start_time)
                            · {{ \Illuminate\Support\Str::substr($schedule->start_time, 0, 5) }}
                            @if ($schedule->end_time)–{{ \Illuminate\Support\Str::substr($schedule->end_time, 0, 5) }}@endif
                        @endif
                    </p>
                @endif
            </div>
            @if ($confirmed)
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 shrink-0">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    Dikonfirmasi
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Main: Laporan + Lead --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Input Jumlah Kelas --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Laporan Kelas</p>

                @if ($confirmed)
                    <div class="flex items-center gap-3">
                        <span class="text-3xl font-bold text-green-600">{{ $mySocialization->classes_obtained ?? 0 }}</span>
                        <span class="text-sm text-gray-500">kelas diperoleh</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Data sudah dikonfirmasi, tidak bisa diubah.</p>
                @else
                    <form action="{{ route('my-socializations.update', $mySocialization) }}" method="POST"
                        class="flex items-end gap-3">
                        @csrf @method('PATCH')
                        <div class="flex-1">
                            <label for="classes_obtained" class="block text-sm font-medium text-gray-700 mb-1">
                                Jumlah kelas saat sosialisasi
                            </label>
                            <input type="number" id="classes_obtained" name="classes_obtained"
                                value="{{ old('classes_obtained', $mySocialization->classes_obtained ?? 0) }}"
                                min="0" required
                                class="block w-full border-gray-300 rounded-lg shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Simpan
                        </button>
                    </form>
                    @error('classes_obtained')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                @endif
            </div>

            {{-- Daftar Prospective Member --}}
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 flex items-center justify-between border-b border-gray-50">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Calon Member Saya</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $leads->total() }} lead dari sosialisasi ini</p>
                    </div>
                    @if (!$confirmed && !in_array($soc->status, ['completed', 'cancelled']))
                        <a href="{{ route('prospective-members.create', ['socialization_id' => $soc->id]) }}"
                            class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-800 focus:outline-none focus:underline">
                            <span class="material-symbols-outlined text-[16px]">person_add</span>
                            Tambah
                        </a>
                    @endif
                </div>

                @if ($leads->isEmpty())
                    <div class="py-10 text-center">
                        <span class="material-symbols-outlined text-3xl text-gray-300 block mb-2">group_add</span>
                        <p class="text-sm text-gray-400">Belum ada calon member yang diinput.</p>
                        @if (!$confirmed && !in_array($soc->status, ['completed', 'cancelled']))
                            <a href="{{ route('prospective-members.create', ['socialization_id' => $soc->id]) }}"
                                class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:underline">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                                Tambah sekarang
                            </a>
                        @endif
                    </div>
                @else
                    {{-- Sort header --}}
                    <div class="grid grid-cols-[1fr_auto_auto] px-5 py-2 border-b border-gray-50 bg-gray-50/50 text-xs font-medium text-gray-400">
                        <a href="{{ $pmSortUrl('full_name') }}" class="hover:text-gray-700">Nama{{ $pmSortIcon('full_name') }}</a>
                        <a href="{{ $pmSortUrl('status') }}" class="hover:text-gray-700 mr-6">Status{{ $pmSortIcon('status') }}</a>
                        <span>Aksi</span>
                    </div>

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
                                        <span class="text-sm font-semibold text-gray-800">{{ $lead->full_name }}</span>
                                        @if ($lead->institution || $lead->institution_name)
                                            <span class="text-xs text-gray-400 hidden sm:inline">
                                                {{ $lead->institution?->institution_name ?? $lead->institution_name }}
                                            </span>
                                        @endif
                                    </div>
                                    @if ($lead->whatsapp_number)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $lead->whatsapp_number }}</p>
                                    @endif
                                </div>

                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full shrink-0 {{ $leadStatusColors[$lead->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $leadStatusLabels[$lead->status] ?? $lead->status }}
                                </span>

                                {{-- WA Button --}}
                                @if ($waNum)
                                    <div class="relative shrink-0" x-data="waPicker({
                                        wa: '{{ $waNum }}',
                                        nama: @js($lead->full_name),
                                        institusi: @js($lead->institution?->institution_name ?? $lead->institution_name ?? ''),
                                        sosialisasi: @js($soc->institution?->institution_name ?? $soc->location_name ?? ''),
                                        status: '{{ $lead->status }}'
                                    })" @click.away="open = false">
                                        <button @click="open = !open"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-full hover:bg-green-50 focus:outline-none transition-colors"
                                            title="Hubungi via WhatsApp">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 362" fill="none" aria-label="WhatsApp"><path fill="#25D366" fill-rule="evenodd" d="M307.546 52.566C273.709 18.684 228.706.017 180.756 0 81.951 0 1.538 80.404 1.504 179.235c-.017 31.594 8.242 62.432 23.928 89.609L0 361.736l95.024-24.925c26.179 14.285 55.659 21.805 85.655 21.814h.077c98.788 0 179.21-80.413 179.244-179.244.017-47.898-18.608-92.926-52.454-126.807v-.008Zm-126.79 275.788h-.06c-26.73-.008-52.952-7.194-75.831-20.765l-5.44-3.231-56.391 14.791 15.05-54.981-3.542-5.638c-14.912-23.721-22.793-51.139-22.776-79.286.035-82.14 66.867-148.973 149.051-148.973 39.793.017 77.198 15.53 105.328 43.695 28.131 28.157 43.61 65.596 43.593 105.398-.035 82.149-66.867 148.982-148.982 148.982v.008Zm81.719-111.577c-4.478-2.243-26.497-13.073-30.606-14.568-4.108-1.496-7.09-2.243-10.073 2.243-2.982 4.487-11.568 14.577-14.181 17.559-2.613 2.991-5.226 3.361-9.704 1.117-4.477-2.243-18.908-6.97-36.02-22.226-13.313-11.878-22.304-26.54-24.916-31.027-2.613-4.486-.275-6.91 1.959-9.136 2.011-2.011 4.478-5.234 6.721-7.847 2.244-2.613 2.983-4.486 4.478-7.469 1.496-2.991.748-5.603-.369-7.847-1.118-2.243-10.073-24.289-13.812-33.253-3.636-8.732-7.331-7.546-10.073-7.692-2.613-.13-5.595-.155-8.586-.155-2.991 0-7.839 1.118-11.947 5.604-4.108 4.486-15.677 15.324-15.677 37.361s16.047 43.344 18.29 46.335c2.243 2.991 31.585 48.225 76.51 67.632 10.684 4.615 19.029 7.374 25.535 9.437 10.727 3.412 20.49 2.931 28.208 1.779 8.604-1.289 26.498-10.838 30.228-21.298 3.73-10.46 3.73-19.433 2.613-21.298-1.117-1.865-4.108-2.991-8.586-5.234l.008-.017Z" clip-rule="evenodd"/></svg>
                                        </button>
                                        <div x-show="open" x-transition
                                            class="absolute right-0 top-8 z-20 w-52 bg-white border border-gray-200 rounded-xl shadow-lg py-1">
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

                                {{-- Quick status toggle --}}
                                @if ($lead->status !== 'FIXED')
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
                                                        class="w-6 h-6 rounded-full text-[10px] font-bold border transition-colors focus:outline-none {{ $cls }}">
                                                        {{ $icon }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-[10px] text-green-600 shrink-0 font-medium">Terdaftar ✓</span>
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
            </div>
        </div>

        {{-- Sidebar: Info sosialisasi --}}
        <div class="space-y-4">
            <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-5">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-3">Info Sosialisasi</p>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Lokasi</dt>
                        <dd class="font-medium text-gray-800">
                            {{ $soc->institution?->institution_name ?? $soc->location_name ?? '—' }}
                        </dd>
                    </div>
                    @if ($schedule)
                        <div>
                            <dt class="text-xs text-gray-400 mb-0.5">Tanggal</dt>
                            <dd class="font-medium text-gray-800">
                                {{ \Carbon\Carbon::parse($schedule->schedule_date)->translatedFormat('d F Y') }}
                            </dd>
                        </div>
                        @if ($schedule->start_time)
                            <div>
                                <dt class="text-xs text-gray-400 mb-0.5">Waktu</dt>
                                <dd class="font-medium text-gray-800">
                                    {{ \Illuminate\Support\Str::substr($schedule->start_time, 0, 5) }}
                                    @if ($schedule->end_time)–{{ \Illuminate\Support\Str::substr($schedule->end_time, 0, 5) }}@endif
                                </dd>
                            </div>
                        @endif
                    @endif
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Kelas Diperoleh</dt>
                        <dd class="font-bold text-2xl text-gray-900">{{ $mySocialization->classes_obtained ?? 0 }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 mb-0.5">Lead Diinput</dt>
                        <dd class="font-bold text-2xl text-indigo-600">{{ $leads->total() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
