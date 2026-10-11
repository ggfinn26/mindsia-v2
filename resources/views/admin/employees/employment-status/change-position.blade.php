@extends('layouts.dashboard')

@section('title', 'Ubah Posisi - ' . $status->employee->full_name)
@section('header_title', 'Ubah Posisi Pegawai')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header (Pola B3) --}}
    <div>
        <a href="{{ route('employment-statuses.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Kelola Kontrak
        </a>
        <p class="text-xs font-bold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">SDM & Karyawan</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Ubah Posisi Pegawai</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Pegawai: <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->full_name }}</span> (Posisi saat ini: {{ $status->position?->position_name ?? 'Belum diatur' }})
        </p>
    </div>

    {{-- Error validation --}}
    @if($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <div class="flex items-center gap-2 font-semibold mb-1">
            <span class="material-symbols-outlined text-[18px]">error</span>
            Ada kesalahan:
        </div>
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">swap_horiz</span>
                <h2 class="font-bold text-sm font-jakarta" style="color: var(--dash-text-heading);">Formulir Perubahan Posisi</h2>
            </div>
        </div>

        <div class="px-6 py-4" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Nama Pegawai</p>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $status->employee->full_name }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider mb-1" style="color: var(--dash-text-muted);">Posisi Saat Ini</p>
                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $status->position?->position_name ?? 'Belum diatur' }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('employment-statuses.change-position.store', $status) }}" method="POST" class="px-6 py-6 space-y-6" novalidate>
            @csrf

            <div>
                <label for="position_id" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Posisi Baru <span class="text-red-500">*</span>
                </label>
                <select id="position_id" name="position_id" required
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Pilih Posisi...</option>
                    @foreach ($positions as $position)
                        @if ($position->id != $status->position_id)
                            <option value="{{ $position->id }}" @selected(old('position_id') == $position->id)>
                                {{ $position->position_name }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label for="contract_start_date" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Tanggal Mulai Kontrak Baru <span class="text-red-500">*</span>
                </label>
                <input type="date" id="contract_start_date" name="contract_start_date" value="{{ old('contract_start_date') }}" required
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                <p class="mt-1 text-xs" style="color: var(--dash-text-muted);">Kontrak lama akan ditutup pada hari sebelum tanggal ini.</p>
            </div>

            <div>
                <label for="contract_end_date" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Tanggal Berakhir Kontrak <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(Opsional)</span>
                </label>
                <input type="date" id="contract_end_date" name="contract_end_date" value="{{ old('contract_end_date') }}"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                    Catatan <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(Opsional)</span>
                </label>
                <textarea id="notes" name="notes" rows="3" placeholder="Alasan perubahan posisi atau keterangan lainnya..."
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3" style="border-top: 1px solid var(--dash-card-border);">
                <a href="{{ route('employment-statuses.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
