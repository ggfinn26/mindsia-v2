@extends('layouts.dashboard')

@section('title', 'Kelola Institusi - MINDSIA')
@section('header_title', 'Kelola Institusi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
     x-data="{ showModal: false, isEdit: false, formData: { id: '', jenjang_institution: '', institution_name: '', regions_id: '' }, showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kelola Institusi</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Total <span class="font-semibold" style="color: var(--dash-text-body);">{{ number_format($totalCount, 0, ',', '.') }}</span> institusi
                @if($totalCount > 2000)(maks. 100 hal. awal)@endif
            </p>
        </div>
        <button @click="showModal = true; isEdit = false; formData = { id: '', jenjang_institution: '', institution_name: '', regions_id: '' }"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity self-start sm:self-auto"
                style="background-color: var(--dash-primary);">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Institusi
        </button>
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
        <form method="GET" action="{{ route('institutions.index') }}"
              class="flex flex-col sm:flex-row flex-wrap gap-3 items-end">
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif

            {{-- Search --}}
            <div class="w-full sm:flex-1 sm:min-w-[200px]">
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                       style="color: var(--dash-text-muted);">Pencarian</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none material-symbols-outlined text-[18px]"
                          style="color: var(--dash-text-faint);">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama institusi..."
                           style="padding-left: 2.5rem !important;">
                </div>
            </div>

            {{-- Jenjang --}}
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                       style="color: var(--dash-text-muted);">Jenjang</label>
                <select name="jenjang" onchange="this.form.submit()">
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangs as $jenjang)
                        <option value="{{ $jenjang }}" @if(request('jenjang') == $jenjang) selected @endif>
                            {{ $jenjang }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Provinsi --}}
            <div class="w-full sm:w-auto">
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                       style="color: var(--dash-text-muted);">Provinsi</label>
                <select name="province_id" onchange="this.form.submit()">
                    <option value="">Semua Provinsi</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}" @if(request('province_id') == $province->id) selected @endif>
                            {{ $province->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">search</span>
                    Cari
                </button>
                @if(request('search') || request('jenjang') || request('province_id'))
                    <a href="{{ route('institutions.index') }}"
                       class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-colors"
                       style="border: 1px solid var(--dash-card-border); color: var(--dash-text-muted); background-color: var(--dash-card-bg);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        Reset
                    </a>
                @endif
            </div>
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
                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ sortUrl('institution_name') }}" class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                Nama Institusi
                                {!! request('sort', 'institution_name') === 'institution_name' ? (request('direction', 'asc') === 'asc' ? ' ↓' : ' ↑') : '' !!}
                            </a>
                        </th>
                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ sortUrl('jenjang_institution') }}" class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                Jenjang
                                {!! request('sort') === 'jenjang_institution' ? (request('direction') === 'asc' ? ' ↓' : ' ↑') : '' !!}
                            </a>
                        </th>
                        <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">
                            <a href="{{ sortUrl('region') }}" class="inline-flex items-center gap-1 hover:opacity-75 transition-opacity">
                                Region
                                {!! request('sort') === 'region' ? (request('direction') === 'asc' ? ' ↓' : ' ↑') : '' !!}
                            </a>
                        </th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($institutions as $institution)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor=''">
                            <td class="px-4 sm:px-6 py-4 text-sm font-medium"
                                style="color: var(--dash-text-heading);">
                                {{ $institution->institution_name }}
                                {{-- Show jenjang inline on mobile --}}
                                <span class="sm:hidden block text-xs mt-0.5"
                                      style="color: var(--dash-text-muted);">
                                    {{ $institution->jenjang_institution }}
                                </span>
                            </td>
                            <td class="hidden sm:table-cell px-6 py-4 text-sm"
                                style="color: var(--dash-text-muted);">
                                {{ $institution->jenjang_institution }}
                            </td>
                            <td class="hidden md:table-cell px-6 py-4 text-sm"
                                style="color: var(--dash-text-muted);">
                                {{ $institution->region->name ?? '—' }}
                            </td>
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button @click="showModal = true; isEdit = true; formData = { id: '{{ $institution->id }}', jenjang_institution: '{{ $institution->jenjang_institution }}', institution_name: '{{ addslashes($institution->institution_name) }}', regions_id: '{{ $institution->regions_id }}' }"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>
                                        <span class="hidden sm:inline">Edit</span>
                                    </button>
                                    <button type="button"
                                            @click="showConfirmModal = true; confirmTitle = 'Hapus Institusi'; confirmMessage = 'Yakin ingin menghapus institusi ini?'; confirmActionUrl = '{{ route('institutions.destroy', $institution->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
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
                            <td colspan="4" class="px-6 py-16 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">domain</span>
                                <p class="text-sm" style="color: var(--dash-text-muted);">Belum ada data institusi.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($institutions->hasPages())
            <div class="px-4 sm:px-6 py-4"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                {{ $institutions->links() }}
            </div>
        @endif
    </div>

    {{-- Confirm modal --}}
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
                <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full" style="background-color: #fee2e2;">
                    <span class="material-symbols-outlined text-[20px]" style="color: #dc2626;">warning</span>
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
                    <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: #dc2626;" x-text="confirmActionText"></button>
                </form>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
        <div class="absolute inset-0 bg-black/50"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showModal = false"></div>
        <div class="relative rounded-2xl shadow-xl w-full max-w-lg overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             @click.stop>
            <div class="flex items-center justify-between px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border);">
                <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);"
                    x-text="isEdit ? 'Edit Institusi' : 'Tambah Institusi'"></h2>
                <button @click="showModal = false" class="flex items-center justify-center w-8 h-8 rounded-lg hover:opacity-75 transition-opacity"
                        style="color: var(--dash-text-muted);">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <form :action="isEdit ? '/institutions/' + formData.id : '{{ route('institutions.store') }}'" method="POST">
                @csrf
                <template x-if="isEdit">
                    @method('PUT')
                </template>
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Nama Institusi</label>
                        <input type="text" name="institution_name" x-model="formData.institution_name" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Jenjang</label>
                        <select name="jenjang_institution" x-model="formData.jenjang_institution" required>
                            <option value="">Pilih Jenjang</option>
                            @foreach($jenjangs as $jenjang)
                                <option value="{{ $jenjang }}">{{ $jenjang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Region</label>
                        <select name="regions_id" x-model="formData.regions_id" required>
                            <option value="">Pilih Region</option>
                            @foreach(\App\Models\Region::all() as $reg)
                                <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="px-6 py-4 flex items-center justify-end gap-3"
                     style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <button type="button" @click="showModal = false"
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
