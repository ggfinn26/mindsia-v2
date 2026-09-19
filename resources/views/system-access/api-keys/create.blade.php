@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Generate API Key</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="#" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium mb-1">Nama / Deskripsi Key <span class="text-red-500">*</span></label>
                <input name="name" type="text" class="form-input w-full" required />
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('system.api-keys.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 mr-3">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Generate Token</button>
            </div>
        </form>
    </div>
</div>
@endsection
