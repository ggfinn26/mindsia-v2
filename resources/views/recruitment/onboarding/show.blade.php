@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Review Onboarding</h1>
        <a href="{{ route('recruitment.onboarding.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-4">Informasi Kandidat</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Kandidat</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Posisi Diterima</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Checklist Dokumen Onboarding</h2>
            <div class="space-y-3">
                <!-- Checklist Items Placeholder -->
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox text-indigo-500" disabled checked>
                    <span class="text-sm ml-2">Foto KTP / Identitas Diri</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox text-indigo-500" disabled checked>
                    <span class="text-sm ml-2">Kartu Keluarga (KK)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox text-indigo-500" disabled>
                    <span class="text-sm ml-2">Buku Rekening Bank</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox text-indigo-500" disabled>
                    <span class="text-sm ml-2">Tanda Tangan Kontrak</span>
                </label>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end gap-3">
            <button class="btn bg-green-500 hover:bg-green-600 text-white">Selesaikan Onboarding (Buat Akun Pegawai)</button>
        </div>
    </div>
</div>
@endsection
