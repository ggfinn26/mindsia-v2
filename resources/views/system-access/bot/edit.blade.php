@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Edit Bot</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="#" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium mb-1">Nama Bot <span class="text-red-500">*</span></label>
                <input name="name" type="text" class="form-input w-full" required />
            </div>
            <div class="mt-8 flex justify-end">
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
