@extends('layouts.dashboard')

@section('title', 'Kelola Cabang - MINDSIA')
@section('header_title', 'Kelola Cabang')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
     x-data="{ showPicModal: false, branchId: '', employeeId: '', showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kelola Cabang</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Daftar cabang, area, dan PIC yang terdaftar.
            </p>
        </div>
        <a href="{{ route('branches.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity self-start sm:self-auto"
           style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Cabang
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium mb-6"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium mb-6"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- Filter bar --}}
    <div class="rounded-2xl p-4 mb-6"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" action="{{ route('branches.index') }}"
              class="flex flex-col sm:flex-row flex-wrap gap-3 items-end">
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif

            {{-- Area --}}
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                       style="color: var(--dash-text-muted);">Area</label>
                <select name="area_id" onchange="this.form.submit()">
                    <option value="">Semua Area</option>
                    @foreach($allAreas as $area)
                        <option value="{{ $area->id }}" @if(request('area_id') == $area->id) selected @endif>
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- PIC --}}
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                       style="color: var(--dash-text-muted);">PIC (MA)</label>
                <select name="pic_id" onchange="this.form.submit()">
                    <option value="">Semua PIC</option>
                    <option value="unassigned" @if(request('pic_id') === 'unassigned') selected @endif>
                        Belum ada PIC
                    </option>
                    @foreach($allPics as $pic)
                        <option value="{{ $pic->id }}" @if(request('pic_id') == $pic->id) selected @endif>
                            {{ $pic->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(request('area_id') || request('pic_id'))
                <a href="{{ route('branches.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-colors w-full sm:w-auto justify-center sm:justify-start"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-muted); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    Reset
                </a>
            @endif
        </form>
    </div>

    @php
        function sortUrl($column) {
            $currentSort = request('sort');
            $currentDir = request('direction', 'asc');
            $dir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
        }
    @endphp

    {{-- Table --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                        {{-- Kode: hidden on mobile --}}
                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ sortUrl('code_branches') }}" class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                Kode
                                {!! request('sort') === 'code_branches' ? (request('direction') === 'asc' ? ' ↓' : ' ↑') : '' !!}
                            </a>
                        </th>
                        {{-- Nama: always --}}
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ sortUrl('branch_name') }}" class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                Nama Cabang
                                {!! request('sort', 'branch_name') === 'branch_name' ? (request('direction', 'asc') === 'asc' ? ' ↓' : ' ↑') : '' !!}
                            </a>
                        </th>
                        {{-- Area: hidden on mobile --}}
                        <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ sortUrl('area') }}" class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                Area
                                {!! request('sort') === 'area' ? (request('direction') === 'asc' ? ' ↓' : ' ↑') : '' !!}
                            </a>
                        </th>
                        {{-- PIC: hidden on mobile --}}
                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ sortUrl('pic') }}" class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                PIC (MA)
                                {!! request('sort') === 'pic' ? (request('direction') === 'asc' ? ' ↓' : ' ↑') : '' !!}
                            </a>
                        </th>
                        {{-- Status: always --}}
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        {{-- Aksi: always --}}
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $branch)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">

                            {{-- Kode --}}
                            <td class="hidden sm:table-cell px-6 py-4 text-sm font-mono whitespace-nowrap"
                                style="color: var(--dash-text-muted);">
                                {{ $branch->code_branches }}
                            </td>

                            {{-- Nama --}}
                            <td class="px-4 sm:px-6 py-4 text-sm font-medium whitespace-nowrap"
                                style="color: var(--dash-text-heading);">
                                {{ $branch->branch_name }}
                                {{-- Show area inline on mobile --}}
                                <span class="md:hidden block text-xs mt-0.5"
                                      style="color: var(--dash-text-muted);">
                                    {{ $branch->area->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Area --}}
                            <td class="hidden md:table-cell px-6 py-4 text-sm whitespace-nowrap"
                                style="color: var(--dash-text-muted);">
                                {{ $branch->area->name ?? '—' }}
                            </td>

                            {{-- PIC --}}
                            <td class="hidden lg:table-cell px-6 py-4 text-sm"
                                style="color: var(--dash-text-muted);">
                                <div class="flex items-center gap-2">
                                    <span>{{ $branch->picEmployee->full_name ?? 'Belum ada PIC' }}</span>
                                    <button type="button"
                                            @click="showPicModal = true; branchId = '{{ $branch->id }}'; employeeId = '{{ $branch->ma_pic_employee_id }}'"
                                            class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold rounded-lg transition-colors"
                                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[12px]">edit</span>Ubah
                                    </button>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                @if($branch->is_active)
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: #f0fdf4; color: #059669;">Aktif</span>
                                @else
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('branches.edit', $branch->id) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>
                                        <span class="hidden sm:inline">Edit</span>
                                    </a>
                                    <button type="button"
                                            @click="showConfirmModal = true; confirmTitle = 'Ubah Status Cabang'; confirmMessage = 'Ubah status aktif/nonaktif cabang ini?'; confirmActionUrl = '{{ route('branches.toggle-active', $branch->id) }}'; confirmMethod = 'PATCH'; confirmActionText = 'Ubah Status'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                            style="background-color: #d97706;">
                                        <span class="material-symbols-outlined text-[14px]">swap_horiz</span>
                                        <span class="hidden sm:inline">Status</span>
                                    </button>
                                    <button type="button"
                                            @click="showConfirmModal = true; confirmTitle = 'Hapus Cabang'; confirmMessage = 'Yakin ingin menghapus cabang ini?'; confirmActionUrl = '{{ route('branches.destroy', $branch->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg text-white hover:opacity-90 transition-opacity"
                                            style="background-color: #dc2626;">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                        <span class="hidden sm:inline">Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">store</span>
                                <p class="text-sm" style="color: var(--dash-text-muted);">Belum ada data cabang.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Confirm modal (hapus + toggle) --}}
    <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/50"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showConfirmModal = false"></div>
        <div class="relative rounded-2xl p-6 max-w-md w-full shadow-xl"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full"
                     :style="confirmMethod === 'DELETE' ? 'background-color: #fee2e2;' : 'background-color: #fef3c7;'">
                    <span class="material-symbols-outlined text-[20px]"
                          :style="confirmMethod === 'DELETE' ? 'color: #dc2626;' : 'color: #d97706;'">warning</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-semibold mb-1" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                    <p class="text-sm" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @click="showConfirmModal = false"
                        class="px-4 py-2 rounded-xl text-sm font-medium"
                        style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);">
                    Batal
                </button>
                <form :action="confirmActionUrl" method="POST" class="inline" x-ref="confirmForm">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            :style="confirmMethod === 'DELETE' ? 'background-color: #dc2626;' : 'background-color: #d97706;'"
                            x-text="confirmActionText"></button>
                </form>
            </div>
        </div>
    </div>

    {{-- PIC Modal --}}
    <div x-show="showPicModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/50"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showPicModal = false"></div>
        <div class="relative rounded-2xl shadow-xl w-full max-w-md overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             @click.stop>
            <div class="flex items-center justify-between px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border);">
                <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Assign PIC Cabang</h2>
                <button @click="showPicModal = false" class="flex items-center justify-center w-8 h-8 rounded-lg hover:opacity-75 transition-opacity"
                        style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <form :action="'/branches/' + branchId + '/assign-pic'" method="POST">
                @csrf
                @method('PATCH')
                <div class="px-6 py-5">
                    <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Pilih MA / PIC</label>
                    <select name="ma_pic_employee_id" x-model="employeeId" required>
                        <option value="">Pilih Pegawai</option>
                        @foreach($allPics as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="px-6 py-4 flex items-center justify-end gap-3"
                     style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <button type="button" @click="showPicModal = false"
                            class="px-4 py-2 rounded-xl text-sm font-medium"
                            style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
