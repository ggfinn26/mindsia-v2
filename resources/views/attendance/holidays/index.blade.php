@extends('layouts.dashboard')

@section('title', 'Hari Libur')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8"
     x-data="{ showConfirmModal: false, confirmActionUrl: '' }">

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-sm mb-4">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Hari Libur</h1>
            <p class="text-sm text-[var(--dash-text-muted)] mt-0.5">Hari libur nasional dan khusus yang mempengaruhi perhitungan hari kerja.</p>
        </div>
        @can('attendance.holiday.manage')
            <a href="{{ route('holidays.create') }}"
               class="inline-flex items-center gap-2 bg-[var(--dash-primary)] hover:opacity-90 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Tambah Hari Libur
            </a>
        @endcan
    </div>

    <div class="bg-[var(--dash-card-bg)] shadow rounded-lg overflow-hidden">
        @if ($holidays->isEmpty())
            <div class="text-center py-12 text-[var(--dash-text-faint)]">
                <span class="material-symbols-outlined text-[48px] mb-2 block">event_busy</span>
                <p class="font-medium">Belum ada hari libur terdaftar.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--dash-card-border)] text-sm">
                    <thead class="bg-[var(--dash-page-bg)]">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">Nama Hari Libur</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">Tanggal Mulai</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">Tanggal Selesai</th>
                            <th class="px-5 py-3 text-left text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">Durasi</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-[var(--dash-text-muted)] uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-[var(--dash-card-bg)] divide-y divide-[var(--dash-card-border)]">
                        @foreach ($holidays as $holiday)
                            <tr class="hover:bg-[var(--dash-page-bg)]">
                                <td class="px-5 py-3.5 font-medium text-[var(--dash-text-heading)]">{{ $holiday->holiday_name }}</td>
                                <td class="px-5 py-3.5 text-[var(--dash-text-body)]">{{ $holiday->holiday_start_date->format('d M Y') }}</td>
                                <td class="px-5 py-3.5 text-[var(--dash-text-body)]">{{ $holiday->holiday_end_date->format('d M Y') }}</td>
                                <td class="px-5 py-3.5 text-[var(--dash-text-body)]">
                                    @php $days = $holiday->holiday_start_date->diffInDays($holiday->holiday_end_date) + 1 @endphp
                                    {{ $days }} hari
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('attendance.holiday.manage')
                                            <a href="{{ route('holidays.edit', $holiday) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] text-[color:var(--dash-text-body)] hover:bg-[var(--dash-nav-hover-bg)]"
                                               style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">edit</span>Edit
                                            </a>
                                            <button type="button"
                                                    @click="showConfirmModal = true; confirmActionUrl = '{{ route('holidays.destroy', $holiday) }}'"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                                    style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: #dc2626;"
                                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                                <span class="material-symbols-outlined text-[14px]">delete</span>Hapus
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($holidays->hasPages())
                <div class="px-5 py-4 border-t border-[var(--dash-card-border)]">{{ $holidays->links() }}</div>
            @endif
        @endif
    </div>


    {{-- Confirm Modal --}}
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div @click="showConfirmModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>
            <div class="relative z-10 inline-block align-bottom bg-[var(--dash-card-bg)] rounded-lg text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-[var(--dash-card-bg)] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-[var(--dash-text-heading)]">Hapus Hari Libur</h3>
                            <div class="mt-2">
                                <p class="text-sm text-[var(--dash-text-muted)]">Hapus hari libur ini? Tindakan tidak bisa diundur.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-[var(--dash-page-bg)] px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="$refs.confirmForm.submit()"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-[#dc2626] hover:opacity-90 text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm gap-1.5"><span class="material-symbols-outlined text-[16px]">delete</span>Hapus</button>
                    <button type="button" @click="showConfirmModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-[var(--dash-input-border)] shadow-sm px-4 py-2 bg-[var(--dash-card-bg)] text-base font-medium text-[var(--dash-text-body)] hover:bg-[var(--dash-page-bg)] focus:outline-none sm:mt-0 sm:w-auto sm:text-sm gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
