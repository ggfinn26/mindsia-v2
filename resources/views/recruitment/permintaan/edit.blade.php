@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('recruitment.permintaan.show', $jobPermintaan) }}" class="text-gray-400 hover:text-gray-600">
            <span class="material-symbols-outlined text-xl">arrow_back</span>
        </a>
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Edit Job Permintaan</h1>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="{{ route('recruitment.permintaan.update', $jobPermintaan) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Posisi yang Diminta <span class="text-red-500">*</span>
                </label>
                <select name="detail[position_id]" class="form-select w-full" required>
                    <option value="">Pilih Posisi</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}"
                            {{ old('detail.position_id', $jobPermintaan->detail?->position_id) == $position->id ? 'selected' : '' }}>
                            {{ $position->position_name }}
                        </option>
                    @endforeach
                </select>
                @error('detail.position_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jenis Permintaan <span class="text-red-500">*</span>
                    </label>
                    <select name="detail[request_type]" class="form-select w-full" required>
                        <option value="">Pilih Jenis</option>
                        <option value="replacement"
                            {{ old('detail.request_type', $jobPermintaan->detail?->request_type) === 'replacement' ? 'selected' : '' }}>
                            Penggantian (Replacement)
                        </option>
                        <option value="new_position"
                            {{ old('detail.request_type', $jobPermintaan->detail?->request_type) === 'new_position' ? 'selected' : '' }}>
                            Posisi Baru (New Position)
                        </option>
                    </select>
                    @error('detail.request_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipe Pekerjaan <span class="text-red-500">*</span>
                    </label>
                    @php $empType = old('detail.employment_type', $jobPermintaan->detail?->employment_type); @endphp
                    <select name="detail[employment_type]" class="form-select w-full" required>
                        <option value="">Pilih Tipe</option>
                        <option value="kontrak" {{ $empType === 'kontrak' ? 'selected' : '' }}>Kontrak</option>
                        <option value="tetap" {{ $empType === 'tetap' ? 'selected' : '' }}>Tetap</option>
                        <option value="magang" {{ $empType === 'magang' ? 'selected' : '' }}>Magang</option>
                        <option value="paruh_waktu" {{ $empType === 'paruh_waktu' ? 'selected' : '' }}>Paruh Waktu</option>
                    </select>
                    @error('detail.employment_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah yang Dibutuhkan <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="detail[headcount]" min="1"
                        value="{{ old('detail.headcount', $jobPermintaan->detail?->headcount ?? 1) }}"
                        class="form-input w-full" required />
                    @error('detail.headcount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah Saat Ini (opsional)
                    </label>
                    <input type="number" name="detail[current_headcount]" min="0"
                        value="{{ old('detail.current_headcount', $jobPermintaan->detail?->current_headcount) }}"
                        class="form-input w-full" placeholder="Isi jika ada" />
                    @error('detail.current_headcount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Target Tanggal Mulai (opsional)
                </label>
                <input type="date" name="detail[target_start_date]"
                    value="{{ old('detail.target_start_date', $jobPermintaan->detail?->target_start_date?->format('Y-m-d')) }}"
                    class="form-input w-full" />
                @error('detail.target_start_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi Pekerjaan (opsional)
                </label>
                <textarea name="detail[job_description]" rows="4" class="form-textarea w-full"
                    placeholder="Uraikan tugas dan tanggung jawab...">{{ old('detail.job_description', $jobPermintaan->detail?->job_description) }}</textarea>
                @error('detail.job_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2 flex justify-end gap-3">
                <a href="{{ route('recruitment.permintaan.show', $jobPermintaan) }}"
                    class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
