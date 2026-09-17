@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header / Timer -->
        <div class="bg-white p-4 shadow-sm rounded-lg mb-6 flex justify-between items-center border-t-4 border-indigo-500">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Section 3: Reading Comprehension</h2>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Sisa Waktu</p>
                <p class="text-2xl font-mono font-bold text-red-600">55:00</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Reading Passage -->
            <div class="bg-white p-6 shadow-sm rounded-lg h-[600px] overflow-y-auto">
                <h3 class="font-bold text-lg mb-4">Passage 1</h3>
                <div class="text-gray-700 leading-relaxed space-y-4">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                    <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                </div>
            </div>

            <!-- Questions -->
            <div class="bg-white p-6 shadow-sm rounded-lg h-[600px] overflow-y-auto">
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">1. What is the main idea of the passage?</h3>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="q1" value="A" class="form-radio h-4 w-4 text-indigo-600">
                            <span class="ml-3 text-gray-700">Pilihan A</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="q1" value="B" class="form-radio h-4 w-4 text-indigo-600">
                            <span class="ml-3 text-gray-700">Pilihan B</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="q1" value="C" class="form-radio h-4 w-4 text-indigo-600">
                            <span class="ml-3 text-gray-700">Pilihan C</span>
                        </label>
                        <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
                            <input type="radio" name="q1" value="D" class="form-radio h-4 w-4 text-indigo-600">
                            <span class="ml-3 text-gray-700">Pilihan D</span>
                        </label>
                    </div>
                </div>
                
                <div class="flex justify-end border-t pt-4">
                    <a href="{{ route('toefl.guest.result') }}" class="btn bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-md font-medium">Selesai Ujian</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
