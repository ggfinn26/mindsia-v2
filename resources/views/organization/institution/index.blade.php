@extends('layouts.dashboard')

@section('title', 'Kelola Institusi')
@section('header_title', 'Kelola Institusi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{
         showModal: false,
         isEdit: false,
         formData: { id: '', jenjang_institution: '', institution_name: '', regions_id: '' },
         showConfirmModal: false,
         confirmTitle: '',
         confirmMessage: '',
         confirmActionUrl: '',
         confirmMethod: 'DELETE',
         confirmActionText: 'Hapus'
     }">

    {{-- Page Header (Pola A) --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Organisasi</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kelola Institusi</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Ditemukan <span class="font-semibold" style="color: var(--dash-text-heading);">{{ number_format($totalCount, 0, ',', '.') }}</span> data institusi
                @if($totalCount > 2000)
                    (Menampilkan maksimal 100 halaman awal).
                @endif
            </p>
        </div>
        <button type="button"
                @click="showModal = true; isEdit = false; formData = { id: '', jenjang_institution: '', institution_name: '', regions_id: '' }"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity shrink-0"
                style="background-color: var(--dash-primary);"
                onmouseover="this.style.opacity='0.9'"
                onmouseout="this.style.opacity='1'">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Institusi
        </button>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
        <span class="material-symbols-outlined text-[18px]">error</span>
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Filter Card --}}
    <div class="rounded-2xl p-4 sm:p-5 shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" action="{{ route('institutions.index') }}" class="flex flex-col sm:flex-row gap-4 items-end">
            {{-- Retain current sort on filter --}}
            @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
            @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif

            <div class="flex-1 w-full sm:w-auto">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Pencarian</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[16px] pointer-events-none"
                          style="color: var(--dash-text-muted);">search</span>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="w-full pr-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           placeholder="Cari nama institusi..."
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
            </div>

            <div class="w-full sm:w-48">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Filter Jenjang</label>
                <select name="jenjang"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'"
                        onchange="this.form.submit()">
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangs as $jenjang)
                        <option value="{{ $jenjang }}" {{ request('jenjang') == $jenjang ? 'selected' : '' }}>{{ $jenjang }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full sm:w-56">
                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                       style="color: var(--dash-text-faint);">Filter Provinsi</label>
                <select name="province_id"
                        class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'"
                        onchange="this.form.submit()">
                    <option value="">Semua Provinsi</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}" {{ request('province_id') == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                @if(request('search') || request('jenjang') || request('province_id'))
                    <a href="{{ route('institutions.index') }}"
                       class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-lg transition-colors flex-1 sm:flex-initial"
                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                        Reset
                    </a>
                @endif

                <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity flex-1 sm:flex-initial"
                        style="background-color: var(--dash-primary);"
                        onmouseover="this.style.opacity='0.9'"
                        onmouseout="this.style.opacity='1'">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    Cari
                </button>
            </div>
        </form>
    </div>

    @php
        $sortUrl = function ($column) {
            $currentSort = request('sort', 'institution_name');
            $currentDir = request('direction', 'asc');
            $dir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
        };
    @endphp

    {{-- Table Card --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide">
                            <a href="{{ $sortUrl('institution_name') }}"
                               class="inline-flex items-center gap-1 transition-colors"
                               style="color: var(--dash-text-faint);"
                               onmouseover="this.style.color='var(--dash-text-heading)'"
                               onmouseout="this.style.color='var(--dash-text-faint)'">
                                <span>Nama Institusi</span>
                                @if(request('sort', 'institution_name') === 'institution_name')
                                    <span class="material-symbols-outlined text-[14px]">
                                        {{ request('direction', 'asc') === 'asc' ? 'arrow_upward' : 'arrow_downward' }}
                                    </span>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide">
                            <a href="{{ $sortUrl('jenjang_institution') }}"
                               class="inline-flex items-center gap-1 transition-colors"
                               style="color: var(--dash-text-faint);"
                               onmouseover="this.style.color='var(--dash-text-heading)'"
                               onmouseout="this.style.color='var(--dash-text-faint)'">
                                <span>Jenjang</span>
                                @if(request('sort') === 'jenjang_institution')
                                    <span class="material-symbols-outlined text-[14px]">
                                        {{ request('direction') === 'asc' ? 'arrow_upward' : 'arrow_downward' }}
                                    </span>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide">
                            <a href="{{ $sortUrl('region') }}"
                               class="inline-flex items-center gap-1 transition-colors"
                               style="color: var(--dash-text-faint);"
                               onmouseover="this.style.color='var(--dash-text-heading)'"
                               onmouseout="this.style.color='var(--dash-text-faint)'">
                                <span>Region</span>
                                @if(request('sort') === 'region')
                                    <span class="material-symbols-outlined text-[14px]">
                                        {{ request('direction') === 'asc' ? 'arrow_upward' : 'arrow_downward' }}
                                    </span>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-faint);">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($institutions as $institution)
                        @php
                            $badgeColors = [
                                'SD' => 'background-color: #fef3c7; color: #b45309;',
                                'SMP' => 'background-color: #dbeafe; color: #1d4ed8;',
                                'MTS' => 'background-color: #e0e7ff; color: #4338ca;',
                                'SMA' => 'background-color: #dcfce7; color: #15803d;',
                                'SMK' => 'background-color: #f3e8ff; color: #7e22ce;',
                                'MA' => 'background-color: #ccfbf1; color: #0f766e;',
                                'PERGURUAN TINGGI' => 'background-color: #ffedd5; color: #c2410c;',
                            ];
                            $badgeStyle = $badgeColors[$institution->jenjang_institution] ?? 'background-color: var(--dash-nav-hover-bg); color: var(--dash-text-body); border: 1px solid var(--dash-card-border);';
                        @endphp
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm font-semibold"
                                style="color: var(--dash-text-heading);">
                                {{ $institution->institution_name }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      style="{{ $badgeStyle }}">
                                    {{ $institution->jenjang_institution }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-sm"
                                style="color: var(--dash-text-body);">
                                {{ $institution->region->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button"
                                        @click="showModal = true; isEdit = true; formData = { id: '{{ $institution->id }}', jenjang_institution: '{{ $institution->jenjang_institution }}', institution_name: '{{ addslashes($institution->institution_name) }}', regions_id: '{{ $institution->regions_id }}' }"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors mr-2"
                                        style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[14px]">edit</span>
                                    Edit
                                </button>
                                <button type="button"
                                        @click="showConfirmModal = true; confirmTitle = 'Hapus Institusi'; confirmMessage = 'Yakin ingin menghapus institusi ini? Tindakan ini tidak dapat dibatalkan.'; confirmActionUrl = '{{ route('institutions.destroy', $institution->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                        style="background-color: #dc2626;"
                                        onmouseover="this.style.opacity='0.9'"
                                        onmouseout="this.style.opacity='1'">
                                    <span class="material-symbols-outlined text-[14px]">delete</span>
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">corporate_fare</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data institusi.</p>
                                <p class="text-xs mt-1 mb-4" style="color: var(--dash-text-faint);">Tambah data institusi baru untuk mulai mengelola organisasi.</p>
                                <button type="button"
                                        @click="showModal = true; isEdit = false; formData = { id: '', jenjang_institution: '', institution_name: '', regions_id: '' }"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                        style="background-color: var(--dash-primary);"
                                        onmouseover="this.style.opacity='0.9'"
                                        onmouseout="this.style.opacity='1'">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    Tambah Institusi
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($institutions->hasPages())
            <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                {{ $institutions->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div x-show="showConfirmModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showConfirmModal = false"
                 class="fixed inset-0 bg-black/60 transition-opacity"
                 aria-hidden="true"></div>

            <div x-show="showConfirmModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10"
                             style="background-color: #fee2e2;">
                            <span class="material-symbols-outlined text-[24px]" style="color: #dc2626;">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1 min-w-0">
                            <h3 class="font-jakarta text-lg font-bold"
                                style="color: var(--dash-text-heading);"
                                x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm"
                                   style="color: var(--dash-text-muted);"
                                   x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 flex flex-col sm:flex-row-reverse gap-3"
                     style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <button type="button"
                            @click="$refs.confirmForm.submit()"
                            class="inline-flex justify-center items-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity sm:w-auto"
                            style="background-color: #dc2626;"
                            onmouseover="this.style.opacity='0.9'"
                            onmouseout="this.style.opacity='1'"
                            x-text="confirmActionText">
                    </button>
                    <button type="button"
                            @click="showConfirmModal = false"
                            class="inline-flex justify-center items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors sm:w-auto"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Tambah / Edit --}}
    <div x-show="showModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showModal = false"
                 class="fixed inset-0 bg-black/60 transition-opacity"
                 aria-hidden="true"></div>

            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <form :action="isEdit ? '/institutions/' + formData.id : '{{ route('institutions.store') }}'" method="POST">
                    @csrf
                    <template x-if="isEdit">
                        @method('PUT')
                    </template>
                    <div class="p-6 rounded-t-2xl space-y-4">
                        <div class="flex items-center justify-between pb-2"
                             style="border-bottom: 1px solid var(--dash-card-border);">
                            <h3 class="font-jakarta text-lg font-bold"
                                style="color: var(--dash-text-heading);"
                                x-text="isEdit ? 'Edit Institusi' : 'Tambah Institusi'"></h3>
                            <button type="button"
                                    @click="showModal = false"
                                    class="p-1 rounded-lg transition-colors"
                                    style="color: var(--dash-text-faint);"
                                    onmouseover="this.style.color='var(--dash-text-heading)'; this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.color='var(--dash-text-faint)'; this.style.backgroundColor='transparent'">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>

                        <div>
                            <label for="institution_name"
                                   class="block text-sm font-medium mb-1.5"
                                   style="color: var(--dash-text-body);">
                                Nama Institusi <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text"
                                   name="institution_name"
                                   id="institution_name"
                                   x-model="formData.institution_name"
                                   required
                                   class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   placeholder="Masukkan nama institusi..."
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        </div>

                        <div>
                            <label for="jenjang_institution"
                                   class="block text-sm font-medium mb-1.5"
                                   style="color: var(--dash-text-body);">
                                Jenjang <span style="color: #dc2626;">*</span>
                            </label>
                            <select name="jenjang_institution"
                                    id="jenjang_institution"
                                    x-model="formData.jenjang_institution"
                                    required
                                    class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                <option value="">Pilih Jenjang...</option>
                                @foreach($jenjangs as $jenjang)
                                    <option value="{{ $jenjang }}">{{ $jenjang }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="regions_id"
                                   class="block text-sm font-medium mb-1.5"
                                   style="color: var(--dash-text-body);">
                                Region <span style="color: #dc2626;">*</span>
                            </label>
                            <select name="regions_id"
                                    id="regions_id"
                                    x-model="formData.regions_id"
                                    required
                                    class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                <option value="">Pilih Region...</option>
                                @foreach(\App\Models\Region::orderBy('name')->get() as $reg)
                                    <option value="{{ $reg->id }}">{{ $reg->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="px-6 py-4 rounded-b-2xl flex flex-col sm:flex-row-reverse gap-3"
                         style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <button type="submit"
                                class="inline-flex justify-center items-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity sm:w-auto"
                                style="background-color: var(--dash-primary);"
                                onmouseover="this.style.opacity='0.9'"
                                onmouseout="this.style.opacity='1'">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Simpan
                        </button>
                        <button type="button"
                                @click="showModal = false"
                                class="inline-flex justify-center items-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors sm:w-auto"
                                style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
