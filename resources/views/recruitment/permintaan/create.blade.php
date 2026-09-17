@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Buat Job Permintaan</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="#" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Posisi / Jabatan <span class="text-red-500">*</span></label>
                <select name="position_id" class="form-select w-full" required>
                    <option value="">Pilih Posisi</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Cabang / Lokasi <span class="text-red-500">*</span></label>
                <select name="branch_id" class="form-select w-full" required>
                    <option value="">Pilih Cabang</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Jumlah Dibutuhkan <span class="text-red-500">*</span></label>
                <input name="quota" type="number" min="1" class="form-input w-full" required />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Target Pemenuhan <span class="text-red-500">*</span></label>
                <input name="target_date" type="date" class="form-input w-full" required />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="4" class="form-textarea w-full"></textarea>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('recruitment.permintaan.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 mr-3">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Buat Permintaan</button>
            </div>
        </form>
    </div>
</div>
@endsection
