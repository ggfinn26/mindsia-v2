@extends('layouts.dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl text-[color:var(--dash-text-heading)] font-bold">Kelola Role & Akses User</h1>
    </div>

    <div class="bg-[var(--dash-card-bg)] shadow-lg rounded-sm border border-[color:var(--dash-card-border)] p-6">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-[color:var(--dash-text-heading)]">User: -</h2>
        </div>
        
        <form action="#" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="roles[]" value="admin" class="form-checkbox text-indigo-500">
                    <span class="text-sm ml-2">Super Admin</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="roles[]" value="marketing" class="form-checkbox text-indigo-500">
                    <span class="text-sm ml-2">Marketing</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="roles[]" value="tutor" class="form-checkbox text-indigo-500">
                    <span class="text-sm ml-2">Tutor</span>
                </label>
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('users.index') }}" class="btn bg-[var(--dash-card-bg)] border-[color:var(--dash-card-border)] hover:border-[color:var(--dash-input-border)] text-[color:var(--dash-text-body)] mr-3 inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">close</span>Batal</a>
                <button type="submit" class="btn bg-[var(--dash-primary)] hover:opacity-90 text-white">Update Role</button>
            </div>
        </form>
    </div>
</div>
@endsection
