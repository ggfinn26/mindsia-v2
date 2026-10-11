@extends('layouts.dashboard')

@section('title', 'Hari Libur - MINDSIA')
@section('header_title', 'Hari Libur')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Absensi & Jadwal</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Hari Libur</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Hari libur nasional dan khusus yang mempengaruhi perhitungan hari kerja.
            </p>
        </div>
        @can('attendance.holiday.manage')
            <div>
                <a href="{{ route('holidays.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0"
                   style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Hari Libur
                </a>
            </div>
        @endcan
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
             style="background-color: #fef2f2; border-color: #fecaca; color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Data Table --}}
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        @if ($holidays->isEmpty())
            <div class="text-center py-12 px-4">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">event_busy</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada hari libur terdaftar.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Nama Hari Libur</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Tanggal Mulai</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Tanggal Selesai</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Durasi</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                        @foreach ($holidays as $holiday)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3.5 whitespace-nowrap font-medium"
                                    style="color: var(--dash-text-heading);">
                                    {{ $holiday->holiday_name }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm"
                                    style="color: var(--dash-text-body);">
                                    {{ $holiday->holiday_start_date->format('d M Y') }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm"
                                    style="color: var(--dash-text-body);">
                                    {{ $holiday->holiday_end_date->format('d M Y') }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm"
                                    style="color: var(--dash-text-body);">
                                    @php $days = $holiday->holiday_start_date->diffInDays($holiday->holiday_end_date) + 1 @endphp
                                    <span class="px-2.5 py-1 inline-flex text-xs font-semibold rounded-md"
                                          style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-heading); border: 1px solid var(--dash-card-border);">
                                        {{ $days }} hari
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                    @can('attendance.holiday.manage')
                                        <div class="inline-flex items-center gap-2 justify-end">
                                            <a href="{{ route('holidays.edit', $holiday) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">edit</span>
                                                Edit
                                            </a>
                                            <button type="button"
                                                    @click="showConfirmModal = true; confirmTitle = 'Hapus Hari Libur'; confirmMessage = 'Yakin ingin menghapus hari libur {{ addslashes($holiday->holiday_name) }}? Tindakan tidak bisa dibatalkan.'; confirmActionUrl = '{{ route('holidays.destroy', $holiday) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                                    style="background-color: var(--dash-danger);">
                                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                                Hapus
                                            </button>
                                        </div>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($holidays->hasPages())
                <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                    {{ $holidays->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="showConfirmModal" @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-black/60"></div>
            </div>
            <div x-show="showConfirmModal"
                 class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: #fee2e2; color: var(--dash-danger);">
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
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
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
                            style="background-color: var(--dash-danger);"
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
</div>
@endsection
