@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Edit Template Notifikasi</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="#" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium mb-1">Nama Template <span class="text-red-500">*</span></label>
                <input name="name" type="text" class="form-input w-full" required />
            </div>

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
                <a href="{{ route('notification.template.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 mr-3">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
