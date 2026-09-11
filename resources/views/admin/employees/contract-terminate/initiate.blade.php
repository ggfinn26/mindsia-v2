@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Mulai Proses Terminasi Kontrak</h1>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Data Karyawan</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600">Nama</p>
                <p class="font-semibold">{{ $status->employee->full_name }}</p>
            </div>
            <div>
                <p class="text-gray-600">Kode Karyawan</p>
                <p class="font-semibold">{{ $status->employee->employee_code }}</p>
            </div>
            <div>
                <p class="text-gray-600">Posisi</p>
                <p class="font-semibold">{{ $status->position->position_name }}</p>
            </div>
            <div>
                <p class="text-gray-600">Tipe Pekerjaan</p>
                <p class="font-semibold">{{ $status->type_employment }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('contract-terminate.initiate.store', $status) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="mb-6">
            <label for="reason" class="block text-sm font-semibold mb-2">Alasan Terminasi</label>
            <textarea name="reason" id="reason" rows="4" class="w-full border rounded px-3 py-2" required>{{ old('reason') }}</textarea>
            @error('reason')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold mb-4">Daftar Kewajiban yang Harus Diselesaikan</label>
            <div id="checklist-items" class="space-y-3">
                <div class="flex gap-2">
                    <input type="text" name="checklist_items[]" placeholder="Contoh: Pengembalian aset" class="flex-1 border rounded px-3 py-2" required>
                    <button type="button" onclick="removeItem(this)" class="bg-red-500 text-white px-3 py-2 rounded">Hapus</button>
                </div>
            </div>
            @error('checklist_items')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror

            <button type="button" onclick="addItem()" class="mt-3 bg-blue-500 text-white px-4 py-2 rounded">+ Tambah Item</button>
        </div>

        <div class="flex gap-4">
            <a href="{{ route('employees.show', $status->employee) }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Batal</a>
            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Mulai Terminasi</button>
        </div>
    </form>
</div>

<template id="itemTemplate">
    <div class="flex gap-2">
        <input type="text" name="checklist_items[]" placeholder="Contoh: Pengembalian aset" class="flex-1 border rounded px-3 py-2" required>
        <button type="button" onclick="removeItem(this)" class="bg-red-500 text-white px-3 py-2 rounded">Hapus</button>
    </div>
</template>

<script>
function addItem() {
    const container = document.getElementById('checklist-items');
    const clone = document.getElementById('itemTemplate').content.cloneNode(true);
    container.appendChild(clone);
}

function removeItem(btn) {
    btn.parentElement.remove();
}
</script>
@endsection
