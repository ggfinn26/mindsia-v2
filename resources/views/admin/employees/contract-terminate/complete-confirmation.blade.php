@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6 text-red-600">Konfirmasi Hard Delete Akun</h1>

    <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-red-900 mb-4">⚠️ Peringatan Penting</h2>
        <p class="text-red-800 mb-3">Anda akan menghapus akun karyawan secara permanen. Tindakan ini:</p>
        <ul class="list-disc list-inside text-red-800 space-y-2">
            <li>Karyawan tidak bisa login lagi</li>
            <li>Tindakan tidak bisa dibatalkan</li>
            <li>Data karyawan tetap tersimpan untuk audit trail</li>
        </ul>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Data yang Akan Dihapus</h2>
        <dl class="space-y-3">
            <div class="flex justify-between border-b pb-3">
                <dt class="font-semibold">Nama</dt>
                <dd>{{ $status->employee->full_name }}</dd>
            </div>
            <div class="flex justify-between border-b pb-3">
                <dt class="font-semibold">Kode Karyawan</dt>
                <dd>{{ $status->employee->employee_code }}</dd>
            </div>
            <div class="flex justify-between border-b pb-3">
                <dt class="font-semibold">Email</dt>
                <dd>{{ $status->employee->email }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="font-semibold">Status Pekerjaan</dt>
                <dd>{{ $status->type_employment }}</dd>
            </div>
        </dl>
    </div>

    <form action="{{ route('contract-terminate.complete', $status) }}" method="POST" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="mb-6">
            <label class="flex items-start gap-3">
                <input type="checkbox" name="confirm" value="on" required class="w-5 h-5 mt-1">
                <span class="text-sm">
                    Saya memahami bahwa tindakan ini akan menghapus akun {{ $status->employee->full_name }} secara permanen dan tidak dapat dibatalkan. Saya siap melanjutkan.
                </span>
            </label>
            @error('confirm')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
        </div>

        <div class="flex gap-4">
            <a href="{{ route('contract-terminate.checklist.show', $status) }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Batal</a>
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hard Delete Akun</button>
        </div>
    </form>
</div>
@endsection
