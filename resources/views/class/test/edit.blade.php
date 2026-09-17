@extends('layouts.dashboard')

@section('title', 'Edit Test — ' . $test->test_name)

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Test</h1>
        <a href="{{ route('classrooms.show', $classroom) }}" class="text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">
            Kembali
        </a>
    </div>

    <p class="text-sm text-gray-500 mb-6">Kelas: <span class="font-medium text-gray-700">{{ $classroom->class_name }}</span></p>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('class-tests.update', [$classroom, $test]) }}" method="POST" class="bg-white shadow sm:rounded-lg">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Test <span class="text-red-500">*</span></label>
                <input type="text" name="test_name" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       value="{{ old('test_name', $test->test_name) }}" />
                @error('test_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Test <span class="text-red-500">*</span></label>
                    <select name="test_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="pre_test" {{ old('test_type', $test->test_type) === 'pre_test' ? 'selected' : '' }}>Pre-Test</option>
                        <option value="post_test" {{ old('test_type', $test->test_type) === 'post_test' ? 'selected' : '' }}>Post-Test</option>
                    </select>
                    @error('test_type')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Test <span class="text-red-500">*</span></label>
                    <input type="date" name="date" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           value="{{ old('date', $test->date?->format('Y-m-d')) }}" />
                    @error('date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $test->description) }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Scoring Criteria (read-only display) -->
            @if($test->scoringCriteria->isNotEmpty())
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-md font-semibold text-gray-900 mb-4">Kriteria Penilaian</h3>
                <div class="space-y-3">
                    @foreach($test->scoringCriteria as $criterion)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <p class="font-medium text-gray-900 text-sm">{{ $criterion->criteria_name }}</p>
                            @if($criterion->description)
                                <p class="text-sm text-gray-500 mt-1">{{ $criterion->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-2">Kriteria tidak dapat diubah melalui form ini.</p>
            </div>
            @endif
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex gap-3 justify-end">
            <a href="{{ route('classrooms.show', $classroom) }}" class="px-6 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
