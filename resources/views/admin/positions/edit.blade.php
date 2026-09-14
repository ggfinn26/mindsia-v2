@extends('layouts.dashboard')

@section('title', 'Edit Posisi')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Posisi: {{ $position->position_name }}</h1>
        <a href="{{ route('positions.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium border border-gray-300">
            Batal
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <strong>Ada kesalahan pada input Anda:</strong>
            <ul class="list-disc pl-5 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <form action="{{ route('positions.update', $position->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Nama Posisi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Posisi <span class="text-red-500">*</span></label>
                    <input type="text" name="position_name" value="{{ old('position_name', $position->position_name) }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Role Sistem Terkait <span class="text-red-500">*</span></label>
                    <select name="role_id" required class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Pilih Role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $position->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Hierarchy Order -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Urutan (Hierarchy) <span class="text-red-500">*</span></label>
                    <input type="number" name="hierarchy_order" value="{{ old('hierarchy_order', $position->hierarchy_order) }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <p class="text-xs text-gray-500 mt-1">Gunakan angka. Angka lebih kecil = hierarki lebih tinggi (contoh: 1 untuk Direktur).</p>
                </div>
            </div>

            <!-- Permissions Khusus -->
            <div class="mt-6 border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Izin Tambahan (Custom Permissions)</h3>
                <p class="text-sm text-gray-500 mb-4">Pilih izin spesifik yang melekat pada posisi ini. (Biasanya cukup ditentukan dari Role, gunakan ini hanya untuk pengecualian).</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 max-h-96 overflow-y-auto p-4 border border-gray-200 rounded-md">
                    @php
                        // Get current position's permission IDs
                        $currentPermissionIds = $position->permissions->pluck('id')->toArray();
                    @endphp
                    @foreach($permissions as $permission)
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded" {{ in_array($permission->id, old('permission_ids', $currentPermissionIds)) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 text-sm">
                                <label class="font-medium text-gray-700">{{ $permission->name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 border border-transparent rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
