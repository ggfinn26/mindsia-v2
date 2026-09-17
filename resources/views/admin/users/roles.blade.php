@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Kelola Role & Akses User</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-800">User: -</h2>
        </div>
        
        <form action="#" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="roles[]" value="admin" class="form-checkbox text-indigo-500">
                    <span class="text-sm ml-2">Super Admin</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="roles[]" value="marketing" class="form-checkbox text-indigo-500">
                    <span class="text-sm ml-2">Marketing</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="roles[]" value="tutor" class="form-checkbox text-indigo-500">
                    <span class="text-sm ml-2">Tutor</span>
                </label>
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('admin.users.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 mr-3">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Update Role</button>
            </div>
        </form>
    </div>
</div>
@endsection
