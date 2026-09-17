@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-5xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Detail Snapshot Pegawai</h1>
        <a href="{{ route('marketing-kpi.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600">Kembali</a>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200 p-6 space-y-6">
        <div>
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Nama Pegawai</h3>
            <p class="text-gray-900 font-medium text-lg">-</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Ranking (Area)</h3>
                <p class="text-gray-900 font-medium">-</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Kelas (vs Target)</h3>
                <p class="text-gray-900 font-medium">- / -</p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Total Omzet (vs Target)</h3>
                <p class="text-gray-900 font-medium">- / -</p>
            </div>
        </div>
    </div>
</div>
@endsection
