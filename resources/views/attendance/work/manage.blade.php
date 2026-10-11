@extends('layouts.dashboard')

@section('title', 'Verifikasi & Koreksi - MINDSIA')
@section('header_title', 'Verifikasi & Koreksi')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 space-y-6"
     x-data="{
         showAdjustModal: false,
         adjustId: null,
         adjustName: '',
         adjustCheckIn: '',
         adjustCheckOut: '',
         adjustStatus: '',
         adjustLateMinutes: 0,
         adjustReason: '',
         showVerifyModal: false,
         verifyUrl: '',
         verifyName: '',
         openAdjustModal(item) {
             this.adjustId = item.id;
             this.adjustName = item.name;
             this.adjustCheckIn = item.checkIn || '';
             this.adjustCheckOut = item.checkOut || '';
             this.adjustStatus = item.status || '';
             this.adjustLateMinutes = item.lateMinutes || 0;
             this.adjustReason = '';
             this.showAdjustModal = true;
         }
     }">

    {{-- Page Header (Pola A) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Presensi</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Verifikasi & Koreksi</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Verifikasi kehadiran harian pegawai dan lakukan koreksi presensi jika diperlukan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('attendance-recaps.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors shrink-0"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                <span class="material-symbols-outlined text-[18px]">table_chart</span>
                Rekap Absensi Pegawai
            </a>
        </div>
    </div>

    {{-- Alerts / Flash Messages --}}
    @if (session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: var(--dash-status-success-bg); border-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: var(--dash-status-danger-bg); border-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text);">
        <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">error</span>
        <div>
            <p class="font-semibold">{{ $errors->first() }}</p>
        </div>
    </div>
    @endif

    {{-- Filter Card --}}
    <div class="rounded-2xl p-5"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:w-auto min-w-[220px]">
                <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                       style="color: var(--dash-text-muted);">Tanggal Presensi</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="w-full px-3.5 py-2 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 1px var(--dash-primary)';"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none';">
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">search</span>
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b flex items-center justify-between"
             style="border-color: var(--dash-card-border);">
            <div class="flex items-center gap-2.5">
                <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                <h2 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">
                    Daftar Kehadiran Pegawai
                </h2>
                <span class="text-xs ml-1" style="color: var(--dash-text-muted);">
                    ({{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }})
                </span>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full"
                  style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border: 1px solid var(--dash-card-border);">
                Total: {{ count($logs) }} Data
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Pegawai</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Check In</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Check Out</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Status</th>
                        <th class="px-6 py-3.5 text-center text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Diverifikasi</th>
                        <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wider"
                            style="color: var(--dash-text-muted);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr style="border-bottom: 1px solid var(--dash-card-border);"
                        class="transition-colors"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-sm" style="color: var(--dash-text-heading);">
                                {{ $log->employee->full_name ?? '-' }}
                            </div>
                            <div class="text-xs font-mono mt-0.5" style="color: var(--dash-text-faint);">
                                {{ $log->employee->employee_code ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-mono"
                            style="color: var(--dash-text-body);">
                            {{ $log->check_in ? $log->check_in->format('H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-mono"
                            style="color: var(--dash-text-body);">
                            {{ $log->check_out ? $log->check_out->format('H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $statusStyles = match($log->status) {
                                    'present'    => ['bg' => 'var(--dash-status-success-bg)', 'text' => 'var(--dash-status-success-text)', 'label' => 'Hadir'],
                                    'checked_in' => ['bg' => 'var(--dash-status-info-bg)',    'text' => 'var(--dash-status-info-text)',    'label' => 'Sedang Hadir'],
                                    'late'       => ['bg' => 'var(--dash-status-warning-bg)', 'text' => 'var(--dash-status-warning-text)', 'label' => 'Terlambat'],
                                    'sick'       => ['bg' => 'var(--dash-status-warning-bg)', 'text' => 'var(--dash-status-warning-text)', 'label' => 'Sakit'],
                                    'permission' => ['bg' => 'var(--dash-status-info-bg)',    'text' => 'var(--dash-status-info-text)',    'label' => 'Izin'],
                                    'leave'      => ['bg' => 'var(--dash-primary-bg)',        'text' => 'var(--dash-primary-text)',        'label' => 'Cuti'],
                                    'holiday'    => ['bg' => 'var(--dash-status-default-bg)', 'text' => 'var(--dash-status-default-text)', 'label' => 'Libur'],
                                    'absent'     => ['bg' => 'var(--dash-status-danger-bg)',  'text' => 'var(--dash-status-danger-text)',  'label' => 'Alpha'],
                                    default      => ['bg' => 'var(--dash-status-default-bg)', 'text' => 'var(--dash-status-default-text)', 'label' => ucfirst($log->status ?? '-')],
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background-color: {{ $statusStyles['bg'] }}; color: {{ $statusStyles['text'] }};">
                                {{ $statusStyles['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            @if($log->verified_at)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold"
                                      style="color: var(--dash-status-success-text);">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                    {{ $log->verified_at->format('H:i') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-medium"
                                      style="color: var(--dash-text-faint);">
                                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                                    Belum
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                            @if(!$log->verified_at)
                                <button type="button"
                                        @click="verifyUrl = '{{ route('work-attendance.verify', $log->id) }}'; verifyName = '{{ addslashes($log->employee->full_name ?? '-') }}'; showVerifyModal = true"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-opacity hover:opacity-90"
                                        style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid var(--dash-status-success-bg);">
                                    <span class="material-symbols-outlined text-[16px]">verified</span>
                                    Verifikasi
                                </button>
                            @endif
                            <button type="button"
                                    @click="openAdjustModal({
                                        id: {{ $log->id }},
                                        name: '{{ addslashes($log->employee->full_name ?? '-') }}',
                                        checkIn: '{{ $log->check_in ? $log->check_in->format('H:i') : '' }}',
                                        checkOut: '{{ $log->check_out ? $log->check_out->format('H:i') : '' }}',
                                        status: '{{ $log->status }}',
                                        lateMinutes: {{ (int) $log->late_minutes }}
                                    })"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold rounded-lg transition-opacity hover:opacity-90"
                                    style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text); border: 1px solid var(--dash-primary-bg);">
                                <span class="material-symbols-outlined text-[16px]">edit_note</span>
                                Koreksi
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <span class="material-symbols-outlined text-[44px] block mx-auto mb-2"
                                  style="color: var(--dash-text-faint);">event_busy</span>
                            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">
                                Tidak ada data absensi untuk tanggal ini.
                            </p>
                            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">
                                Silakan pilih tanggal lain menggunakan filter di atas.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Dialog Koreksi Absensi (Alpine.js - Card TANPA overflow-hidden, rounded-t-2xl header, rounded-b-2xl footer) --}}
    <div x-show="showAdjustModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(2px);">
        <div @click.outside="showAdjustModal = false"
             class="w-full max-w-lg rounded-2xl shadow-2xl transition-all"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            {{-- Header: rounded-t-2xl --}}
            <div class="px-6 py-4 rounded-t-2xl border-b flex items-center justify-between"
                 style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">edit_note</span>
                    <div>
                        <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                            Koreksi Data Absensi
                        </h3>
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);" x-text="adjustName"></p>
                    </div>
                </div>
                <button type="button" @click="showAdjustModal = false"
                        class="p-1.5 rounded-lg transition-colors"
                        style="color: var(--dash-text-muted);"
                        onmouseover="this.style.backgroundColor='var(--dash-card-border)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            {{-- Form Body --}}
            <form :action="'{{ url('work-attendance') }}/' + adjustId + '/adjust'" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                               style="color: var(--dash-text-muted);">Status Presensi</label>
                        <select name="status" x-model="adjustStatus" data-no-enhance
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 1px var(--dash-primary)';"
                                onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none';">
                            <option value="">Pilih ...</option>
                            <option value="present">Hadir</option>
                            <option value="checked_in">Sedang Hadir</option>
                            <option value="sick">Sakit</option>
                            <option value="permission">Izin</option>
                            <option value="leave">Cuti</option>
                            <option value="holiday">Libur</option>
                            <option value="absent">Alpha</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                                   style="color: var(--dash-text-muted);">Check In</label>
                            <input type="time" name="check_in" x-model="adjustCheckIn"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 1px var(--dash-primary)';"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none';">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                                   style="color: var(--dash-text-muted);">Check Out</label>
                            <input type="time" name="check_out" x-model="adjustCheckOut"
                                   class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 1px var(--dash-primary)';"
                                   onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none';">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                               style="color: var(--dash-text-muted);">Menit Terlambat</label>
                        <input type="number" min="0" name="late_minutes" x-model="adjustLateMinutes"
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 1px var(--dash-primary)';"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none';">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5"
                               style="color: var(--dash-text-muted);">Catatan Koreksi (Wajib)</label>
                        <textarea name="adjustment_reason" x-model="adjustReason" required rows="3"
                                  placeholder="Tuliskan alasan koreksi presensi..."
                                  class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                  style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                  onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 1px var(--dash-primary)';"
                                  onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none';"></textarea>
                    </div>
                </div>

                {{-- Footer: rounded-b-2xl --}}
                <div class="px-6 py-4 rounded-b-2xl border-t flex items-center justify-end gap-3"
                     style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                    <button type="button" @click="showAdjustModal = false"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">check</span>
                        Simpan Koreksi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Dialog Konfirmasi Verifikasi (Alpine.js - Card TANPA overflow-hidden, rounded-t-2xl header, rounded-b-2xl footer) --}}
    <div x-show="showVerifyModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(2px);">
        <div @click.outside="showVerifyModal = false"
             class="w-full max-w-md rounded-2xl shadow-2xl transition-all"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            {{-- Header: rounded-t-2xl --}}
            <div class="px-6 py-4 rounded-t-2xl border-b flex items-center justify-between"
                 style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-status-success-text);">verified</span>
                    <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">
                        Verifikasi Kehadiran
                    </h3>
                </div>
                <button type="button" @click="showVerifyModal = false"
                        class="p-1.5 rounded-lg transition-colors"
                        style="color: var(--dash-text-muted);"
                        onmouseover="this.style.backgroundColor='var(--dash-card-border)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>

            {{-- Form Body --}}
            <form :action="verifyUrl" method="POST">
                @csrf
                <div class="p-6">
                    <p class="text-sm" style="color: var(--dash-text-body);">
                        Apakah Anda yakin ingin memverifikasi kehadiran untuk pegawai
                        <strong style="color: var(--dash-text-heading);" x-text="verifyName"></strong>?
                    </p>
                </div>

                {{-- Footer: rounded-b-2xl --}}
                <div class="px-6 py-4 rounded-b-2xl border-t flex items-center justify-end gap-3"
                     style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                    <button type="button" @click="showVerifyModal = false"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white transition-opacity hover:opacity-90"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
