@extends('layouts.dashboard')

@section('title', 'Ubah Posisi — ' . $status->employee->full_name)
@section('header_title', 'Ubah Posisi Karyawan')

@section('content')
<div class="max-w-2xl mx-auto">

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 mb-6 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#5586DB] text-[20px]">swap_horiz</span>
                <h2 class="font-bold text-gray-800 font-jakarta">Form Ubah Posisi</h2>
            </div>
            <a href="{{ route('employees.show', $status->employee) }}" class="text-[13px] font-medium text-gray-500 hover:text-gray-800 flex items-center gap-1 transition-colors">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
            </a>
        </div>

        <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Karyawan</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $status->employee->full_name }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Posisi Saat Ini</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $status->position->position_name }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('employment-statuses.change-position.store', $status) }}" method="POST" class="px-6 py-5 space-y-5" novalidate>
            @csrf

            <div>
                <label for="position_id" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                    Posisi Baru
                </label>
                <select id="position_id" name="position_id" required
                        class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                    <option value="">-- Pilih Posisi --</option>
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
                <label for="contract_start_date" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                    Tanggal Mulai Kontrak Baru
                </label>
                <input type="date" id="contract_start_date" name="contract_start_date" value="{{ old('contract_start_date') }}"
                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                <p class="mt-1 text-[11px] text-gray-400">Kontrak lama akan ditutup pada hari sebelum tanggal ini.</p>
            </div>

            <div>
                <label for="contract_end_date" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                    Tanggal Berakhir Kontrak <span class="font-normal text-gray-400 normal-case tracking-normal">(Opsional)</span>
                </label>
                <input type="date" id="contract_end_date" name="contract_end_date" value="{{ old('contract_end_date') }}"
                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
            </div>

            <div>
                <label for="notes" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                    Catatan <span class="font-normal text-gray-400 normal-case tracking-normal">(Opsional)</span>
                </label>
                <textarea id="notes" name="notes" rows="3" placeholder="Alasan perubahan posisi atau keterangan lainnya..."
                          class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">{{ old('notes') }}</textarea>
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('employees.show', $status->employee) }}"
                   class="px-5 py-2.5 text-[13px] font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="bg-[#5586DB] hover:bg-[#4A78C4] text-white px-6 py-2.5 rounded-xl text-[13px] font-bold shadow-sm transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
