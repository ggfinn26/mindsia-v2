@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Buat Periode Payroll Baru</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="{{ route('admin.payroll.periods.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium mb-1">Nama Periode <span class="text-red-500">*</span></label>
                <input name="name" type="text" class="form-input w-full" placeholder="Misal: Januari 2024" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input name="start_date" type="date" class="form-input w-full" required />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Akhir <span class="text-red-500">*</span></label>
                    <input name="end_date" type="date" class="form-input w-full" required />
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Bulan</label>
                <input name="month" type="number" min="1" max="12" class="form-input w-full" />
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-1">Tahun</label>
                <input name="year" type="number" min="2000" max="2099" class="form-input w-full" />
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('admin.payroll.periods.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 mr-3">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Simpan Periode</button>
            </div>
        </form>
    </div>
</div>
@endsection
