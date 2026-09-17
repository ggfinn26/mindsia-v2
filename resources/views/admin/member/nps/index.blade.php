@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-gray-800 font-bold">Review & NPS Member</h1>
    </div>

    <!-- Stats/Scores -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Average Rating -->
        <div class="flex flex-col bg-white shadow-lg rounded-sm border border-gray-200 p-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-2">Rata-rata Rating</h2>
            <div class="flex items-start">
                <div class="text-3xl font-bold text-gray-800 mr-2">0.0</div>
                <div class="text-sm font-semibold text-white px-1.5 bg-yellow-500 rounded-full mt-1">/ 5</div>
            </div>
        </div>
        <!-- NPS Score -->
        <div class="flex flex-col bg-white shadow-lg rounded-sm border border-gray-200 p-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-2">NPS Score</h2>
            <div class="flex items-start">
                <div class="text-3xl font-bold text-gray-800 mr-2">0</div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead class="text-xs font-semibold uppercase text-gray-500 bg-gray-50 border-t border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">Member & Program</th>
                        <th class="px-6 py-3 text-left">Rating</th>
                        <th class="px-6 py-3 text-left">Kategori NPS</th>
                        <th class="px-6 py-3 text-left">Review</th>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Status Landing Page</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Belum ada review NPS.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
