@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Kirim Notifikasi Manual</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="#" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium mb-1">Kirim Ke (Penerima) <span class="text-red-500">*</span></label>
                <select name="recipient_type" class="form-select w-full" required>
                    <option value="">Pilih Target...</option>
                    <option value="all_employee">Semua Pegawai</option>
                    <option value="all_member">Semua Member</option>
                    <option value="specific_branch">Pegawai di Cabang Tertentu</option>
                    <option value="specific_users">Pilih Pengguna Tertentu</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Gunakan Template (Opsional)</label>
                <select name="template_id" class="form-select w-full">
                    <option value="">Tulis Manual Tanpa Template</option>
                </select>
            </div>

            <hr class="my-4 border-gray-200">

            <div>
                <label class="block text-sm font-medium mb-1">Channel Pengiriman <span class="text-red-500">*</span></label>
                <div class="flex flex-wrap gap-4 mt-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="channels[]" value="in-app" class="form-checkbox text-indigo-500" checked>
                        <span class="text-sm ml-2">In-App</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="channels[]" value="email" class="form-checkbox text-indigo-500">
                        <span class="text-sm ml-2">Email</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="channels[]" value="whatsapp" class="form-checkbox text-indigo-500">
                        <span class="text-sm ml-2">WhatsApp / Telegram</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Subject (Jika Email)</label>
                <input name="subject" type="text" class="form-input w-full" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Isi Pesan <span class="text-red-500">*</span></label>
                <textarea name="content" rows="6" class="form-textarea w-full" required></textarea>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white flex items-center">
                    <span class="material-symbols-outlined mr-2">send</span> Kirim Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
