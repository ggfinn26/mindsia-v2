@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">
    <!-- Page header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('leave-requests.index') }}" class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-white border border-gray-200 text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <span class="sr-only">Kembali</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl md:text-3xl text-gray-900 font-bold tracking-tight">Ajukan Permohonan</h1>
                <p class="mt-1 text-sm text-gray-500">Isi formulir di bawah untuk mengajukan izin, sakit, atau cuti.</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="p-6 sm:p-8 space-y-6">
                <!-- Jenis Permohonan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="leave_type">Jenis Permohonan <span class="text-rose-500" aria-hidden="true">*</span></label>
                    <select id="leave_type" name="leave_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('leave_type') border-rose-300 text-rose-900 focus:border-rose-500 focus:ring-rose-500 @enderror" required>
                        <option value="" disabled @if(!old('leave_type')) selected @endif>Pilih Jenis</option>
                        <option value="permission" @if(old('leave_type') === 'permission') selected @endif>Izin</option>
                        <option value="sick" @if(old('leave_type') === 'sick') selected @endif>Sakit</option>
                        <option value="leave" @if(old('leave_type') === 'leave') selected @endif>Cuti</option>
                    </select>
                    @error('leave_type')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Mulai -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="start_date">Mulai Tanggal <span class="text-rose-500" aria-hidden="true">*</span></label>
                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('start_date') border-rose-300 text-rose-900 focus:border-rose-500 focus:ring-rose-500 @enderror" required />
                        @error('start_date')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="end_date">Sampai Tanggal <span class="text-rose-500" aria-hidden="true">*</span></label>
                        <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('end_date') border-rose-300 text-rose-900 focus:border-rose-500 focus:ring-rose-500 @enderror" required />
                        @error('end_date')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Alasan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="reason">Alasan / Keterangan <span class="text-rose-500" aria-hidden="true">*</span></label>
                    <textarea id="reason" name="reason" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors @error('reason') border-rose-300 text-rose-900 focus:border-rose-500 focus:ring-rose-500 @enderror" placeholder="Tuliskan keterangan detail..." required>{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lampiran -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="attachment">Lampiran Pendukung</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md bg-gray-50 hover:bg-gray-100 transition-colors relative">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="attachment" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span class="px-2">Unggah file</span>
                                    <input id="attachment" name="attachment" type="file" class="sr-only">
                                </label>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, PDF up to 5MB</p>
                        </div>
                    </div>
                    @error('attachment')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3 rounded-b-lg">
                <a href="{{ route('leave-requests.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">Batal</a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">Ajukan Permohonan</button>
            </div>
        </form>
    </div>
</div>
@endsection
