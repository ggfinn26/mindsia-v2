@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">
    <!-- Page header -->
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Ajukan Permohonan (Izin/Sakit/Cuti)</h1>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <form action="#" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <!-- Jenis Permohonan -->
                <div>
                    <label class="block text-sm font-medium mb-1" for="type">Jenis Permohonan <span class="text-red-500">*</span></label>
                    <select id="type" name="type" class="form-select w-full" required>
                        <option value="">Pilih Jenis</option>
                        <option value="izin">Izin</option>
                        <option value="sakit">Sakit</option>
                        <option value="cuti">Cuti</option>
                    </select>
                </div>

                <!-- Tanggal Mulai -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="start_date">Dari Tanggal <span class="text-red-500">*</span></label>
                        <input id="start_date" name="start_date" type="date" class="form-input w-full" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="end_date">Sampai Tanggal <span class="text-red-500">*</span></label>
                        <input id="end_date" name="end_date" type="date" class="form-input w-full" required />
                    </div>
                </div>

                <!-- Alasan -->
                <div>
                    <label class="block text-sm font-medium mb-1" for="reason">Alasan / Keterangan <span class="text-red-500">*</span></label>
                    <textarea id="reason" name="reason" rows="4" class="form-textarea w-full" required></textarea>
                </div>

                <!-- Lampiran -->
                <div>
                    <label class="block text-sm font-medium mb-1" for="attachment">Lampiran Pendukung</label>
                    <input id="attachment" name="attachment" type="file" class="form-input w-full" />
                    <p class="text-xs text-gray-500 mt-1">Surat dokter jika sakit, atau dokumen terkait izin. Maks 2MB.</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('attendance.leave.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 mr-3">Batal</a>
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Ajukan Permohonan</button>
            </div>
        </form>
    </div>
</div>
@endsection
