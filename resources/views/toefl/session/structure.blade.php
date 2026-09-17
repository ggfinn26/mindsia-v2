@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header / Timer -->
        <div class="bg-white p-4 shadow-sm rounded-lg mb-6 flex justify-between items-center border-t-4 border-indigo-500">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Section 2: Structure & Written Expression</h2>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Sisa Waktu</p>
                <p class="text-2xl font-mono font-bold text-red-600">25:00</p>
            </div>
        </div>

        <!-- Question Area -->
        <div class="bg-white p-6 shadow-sm rounded-lg">
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">1. The committee _____ decided to postpone the meeting.</h3>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="q1" value="A" class="form-radio h-4 w-4 text-indigo-600">
                        <span class="ml-3 text-gray-700">have</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="q1" value="B" class="form-radio h-4 w-4 text-indigo-600">
                        <span class="ml-3 text-gray-700">has</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="q1" value="C" class="form-radio h-4 w-4 text-indigo-600">
                        <span class="ml-3 text-gray-700">is</span>
                    </label>
                    <label class="flex items-center p-3 border rounded-md hover:bg-gray-50 cursor-pointer">
                        <input type="radio" name="q1" value="D" class="form-radio h-4 w-4 text-indigo-600">
                        <span class="ml-3 text-gray-700">are</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end border-t pt-4">
                <a href="{{ route('toefl.session.reading') }}" class="btn bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-medium">Lanjut ke Section 3</a>
            </div>
        </div>
    </div>
</div>
@endsection
