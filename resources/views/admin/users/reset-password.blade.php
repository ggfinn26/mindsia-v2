@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-red-600 font-bold">Reset Password User</h1>
    </div>

    <div class="bg-white shadow-lg rounded-sm border border-red-200 p-6">
        <p class="text-gray-600 mb-4">Anda akan mereset password untuk user tertentu. Silakan konfirmasi tindakan ini.</p>
        
        <form action="#" method="POST">
            @csrf
            
            <div class="mt-4 flex">
                <a href="{{ route('users.index') }}" class="btn bg-white border-gray-200 hover:border-gray-300 text-gray-600 mr-3">Batal</a>
                <button type="submit" class="btn bg-red-500 hover:bg-red-600 text-white">Kirim Email Reset Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
