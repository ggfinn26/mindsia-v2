@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Profil Member</h1>
        <button class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Ganti Password</button>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap</h3>
                <p class="text-gray-900 font-medium text-lg">{{ auth()->user()->name }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Email</h3>
                <p class="text-gray-900 font-medium">{{ auth()->user()->email }}</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Program Aktif</h3>
                <p class="text-gray-900 font-medium">-</p>
            </div>
        </div>
    </div>
</div>
@endsection
