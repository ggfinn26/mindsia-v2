@extends('layouts.dashboard')

@section('title', 'Kelola Cabang - MINDSIA')
@section('header_title', 'Kelola Cabang')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{ showPicModal: false, branchId: '', employeeId: '', showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Organisasi</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kelola Cabang</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Kelola data kantor cabang, area, dan penanggung jawab (PIC).
            </p>
        </div>
        <div>
            <a href="{{ route('branches.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Cabang
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('branches.index') }}"
          class="flex flex-wrap gap-4 items-end p-4 sm:p-5 rounded-2xl shadow-sm"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        {{-- Retain current sort on filter --}}
        @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif
        @if(request('direction')) <input type="hidden" name="direction" value="{{ request('direction') }}"> @endif

        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Filter Area</label>
            <select name="area_id"
                    class="block w-full min-w-[180px] text-sm px-3.5 py-2.5 rounded-lg outline-none transition-all cursor-pointer"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'"
                    onchange="this.form.submit()">
                <option value="">Semua Area</option>
                @foreach($allAreas as $area)
                    <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide"
                   style="color: var(--dash-text-faint);">Filter PIC (MA)</label>
            <select name="pic_id"
                    class="block w-full min-w-[180px] text-sm px-3.5 py-2.5 rounded-lg outline-none transition-all cursor-pointer"
                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                    onfocus="this.style.borderColor='var(--dash-primary)'"
                    onblur="this.style.borderColor='var(--dash-input-border)'"
                    onchange="this.form.submit()">
                <option value="">Semua PIC</option>
                <option value="unassigned" {{ request('pic_id') === 'unassigned' ? 'selected' : '' }}>Belum ada PIC</option>
                @foreach($allPics as $pic)
                    <option value="{{ $pic->id }}" {{ request('pic_id') == $pic->id ? 'selected' : '' }}>{{ $pic->full_name }}</option>
                @endforeach
            </select>
        </div>

        @if(request('area_id') || request('pic_id'))
            <div>
                <a href="{{ route('branches.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                    Reset
                </a>
            </div>
        @endif
    </form>

    @php
        if (!function_exists('sortUrl')) {
            function sortUrl($column) {
                $currentSort = request('sort');
                $currentDir = request('direction', 'asc');
                $dir = ($currentSort === $column && $currentDir === 'asc') ? 'desc' : 'asc';
                return request()->fullUrlWithQuery(['sort' => $column, 'direction' => $dir]);
            }
        }
    @endphp

    {{-- Data Table --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">
                            <a href="{{ sortUrl('code_branches') }}" class="inline-flex items-center gap-1 hover:opacity-80" style="color: var(--dash-text-faint);">
                                Kode {!! request('sort') === 'code_branches' ? (request('direction') === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">
                            <a href="{{ sortUrl('branch_name') }}" class="inline-flex items-center gap-1 hover:opacity-80" style="color: var(--dash-text-faint);">
                                Nama Cabang {!! request('sort', 'branch_name') === 'branch_name' ? (request('direction', 'asc') === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">
                            <a href="{{ sortUrl('area') }}" class="inline-flex items-center gap-1 hover:opacity-80" style="color: var(--dash-text-faint);">
                                Area {!! request('sort') === 'area' ? (request('direction') === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">
                            <a href="{{ sortUrl('pic') }}" class="inline-flex items-center gap-1 hover:opacity-80" style="color: var(--dash-text-faint);">
                                PIC (MA) {!! request('sort') === 'pic' ? (request('direction') === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider" style="color: var(--dash-text-faint);">Status</th>
                        <th class="px-5 py-3.5 text-xs font-semibold uppercase tracking-wider text-right" style="color: var(--dash-text-faint);">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                    @forelse($branches as $branch)
                        <tr style="border-bottom: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-5 py-3.5 whitespace-nowrap font-medium" style="color: var(--dash-text-heading);">
                                {{ $branch->code_branches }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap font-medium" style="color: var(--dash-text-heading);">
                                {{ $branch->branch_name }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap" style="color: var(--dash-text-body);">
                                {{ $branch->area->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap" style="color: var(--dash-text-body);">
                                <div class="inline-flex items-center gap-2">
                                    <span class="font-medium">{{ $branch->picEmployee->full_name ?? 'Belum ada PIC' }}</span>
                                    <button type="button"
                                            @click="showPicModal = true; branchId = '{{ $branch->id }}'; employeeId = '{{ $branch->ma_pic_employee_id }}'"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg transition-colors shrink-0"
                                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-primary-text);"
                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>
                                        Ubah
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($branch->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md"
                                          style="background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #059669;">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-md"
                                          style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    {{-- Edit --}}
                                    <a href="{{ route('branches.edit', $branch->id) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg transition-colors"
                                       style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                       onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                       onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">edit</span>
                                        Edit
                                    </a>
                                    {{-- Toggle Status --}}
                                    <button type="button"
                                            @click="showConfirmModal = true; confirmTitle = 'Ubah Status Cabang'; confirmMessage = 'Ubah status cabang {{ addslashes($branch->branch_name) }}?'; confirmActionUrl = '{{ route('branches.toggle-active', $branch->id) }}'; confirmMethod = 'PATCH'; confirmActionText = 'Ubah Status'"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg transition-colors"
                                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                        <span class="material-symbols-outlined text-[14px]">{{ $branch->is_active ? 'toggle_off' : 'toggle_on' }}</span>
                                        Status
                                    </button>
                                    {{-- Delete --}}
                                    <button type="button"
                                            @click="showConfirmModal = true; confirmTitle = 'Hapus Cabang'; confirmMessage = 'Yakin ingin menghapus cabang {{ addslashes($branch->branch_name) }}?'; confirmActionUrl = '{{ route('branches.destroy', $branch->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                            style="background-color: #dc2626;">
                                        <span class="material-symbols-outlined text-[14px]">delete</span>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                                      style="color: var(--dash-text-faint);">store</span>
                                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada data cabang.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus / Tindakan --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="showConfirmModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full overflow-hidden"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             :style="confirmMethod === 'DELETE' ? 'background-color: #fee2e2; color: #dc2626;' : 'background-color: #fef3c7; color: #d97706;'">
                            <span class="material-symbols-outlined text-[24px]">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showConfirmModal = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="button" @click="$refs.confirmForm.submit()"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            :style="confirmMethod === 'DELETE' ? 'background-color: #dc2626;' : 'background-color: var(--dash-primary);'"
                            x-text="confirmActionText">
                    </button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </div>

    {{-- Modal PIC --}}
    <div x-show="showPicModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showPicModal" @click="showPicModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="showPicModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <form :action="'{{ url('/branches') }}/' + branchId + '/assign-pic'" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="p-6 rounded-t-2xl">
                        <div class="flex items-center justify-between pb-4 mb-4"
                             style="border-bottom: 1px solid var(--dash-card-border);">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Assign PIC Cabang</h3>
                            <button type="button" @click="showPicModal = false" class="p-1 rounded-lg hover:opacity-80 transition-opacity" style="color: var(--dash-text-faint);">
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>

                        <div>
                            <label for="ma_pic_employee_id" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                                Pilih MA / PIC <span class="text-red-500">*</span>
                            </label>
                            <select name="ma_pic_employee_id" id="ma_pic_employee_id" x-model="employeeId" required
                                    class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                <option value="">Pilih Pegawai</option>
                                @foreach($allPics as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                         style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                        <button type="button" @click="showPicModal = false"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                                style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">save</span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

