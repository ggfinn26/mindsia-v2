@extends('layouts.dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{
    showConfirmModal: false,
    confirmTitle: '',
    confirmMessage: '',
    confirmActionUrl: '',
    confirmMethod: 'POST',
    confirmActionText: 'Konfirmasi'
}">
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-[color:var(--dash-text-muted)]">
                @if(request('tab') == 'assign')
                    Manajemen
                @elseif(request('tab') == 'results')
                    Laporan
                @else
                    Konfigurasi
                @endif
            </p>
            <h1 class="font-jakarta text-2xl font-bold tracking-tight text-[color:var(--dash-text-heading)]">
                @if(request('tab') == 'assign')
                    Assign Survey
                @elseif(request('tab') == 'results')
                    Hasil Survey
                @else
                    Template Survey
                @endif
            </h1>
        </div>

        <div class="flex items-center gap-2">
            @if(!request()->has('tab'))
                @can('survey.form.create')
                <a href="{{ route('surveys.create') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-lg bg-[var(--dash-primary,#215aac)] px-5 text-sm font-bold text-white shadow-sm hover:bg-[#194a91] transition-colors">
                    <span class="material-symbols-outlined text-[18px]">add</span> Tambah Survey
                </a>
                @endcan
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden rounded-xl border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[color:var(--dash-card-border)] text-sm">
                <thead class="bg-[var(--dash-nav-hover-bg)] text-left text-xs font-bold uppercase tracking-wider text-[color:var(--dash-text-muted)]">
                    <tr>
                        <th class="px-6 py-4">Nama Survey</th>
                        <th class="px-6 py-4">Batas Waktu</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($surveys as $survey)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-[color:var(--dash-text-heading)]">
                                {{ $survey->survey_name }}
                            </td>
                            <td class="px-6 py-4 text-[color:var(--dash-text-body)] font-medium">
                                @if ($survey->deadline_at)
                                    <span class="inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px] text-[color:var(--dash-text-faint)]">calendar_month</span> {{ $survey->deadline_at->format('d M Y H:i') }}</span>
                                @else
                                    <span class="text-[color:var(--dash-text-faint)] italic">Tanpa Batas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($survey->isExpired())
                                    <span class="inline-flex items-center gap-1 rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-wide bg-red-100 text-red-800">Berakhir</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-md px-2.5 py-1 text-xs font-bold uppercase tracking-wide bg-emerald-100 text-emerald-800">Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center justify-end gap-3">
                                    @if(request('tab') == 'assign')
                                        <a href="{{ route('surveys.show', $survey->id) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">Assign Member/Pegawai</a>
                                    @elseif(request('tab') == 'results')
                                        @can('survey.result.view')
                                            <a href="{{ route('surveys.results', $survey->id) }}" class="text-sm font-semibold text-purple-600 hover:text-purple-800 transition-colors">Lihat Hasil</a>
                                        @endcan
                                    @else
                                        <a href="{{ route('surveys.show', $survey->id) }}" class="text-sm font-semibold text-[color:var(--dash-primary-text,#215aac)] hover:text-[#194a91] transition-colors">Detail</a>
                                        @can('survey.form.update')
                                            <a href="{{ route('surveys.edit', $survey->id) }}" class="text-sm font-semibold text-amber-600 hover:text-amber-800 transition-colors">Edit</a>
                                        @endcan
                                        @can('survey.form.delete')
                                            <button type="button" @click="showConfirmModal = true; confirmTitle = 'Hapus Survey'; confirmMessage = 'Yakin ingin menghapus survey ini?'; confirmActionUrl = '{{ route('surveys.destroy', $survey->id) }}'; confirmMethod = 'DELETE'; confirmActionText = 'Hapus'" class="text-sm font-semibold text-red-600 hover:text-red-800 transition-colors">Hapus</button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-[var(--dash-nav-hover-bg)] mb-3">
                                    <span class="material-symbols-outlined text-[color:var(--dash-text-faint)] text-2xl">fact_check</span>
                                </div>
                                <h3 class="text-sm font-medium text-[color:var(--dash-text-heading)]">Belum ada data survey</h3>
                                <p class="mt-1 text-sm text-[color:var(--dash-text-muted)]">
                                    @if(request('tab') == 'assign') Belum ada survey yang bisa di-assign. @elseif(request('tab') == 'results') Belum ada hasil survey yang tersedia. @else Silakan buat template survey baru. @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showConfirmModal = false">
                <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
            </div>
            
            <!-- Modal panel -->
            <div class="relative inline-block align-bottom bg-[var(--dash-card-bg)] rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-[color:var(--dash-card-border)]">
                <div class="bg-[var(--dash-card-bg)] px-6 pt-6 pb-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-50 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="font-jakarta text-lg font-bold text-[color:var(--dash-text-heading)]" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-[color:var(--dash-text-muted)]" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-[var(--dash-nav-hover-bg)] px-6 py-4 sm:flex sm:flex-row-reverse gap-3 border-t border-[color:var(--dash-card-border)]">
                    <button type="button" @click="$refs.confirmForm.submit()" class="w-full inline-flex justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-red-700 transition-colors sm:w-auto" x-text="confirmActionText"></button>
                    <button type="button" @click="showConfirmModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-[color:var(--dash-card-border)] bg-[var(--dash-card-bg)] px-5 py-2.5 text-sm font-bold text-[color:var(--dash-text-body)] shadow-sm hover:bg-[var(--dash-nav-hover-bg)] transition-colors sm:mt-0 sm:w-auto">Batal</button>
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
