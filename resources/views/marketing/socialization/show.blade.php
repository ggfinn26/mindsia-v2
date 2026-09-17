@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Sosialisasi</h1>
        <a href="{{ route('marketing.socialization.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Institusi / Sekolah Tujuan</h3>
                <p class="text-gray-900 font-medium text-lg">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">PIC Marketing</h3>
                <p class="text-gray-900 font-medium">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Tanggal Rencana</h3>
                <p class="text-gray-900 font-medium">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</h3>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Scheduled</span>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Laporan Hasil</h2>
            <form action="#" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Catatan / Laporan Pelaksanaan</label>
                        <textarea name="report_notes" rows="4" class="form-textarea w-full"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Jumlah Lead Didapat</label>
                        <input name="leads_generated" type="number" class="form-input w-full md:w-1/3" />
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="btn bg-green-500 hover:bg-green-600 text-white">Simpan Laporan & Tandai Selesai</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
