@extends('layouts.dashboard')

@section('title', 'Detail Diskon - ' . $discount->discount_code . ' - MINDSIA')
@section('header_title', 'Detail Diskon')

@section('content')
@php
    $usedCount = $discount->registrations->count();
    $remaining = $discount->discount_quota ? max(0, $discount->discount_quota - $usedCount) : null;
    $assignedProgramIds = $discount->programs->pluck('program_id')->toArray();
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6"
     x-data="{
         showEdit: {{ $errors->any() ? 'true' : 'false' }},
         deleteModalOpen: false
     }">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('discounts.index') }}" class="dash-back mb-3 inline-flex items-center gap-1.5 text-xs font-semibold">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Daftar Diskon
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight font-mono" style="color: var(--dash-text-heading);">{{ $discount->discount_code }}</h1>
                    @if ($discount->is_active)
                        <span class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-slate-500 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-full text-xs font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                        </span>
                    @endif
                </div>
            </div>
            @can('member.manage')
            <div class="flex items-center gap-2">
                <form action="{{ route('discounts.toggle-active', $discount) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg transition-colors"
                            style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        <span class="material-symbols-outlined text-[16px]">{{ $discount->is_active ? 'pause_circle' : 'play_circle' }}</span>
                        {{ $discount->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                <button type="button" @click="deleteModalOpen = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                        style="background-color: #dc2626;">
                    <span class="material-symbols-outlined text-[16px]">delete</span>
                    Hapus
                </button>
            </div>
            @endcan
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

    {{-- Card 1: Informasi Diskon + Form Edit --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b flex items-center justify-between"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Informasi Diskon</h2>
            @can('member.manage')
            <button type="button" @click="showEdit = !showEdit"
                    class="inline-flex items-center gap-1 text-xs font-semibold hover:underline"
                    style="color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-[14px]">edit</span>
                <span x-text="showEdit ? 'Tutup Form Edit' : 'Edit Diskon'"></span>
            </button>
            @endcan
        </div>

        <div class="px-6 py-5 grid grid-cols-2 md:grid-cols-4 gap-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Tipe</p>
                <p class="font-medium text-sm" style="color: var(--dash-text-heading);">
                    {{ $discount->discount_type === 'percentage' ? 'Persentase (%)' : 'Fixed Nominal (Rp)' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Nilai Potongan</p>
                <p class="font-bold text-base font-mono" style="color: var(--dash-text-heading);">
                    @if ($discount->discount_type === 'percentage')
                        {{ $discount->discount_nominal }}%
                    @else
                        Rp {{ number_format($discount->discount_nominal, 0, ',', '.') }}
                    @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Kuota Slot</p>
                @if ($discount->discount_quota)
                    <p class="font-semibold text-sm" style="color: var(--dash-text-heading);">{{ $usedCount }} / {{ $discount->discount_quota }} terpakai</p>
                    <div class="w-full rounded-full h-1.5 mt-1.5" style="background-color: var(--dash-nav-hover-bg);">
                        <div class="h-1.5 rounded-full"
                             style="width: {{ min(100, round($usedCount / $discount->discount_quota * 100)) }}%; background-color: {{ $remaining === 0 ? '#dc2626' : 'var(--dash-primary)' }};"></div>
                    </div>
                @else
                    <p class="text-sm" style="color: var(--dash-text-body);">Tidak terbatas</p>
                    @if ($usedCount > 0)
                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $usedCount }} sudah digunakan</p>
                    @endif
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Periode Berlaku</p>
                @if ($discount->starts_at || $discount->expired_at)
                    <p class="text-xs" style="color: var(--dash-text-body);">
                        {{ $discount->starts_at?->format('d M Y') ?? '-' }}
                        <span class="mx-1" style="color: var(--dash-text-faint);">s/d</span>
                        {{ $discount->expired_at?->format('d M Y') ?? '-' }}
                    </p>
                @else
                    <p class="text-sm" style="color: var(--dash-text-muted);">Tidak ada batas waktu</p>
                @endif
            </div>
        </div>

        @can('member.manage')
        <div x-show="showEdit" x-cloak class="px-6 pb-6 pt-4 border-t" style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-jakarta text-sm font-bold mb-4" style="color: var(--dash-text-heading);">Edit Parameter Diskon</h3>
            @if ($errors->any())
                <div class="rounded-xl p-3 text-xs mb-4"
                     style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('discounts.update', $discount) }}" method="POST" class="space-y-4" novalidate>
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                            Kode Diskon <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="discount_code"
                               value="{{ old('discount_code', $discount->discount_code) }}"
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all uppercase font-mono"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                            Tipe Diskon <span class="text-red-500">*</span>
                        </label>
                        <select name="discount_type" data-no-enhance
                                class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <option value="percentage" {{ old('discount_type', $discount->discount_type) === 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="fixed_amount" {{ old('discount_type', $discount->discount_type) === 'fixed_amount' ? 'selected' : '' }}>Fixed (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                            Nilai Potongan <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="discount_nominal" min="0"
                               value="{{ old('discount_nominal', $discount->discount_nominal) }}"
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all font-mono"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">Kuota Slot</label>
                        <input type="number" name="discount_quota" min="0"
                               value="{{ old('discount_quota', $discount->discount_quota) }}"
                               placeholder="Kosongkan jika tidak terbatas"
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all font-mono"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">Berlaku Mulai</label>
                        <input type="date" name="starts_at"
                               value="{{ old('starts_at', $discount->starts_at?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">Berlaku Sampai</label>
                        <input type="date" name="expired_at"
                               value="{{ old('expired_at', $discount->expired_at?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3">
                    <button type="button" @click="showEdit = false"
                            class="px-4 py-2 text-xs font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                            style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[16px]">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        @endcan
    </div>

    {{-- Card 2: Eligibilitas Program --}}
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Eligibilitas Program</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                Tentukan program mana saja yang berhak menggunakan voucher diskon ini. Jika tidak ada yang dipilih, diskon berlaku secara global.
            </p>
        </div>
        <div class="px-6 py-5">
            @if ($programs->isEmpty())
                <p class="text-xs" style="color: var(--dash-text-muted);">Belum ada program aktif terdaftar.</p>
            @else
                @can('member.manage')
                <form action="{{ route('discounts.programs.sync', $discount) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="space-y-2">
                        @foreach ($programs as $program)
                        <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-colors"
                               style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg);"
                               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            <input type="checkbox" name="program_ids[]" value="{{ $program->id }}"
                                   {{ in_array($program->id, $assignedProgramIds) ? 'checked' : '' }}
                                   class="rounded focus:ring-0"
                                   style="accent-color: var(--dash-primary);" />
                            <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $program->program_name }}</span>
                            <span class="text-xs font-mono ml-auto" style="color: var(--dash-text-faint);">{{ $program->program_code }}</span>
                        </label>
                        @endforeach
                    </div>
                    <div class="mt-5 flex items-center justify-between pt-3 border-t" style="border-color: var(--dash-card-border);">
                        <p class="text-xs" style="color: var(--dash-text-muted);">
                            @if (count($assignedProgramIds) === 0)
                                Status: Berlaku untuk semua program
                            @else
                                Status: {{ count($assignedProgramIds) }} program dipilih
                            @endif
                        </p>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                            Simpan Eligibilitas
                        </button>
                    </div>
                </form>
                @else
                <div class="space-y-2">
                    @foreach ($programs as $program)
                    <div class="flex items-center gap-3 p-3 rounded-xl border"
                         style="border-color: var(--dash-card-border); background-color: var(--dash-card-bg);">
                        <span class="flex items-center justify-center">
                            @if (in_array($program->id, $assignedProgramIds))
                                <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                            @else
                                <span class="material-symbols-outlined text-[18px]" style="color: var(--dash-text-faint);">radio_button_unchecked</span>
                            @endif
                        </span>
                        <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $program->program_name }}</span>
                        <span class="text-xs font-mono ml-auto" style="color: var(--dash-text-faint);">{{ $program->program_code }}</span>
                    </div>
                    @endforeach
                </div>
                @endcan
            @endif
        </div>
    </div>

    {{-- Card 3: Riwayat Penggunaan --}}
    @if ($discount->registrations->count() > 0)
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-sm font-bold uppercase tracking-wide" style="color: var(--dash-text-heading);">Riwayat Penggunaan</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $discount->registrations->count() }} registrasi telah menggunakan diskon ini</p>
        </div>
        <table class="w-full text-sm text-left">
            <thead>
                <tr style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold" style="color: var(--dash-text-muted);">Member & No. Registrasi</th>
                    <th class="px-5 py-3.5 uppercase tracking-wider text-[11px] font-bold text-right" style="color: var(--dash-text-muted);">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($discount->registrations->take(20) as $reg)
                <tr style="border-bottom: 1px solid var(--dash-card-border);"
                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <td class="px-5 py-3.5">
                        <p class="font-medium text-sm" style="color: var(--dash-text-heading);">{{ $reg->memberData?->full_name ?? '-' }}</p>
                        <p class="font-mono text-xs mt-0.5" style="color: var(--dash-text-muted);">{{ $reg->registration_number ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-3.5 text-xs text-right whitespace-nowrap" style="color: var(--dash-text-muted);">{{ $reg->created_at?->format('d M Y') ?? '-' }}</td>
                </tr>
                @endforeach
                    @if ($discount->registrations->count() > 20)
                    <tr>
                        <td colspan="3" class="px-5 py-3 text-center text-xs" style="color: var(--dash-text-faint);">
                            + {{ $discount->registrations->count() - 20 }} registrasi lainnya
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Modal Konfirmasi Hapus (Custom Alpine) --}}
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div @click="deleteModalOpen = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            </div>
            <div class="relative z-10 inline-block align-bottom rounded-2xl text-left shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-6 rounded-t-2xl">
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-11 w-11 rounded-full sm:mx-0"
                             style="background-color: #fee2e2; color: #dc2626;">
                            <span class="material-symbols-outlined text-[24px]">delete</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="font-jakarta text-lg font-bold" style="color: var(--dash-text-heading);">Hapus Diskon</h3>
                            <div class="mt-2 text-sm" style="color: var(--dash-text-muted);">
                                Yakin ingin menghapus diskon <span class="font-mono font-bold" style="color: var(--dash-text-heading);">{{ $discount->discount_code }}</span>?
                                Diskon yang sudah dipakai dalam registrasi tidak dapat dihapus.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 rounded-b-2xl flex flex-col-reverse sm:flex-row sm:justify-end gap-2"
                     style="background-color: var(--dash-nav-hover-bg); border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="deleteModalOpen = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <form action="{{ route('discounts.destroy', $discount) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                                style="background-color: #dc2626;">
                            Hapus Diskon
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
