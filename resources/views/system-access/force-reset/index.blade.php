@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-red-600 font-bold">Force Reset Passwords</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-red-200 p-6">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-800">Cari Pengguna</h2>
            <p class="text-sm text-gray-500">Gunakan fitur ini dengan hati-hati. Fitur ini akan memaksa pengguna untuk mereset password mereka.</p>
        </div>
        
        <form action="#" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Email / Username Pengguna</label>
                <div class="flex gap-2">
                    <input name="search" type="text" class="form-input flex-1" placeholder="Masukkan email atau username" />
                    <button type="button" class="btn bg-gray-200 hover:bg-gray-300 text-gray-700">Cari</button>
                </div>
            </div>
            
            <!-- Result placeholder -->
            <div class="hidden border border-gray-200 p-4 rounded bg-gray-50 mt-4">
                <p class="font-medium text-gray-800">User ditemukan: <span class="text-indigo-600">John Doe (john@example.com)</span></p>
                <div class="mt-4 flex gap-3">
                    <button type="submit" class="btn bg-red-500 hover:bg-red-600 text-white">Reset Password (Kirim Link via Email)</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
