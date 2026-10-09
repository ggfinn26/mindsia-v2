@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-red-600 font-bold">Force Reset Passwords</h1>
    </div>

    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-red-200 p-6">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-[color:var(--dash-text-heading)]">Cari Pengguna</h2>
            <p class="text-sm text-[color:var(--dash-text-muted)]">Gunakan fitur ini dengan hati-hati. Fitur ini akan memaksa pengguna untuk mereset password mereka.</p>
        </div>
        
        <form action="#" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Email / Username Pengguna</label>
                <div class="flex gap-2">
                    <input name="search" type="text" class="form-input flex-1" placeholder="Masukkan email atau username" />
                    <button type="button" class="btn bg-gray-200 hover:bg-gray-300 text-[color:var(--dash-text-body)]">Cari</button>
                </div>
            </div>
            
            <!-- Result placeholder -->
            <div class="hidden border border-[color:var(--dash-card-border)] p-4 rounded bg-[var(--dash-nav-hover-bg)] mt-4">
                <p class="font-medium text-[color:var(--dash-text-heading)]">User ditemukan: <span class="text-indigo-600">John Doe (john@example.com)</span></p>
                <div class="mt-4 flex gap-3">
                    <button type="submit" class="btn bg-red-500 hover:bg-red-600 text-white">Reset Password (Kirim Link via Email)</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
