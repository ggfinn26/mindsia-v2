@extends('layouts.dashboard')

@section('title', 'Edit Prospective Member')

@section('content')
<div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('prospective-members.show', $lead) }}" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-sm">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit Data Lead</h1>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('prospective-members.update', $lead) }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $lead->full_name) }}" required
                class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('full_name') border-red-300 @enderror">
            @error('full_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-1">
                Nomor WhatsApp <span class="text-red-500">*</span>
            </label>
            <div class="flex rounded-md shadow-sm">
                <span class="inline-flex items-center rounded-l-md border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm font-bold text-gray-500">+62</span>
                <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $lead-  minlength="8" maxlength="15">whatsapp_number) }}" required
                    placeholder="8xxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                    class="block w-full min-w-0 flex-1 rounded-none rounded-r-md border-gray-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('whatsapp_number') border-red-300 @enderror">
            </div>
            @error('whatsapp_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email
            </label>
            <input type="email" name="email" id="email" value="{{ old('email', $lead->email) }}"
                class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-300 @enderror">
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="instagram" class="block text-sm font-medium text-gray-700 mb-1">
                Instagram (Username)
            </label>
            <input type="text" name="instagram" id="instagram" value="{{ old('instagram', $lead->instagram) }}"
                class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('instagram') border-red-300 @enderror">
            @error('instagram') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                Jenis Kelamin
            </label>
            <select name="gender" id="gender" class="block w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('gender') border-red-300 @enderror">
                <option value="">-- Pilih --</option>
                <option value="L" {{ old('gender', $lead->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('gender', $lead->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('gender') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('prospective-members.show', $lead) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
