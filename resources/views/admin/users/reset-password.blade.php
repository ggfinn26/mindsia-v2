@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-red-600 font-bold">Reset Password User</h1>
    </div>

    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-red-200 p-6">
        <p class="text-[color:var(--dash-text-body)] mb-4">Anda akan mereset password untuk user tertentu. Silakan konfirmasi tindakan ini.</p>
        
        <form action="#" method="POST">
            @csrf
            
            <div class="mt-4 flex">
                <a href="{{ route('users.index') }}" class="btn bg-[var(--dash-card-bg)] border-[color:var(--dash-card-border)] hover:border-[color:var(--dash-input-border)] text-[color:var(--dash-text-body)] mr-3 inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</a>
                <button type="submit" class="btn bg-[#dc2626] hover:opacity-90 text-white inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">lock_reset</span>Kirim Email Reset Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
