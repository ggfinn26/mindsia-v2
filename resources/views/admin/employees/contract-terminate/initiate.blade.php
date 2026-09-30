@extends('layouts.dashboard')

@section('title', 'Terminasi Kontrak — ' . $status->employee->full_name)
@section('header_title', 'Terminasi Kontrak')

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
                <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-red-500 text-[18px]">person_remove</span>
                </div>
                <h2 class="font-bold text-gray-800 font-jakarta">Mulai Terminasi Kontrak</h2>
            </div>
            <a href="{{ route('employees.show', $status->employee) }}" class="text-[13px] font-medium text-gray-500 hover:text-gray-800 flex items-center gap-1 transition-colors">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
            </a>
        </div>

        <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
            <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Karyawan</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $status->employee->full_name }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kode Karyawan</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $status->employee->employee_code }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Posisi</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $status->position->position_name }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tipe Pekerjaan</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $status->type_employment }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('contract-terminate.initiate.store', $status) }}" method="POST" class="px-6 py-5 space-y-6" novalidate>
            @csrf

            <div>
                <label for="reason" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                    Alasan Terminasi <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" id="reason" rows="3" placeholder="Deskripsikan alasan terminasi..."
                          class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-red-500 focus:ring-red-500/20 transition-all">{{ old('reason') }}</textarea>
                @error('reason')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                    Daftar Kewajiban yang Harus Diselesaikan <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-400 mb-3">Minimal satu item wajib diisi. Jika tidak ada kewajiban yang perlu diselesaikan, isikan "Tidak ada kewajiban yang perlu diselesaikan."</p>
                @error('checklist_items')<p class="mb-2 text-xs text-red-600">{{ $message }}</p>@enderror
                
                <div id="checklist-items" class="space-y-3">
                    <div class="flex items-center gap-2 group">
                        <input type="text" name="checklist_items[]" placeholder="Contoh: Pengembalian laptop / ID Card" 
                               class="flex-1 px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20" required>
                        <button type="button" onclick="removeItem(this)" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="button" onclick="addItem()" class="inline-flex items-center gap-1.5 px-4 py-2 text-[12px] font-bold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <span class="material-symbols-outlined text-[16px]">add</span> Tambah Item
                    </button>
                </div>
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('employees.show', $status->employee) }}"
                   class="px-5 py-2.5 text-[13px] font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white px-6 py-2.5 rounded-xl text-[13px] font-bold shadow-sm transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Mulai Terminasi
                </button>
            </div>
        </form>
    </div>
</div>

<template id="itemTemplate">
    <div class="flex items-center gap-2 group">
        <input type="text" name="checklist_items[]" placeholder="Contoh: Pengembalian laptop / ID Card" 
               class="flex-1 px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20" required>
        <button type="button" onclick="removeItem(this)" class="w-10 h-10 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors">
            <span class="material-symbols-outlined text-[20px]">delete</span>
        </button>
    </div>
</template>

<script>
function addItem() {
    const container = document.getElementById('checklist-items');
    const clone = document.getElementById('itemTemplate').content.cloneNode(true);
    container.appendChild(clone);
}

function removeItem(btn) {
    const container = document.getElementById('checklist-items');
    // Don't remove if it's the last item
    if (container.children.length > 1) {
        btn.parentElement.remove();
    } else {
        btn.parentElement.querySelector('input').value = '';
    }
}
</script>
@endsection
