@extends('layouts.dashboard')

@section('title', 'Kelola Sanksi Absen - MINDSIA')
@section('header_title', 'Kelola Sanksi Absen')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto space-y-6"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '' }">

    {{-- Page Header Pola A --}}
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Presensi</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Kelola Sanksi Absen</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Konfigurasi aturan dan eskalasi tindakan otomatis atas pelanggaran kehadiran.
            </p>
        </div>
        <div>
            <a href="{{ route('attendance-rules.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white shadow-sm transition-opacity hover:opacity-90 shrink-0"
               style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Tambah Aturan
            </a>
        </div>
    </div>

    {{-- Flash Notifications --}}
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

    @if ($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #fef2f2; border-color: #fecaca; color: var(--dash-danger);">
            <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
            <div class="flex-1">
                <p class="font-bold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Card Container --}}
    <div class="rounded-2xl border border-[var(--dash-card-border)] bg-[var(--dash-card-bg)] shadow-sm">
        <div class="px-6 py-4 border-b flex items-center justify-between"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2.5">
                <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Daftar Aturan Sanksi</h3>
                <span class="text-xs ml-1" style="color: var(--dash-text-muted);">Aturan pemicu dan urutan tindakan sanksi absensi</span>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="background-color: var(--dash-card-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">
                Total: {{ $rules->total() }} Aturan
            </span>
        </div>

        @if ($rules->isEmpty())
            <div class="text-center py-12 px-4">
                <span class="material-symbols-outlined text-[48px] block mx-auto mb-3"
                      style="color: var(--dash-text-faint);">gavel</span>
                <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada aturan sanksi absen terdaftar.</p>
                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Klik tombol Tambah Aturan untuk membuat aturan baru.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Nama Aturan</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Tipe Absensi</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Pemicu</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Aksi Sanksi</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold"
                                style="color: var(--dash-text-muted);">Status</th>
                            <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right"
                                style="color: var(--dash-text-muted);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" style="border-color: var(--dash-card-border);">
                        @php
                            $triggerLabels = [
                                'consecutive_absence' => 'Absen Beruntun',
                                'monthly_absence' => 'Absen Bulanan',
                                'monthly_late_count' => 'Telat (hitungan)',
                                'monthly_late_minutes' => 'Telat (menit)',
                                'daily_late' => 'Telat Harian',
                            ];
                            $actionLabels = [
                                'notification' => 'Notifikasi',
                                'warning_letter' => 'Surat Peringatan',
                                'payroll_deduction' => 'Potongan Gaji',
                                'mark_anomaly' => 'Tandai Anomali',
                                'create_follow_up' => 'Buat Tindak Lanjut',
                            ];
                        @endphp
                        @foreach ($rules as $rule)
                            <tr style="border-bottom: 1px solid var(--dash-card-border);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td class="px-5 py-3.5 font-medium" style="color: var(--dash-text-heading);">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-primary);">policy</span>
                                        <span class="font-semibold">{{ $rule->rule_name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm" style="color: var(--dash-text-body);">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium"
                                          style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-heading);">
                                        {{ $rule->attendance_type === 'work_schedule' ? 'Kerja Harian' : 'Sesi/Kelas' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-sm" style="color: var(--dash-text-body);">
                                    <div class="flex flex-col gap-0.5">
                                        <span class="font-medium" style="color: var(--dash-text-heading);">
                                            {{ $triggerLabels[$rule->trigger_type] ?? $rule->trigger_type }}
                                        </span>
                                        <span class="text-xs" style="color: var(--dash-text-muted);">
                                            Syarat: {{ $rule->trigger_operator }} {{ $rule->trigger_value }} ({{ $rule->period_type === 'daily' ? 'Harian' : 'Bulanan' }})
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-sm" style="color: var(--dash-text-body);">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse ($rule->actions->sortBy('action_order') as $action)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium"
                                                  style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text); border: 1px solid var(--dash-card-border);">
                                                <span class="font-bold">{{ $action->action_order }}.</span>
                                                {{ $actionLabels[$action->action_type] ?? $action->action_type }}
                                            </span>
                                        @empty
                                            <span class="text-xs" style="color: var(--dash-text-faint);">Tidak ada aksi</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-sm">
                                    @if ($rule->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: #f0fdf4; color: #059669;">
                                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: #059669;"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background-color: #fef2f2; color: var(--dash-danger);">
                                            <span class="w-1.5 h-1.5 rounded-full" style="background-color: var(--dash-danger);"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <a href="{{ route('attendance-rules.edit', $rule->id) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                                           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                            <span class="material-symbols-outlined text-[14px]">edit</span>
                                            Edit
                                        </a>
                                        <button type="button"
                                                @click="showConfirmModal = true; confirmTitle = 'Hapus Aturan Sanksi'; confirmMessage = 'Yakin ingin menghapus aturan \'{{ addslashes($rule->rule_name) }}\'? Tindakan ini tidak dapat dibatalkan.'; confirmActionUrl = '{{ route('attendance-rules.destroy', $rule->id) }}'"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                                style="background-color: var(--dash-danger);">
                                            <span class="material-symbols-outlined text-[14px]">delete</span>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($rules->hasPages())
                <div class="px-5 py-4" style="border-top: 1px solid var(--dash-card-border);">
                    {{ $rules->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Modal Konfirmasi Hapus (Alpine.js Custom Modal tanpa overflow-hidden) --}}
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
                            style="background-color: var(--dash-danger);">
                        Hapus
                    </button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
