@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Diskon</h1>
        <a href="{{ route('admin.member.discounts.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Kode Diskon</h3>
                <p class="text-gray-900 font-medium text-lg">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Promo</h3>
                <p class="text-gray-900 font-medium">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Tipe & Nilai Potongan</h3>
                <p class="text-gray-900 font-medium">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Status Kuota</h3>
                <p class="text-gray-900 font-medium">Terpakai: 0 / Total: 0</p>
            </div>
        </div>
    </div>
</div>
@endsection
