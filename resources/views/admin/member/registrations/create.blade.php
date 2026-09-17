@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Registrasi Member Baru</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="#" method="POST" class="p-6 space-y-4">
            @csrf
            
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Informasi Dasar</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input name="name" type="text" class="form-input w-full" required />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
                    <input name="email" type="email" class="form-input w-full" required />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Telepon / WhatsApp <span class="text-red-500">*</span></label>
                    <input name="phone" type="text" class="form-input w-full" required />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Instansi / Asal Sekolah</label>
                    <input name="institution" type="text" class="form-input w-full" />
                </div>
            </div>

            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 mt-6">Pemilihan Program</h2>
            
            <div>
                <label class="block text-sm font-medium mb-1">Pilih Program / Course <span class="text-red-500">*</span></label>
                <select name="program_id" class="form-select w-full" required>
                    <option value="">-- Pilih Program --</option>
                </select>
            </div>

            <div class="mt-8 flex justify-end">
                <a href="#" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 mr-3">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Daftarkan Member</button>
            </div>
        </form>
    </div>
</div>
@endsection
