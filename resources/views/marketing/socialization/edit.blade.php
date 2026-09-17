@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Edit Jadwal Sosialisasi</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="#" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Institusi / Sekolah Tujuan <span class="text-red-500">*</span></label>
                    <select name="institution_id" class="form-select w-full" required>
                        <option value="">Pilih Institusi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Rencana <span class="text-red-500">*</span></label>
                    <input name="scheduled_date" type="date" class="form-input w-full" required />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Fee Partner / Sekolah (Opsional)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input name="partner_fee" type="number" class="form-input w-full pl-10" placeholder="0" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Tenggat Waktu Fee</label>
                <input name="partner_fee_due_date" type="date" class="form-input w-full" />
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('marketing.socialization.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 mr-3">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
