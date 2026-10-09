@extends('layouts.dashboard')

@section('title', 'Ubah Password - MINDSIA')
@section('header_title', 'Ubah Password')

@section('content')
<h1 class="sr-only">Ubah Password</h1>
<div class="max-w-2xl mx-auto pt-8 sm:pt-16">

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">error</span>
            <ul class="list-disc list-inside space-y-0.5 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('employee.password.update') }}" method="POST">
            @csrf
            @method('PATCH')

            <div class="px-6 sm:px-8 py-6">
                <div class="flex items-center gap-2.5 mb-6">
                    <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                    <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Ubah Password Akun</h3>
                </div>
                <p class="text-sm mb-6 -mt-4" style="color: var(--dash-text-muted);">
                    Pastikan akun Anda menggunakan password yang panjang, acak, dan aman.
                </p>

                <div class="space-y-5">
                    <div x-data="{ show: false }">
                        <label for="current_password" class="block text-sm font-medium mb-1.5"
                               style="color: var(--dash-text-body);">Password Saat Ini</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'"
                                   name="current_password" id="current_password" required
                                   style="padding-right: 2.75rem !important;">
                            <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    style="color: var(--dash-text-muted);">
                                <span class="material-symbols-outlined text-[18px]"
                                      x-text="show ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                    </div>

                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium mb-1.5"
                               style="color: var(--dash-text-body);">Password Baru</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'"
                                   name="password" id="password" required
                                   style="padding-right: 2.75rem !important;">
                            <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    style="color: var(--dash-text-muted);">
                                <span class="material-symbols-outlined text-[18px]"
                                      x-text="show ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);">
                            Minimal 8 karakter, mengandung huruf besar, huruf kecil, angka, dan simbol.
                        </p>
                    </div>

                    <div x-data="{ show: false }">
                        <label for="password_confirmation" class="block text-sm font-medium mb-1.5"
                               style="color: var(--dash-text-body);">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'"
                                   name="password_confirmation" id="password_confirmation" required
                                   style="padding-right: 2.75rem !important;">
                            <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                    style="color: var(--dash-text-muted);">
                                <span class="material-symbols-outlined text-[18px]"
                                      x-text="show ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 sm:px-8 py-4 border-t flex items-center justify-end"
                 style="border-color: var(--dash-card-border); background-color: var(--dash-primary-bg);">
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold rounded-lg text-white transition-colors"
                        style="background-color: var(--dash-primary);">
                    Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
