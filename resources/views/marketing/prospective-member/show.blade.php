@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Prospek Member</h1>
        <a href="{{ route('prospective-members.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Info Prospek -->
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 md:col-span-1">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Profil Prospek</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Nama Lengkap</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Telepon / WhatsApp</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Asal Sekolah</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Status Saat Ini</h3>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Almost</span>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-gray-200">
                <a href="#" class="btn w-full bg-green-500 hover:bg-green-600 text-white flex justify-center items-center">
                    <span class="material-symbols-outlined mr-2">chat</span> Hubungi via WA
                </a>
            </div>
        </div>

        <!-- Follow-up Log -->
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 md:col-span-2 flex flex-col h-[600px]">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Log Follow-Up</h2>
            
            <div class="flex-1 overflow-y-auto space-y-4 mb-4 pr-2">
                <!-- Log entries will go here -->
                <div class="text-center text-gray-500 text-sm py-4">Belum ada aktivitas follow-up.</div>
            </div>

            <!-- Add Log Form -->
            <div class="border-t pt-4">
                <form action="#" method="POST">
                    @csrf
                    <textarea name="log_notes" rows="3" class="form-textarea w-full mb-2" placeholder="Catatan hasil follow-up..."></textarea>
                    <div class="flex justify-between items-center">
                        <select name="status_update" class="form-select text-sm">
                            <option value="">-- Update Status (Opsional) --</option>
                            <option value="ALMOST">ALMOST</option>
                            <option value="YES">YES</option>
                            <option value="FIXED">FIXED</option>
                            <option value="NO">NO</option>
                        </select>
                        <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Simpan Catatan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
