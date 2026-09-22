@extends('layouts.dashboard')

@section('title', 'Buat Test TOEFL')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Buat Test TOEFL Baru</h1>
        <a href="{{ route('toefl-tests.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 text-sm">Kembali</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="{{ route('toefl-tests.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label for="test_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Test <span class="text-red-500">*</span></label>
                <input id="test_name" name="test_name" type="text" value="{{ old('test_name') }}"
                    class="form-input w-full @error('test_name') border-red-400 @enderror" required />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Password Test <span class="text-gray-400 font-normal">(kosongkan jika tanpa password)</span>
                </label>
                <input id="password" name="password" type="text" value="{{ old('password') }}"
                    class="form-input w-full @error('password') border-red-400 @enderror"
                    placeholder="Min. 4 karakter" />
            </div>

            <div class="flex items-center gap-3">
                <input id="is_trial" name="is_trial" type="checkbox" value="1"
                    class="rounded border-gray-300 text-indigo-600" {{ old('is_trial') ? 'checked' : '' }} />
                <label for="is_trial" class="text-sm font-medium text-gray-700">
                    Test Trial <span class="text-gray-400 font-normal">(akses publik tanpa login member)</span>
                </label>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <p class="text-sm font-medium text-gray-700 mb-3">Durasi per Section (menit)</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Listening</label>
                        <input name="listening_time_limit" type="number" min="1"
                            value="{{ old('listening_time_limit', 35) }}" class="form-input w-full" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Structure</label>
                        <input name="structure_time_limit" type="number" min="1"
                            value="{{ old('structure_time_limit', 25) }}" class="form-input w-full" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Reading</label>
                        <input name="reading_time_limit" type="number" min="1"
                            value="{{ old('reading_time_limit', 55) }}" class="form-input w-full" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('toefl-tests.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Buat Test</button>
            </div>
        </form>
    </div>
</div>
@endsection
