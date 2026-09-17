@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Support Ticket</h1>
        <a href="{{ route('admin.member.support-tickets.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Ticket Info -->
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 md:col-span-1">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Informasi Tiket</h2>
            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Tiket ID</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Member</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Kategori</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Status</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 mb-1">Ditugaskan Kepada</h3>
                    <p class="text-gray-900 font-medium">-</p>
                </div>
            </div>
        </div>

        <!-- Ticket Thread -->
        <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 md:col-span-2 flex flex-col h-[600px]">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Subject: -</h2>
            
            <div class="flex-1 overflow-y-auto space-y-4 mb-4 pr-2">
                <!-- Thread messages will go here -->
                <div class="text-center text-gray-500 text-sm py-4">Belum ada pesan.</div>
            </div>

            <!-- Reply Form -->
            <div class="border-t pt-4">
                <form action="#" method="POST">
                    @csrf
                    <textarea name="message" rows="3" class="form-textarea w-full mb-2" placeholder="Tulis balasan..."></textarea>
                    <div class="flex justify-between items-center">
                        <label class="flex items-center text-sm">
                            <input type="checkbox" name="close_ticket" class="form-checkbox text-indigo-500">
                            <span class="ml-2">Tandai tiket selesai</span>
                        </label>
                        <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Kirim Balasan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
