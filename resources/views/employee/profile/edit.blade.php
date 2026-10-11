@extends('layouts.dashboard')

@section('title', 'Edit Profile - MINDSIA')
@section('header_title', 'Edit Profile')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('employee.profile.show') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Profile Saya
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Pengaturan Akun</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Edit Profile</h1>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm"
         style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
        <ul class="list-disc list-inside space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        {{-- Informasi Akun --}}
        <div class="rounded-2xl overflow-hidden mb-5"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Informasi Akun</h2>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div>
                    <label for="name" class="block text-sm font-medium mb-1"
                           style="color: var(--dash-text-body);">
                        Nama Panggilan (Tampil di Dashboard) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>

                <div>
                    <label for="telegram_chat_id" class="block text-sm font-medium mb-1"
                           style="color: var(--dash-text-body);">Telegram Chat ID</label>
                    <input type="text" name="telegram_chat_id" id="telegram_chat_id"
                           value="{{ old('telegram_chat_id', $user->telegram_chat_id) }}"
                           placeholder="Misal: 123456789"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">
                        Gunakan ID ini untuk menerima notifikasi dari bot Telegram kami.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">Email</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full px-4 py-2.5 text-sm rounded-lg cursor-not-allowed"
                           style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-faint);">
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">
                        Email tidak dapat diubah. Hubungi IT jika perlu mengubah email.
                    </p>
                </div>
            </div>
        </div>

        @if($employee)
        {{-- Data Karyawan --}}
        <div class="rounded-2xl overflow-hidden mb-5"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="px-6 py-4"
                 style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Data Karyawan</h2>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Nama Lengkap (HR)
                        </label>
                        <input type="text" value="{{ $employee->full_name }}" disabled
                               class="w-full px-4 py-2.5 text-sm rounded-lg cursor-not-allowed"
                               style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-faint);">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);">
                            Kode Karyawan
                        </label>
                        <input type="text" value="{{ $employee->employee_code }}" disabled
                               class="w-full px-4 py-2.5 text-sm rounded-lg cursor-not-allowed"
                               style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-faint);">
                    </div>
                </div>

                <div>
                    <label for="whatsapp_number" class="block text-sm font-medium mb-1"
                           style="color: var(--dash-text-body);">No. WhatsApp</label>
                    <div class="flex rounded-lg overflow-hidden"
                         style="border: 1px solid var(--dash-input-border);"
                         id="wa-wrapper">
                        <span class="inline-flex items-center px-3 text-sm shrink-0"
                              style="background-color: var(--dash-nav-hover-bg); border-right: 1px solid var(--dash-input-border); color: var(--dash-text-muted);">
                            +62
                        </span>
                        <input type="text" name="whatsapp_number" id="whatsapp_number"
                               value="{{ old('whatsapp_number', preg_replace('/^\+?62/', '', $employee->whatsapp_number)) }}"
                               placeholder="81234567890"
                               minlength="8" maxlength="15"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                               class="flex-1 px-3 py-2.5 text-sm focus:outline-none"
                               style="background-color: var(--dash-input-bg); color: var(--dash-text-heading);"
                               onfocus="document.getElementById('wa-wrapper').style.borderColor='var(--dash-primary)'; document.getElementById('wa-wrapper').style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="document.getElementById('wa-wrapper').style.borderColor='var(--dash-input-border)'; document.getElementById('wa-wrapper').style.boxShadow='none'">
                    </div>
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">
                        Gunakan nomor yang aktif untuk keperluan notifikasi sistem.
                    </p>
                </div>

                <div>
                    <label for="photo" class="block text-sm font-medium mb-1"
                           style="color: var(--dash-text-body);">Foto Profil</label>
                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg"
                           class="dash-file-input">
                    <p class="mt-1 text-xs" style="color: var(--dash-text-faint);">
                        Maksimal 2MB. Format JPG, JPEG, PNG.
                    </p>
                </div>

                {{-- Tanda Tangan --}}
                <div style="border-top: 1px solid var(--dash-card-border);" class="pt-5">
                    <label class="block text-sm font-medium mb-2" style="color: var(--dash-text-body);">
                        Tanda Tangan
                    </label>

                    @if($employee->signature_image)
                    <div class="mb-3 p-3 rounded-xl max-w-sm"
                         style="border: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <p class="text-xs mb-2" style="color: var(--dash-text-faint);">Tanda Tangan Saat Ini:</p>
                        <img src="{{ Storage::disk('public')->url($employee->signature_image) }}" alt="Tanda Tangan" class="h-16 object-contain">
                    </div>
                    <p class="text-xs mb-2" style="color: var(--dash-text-faint);">
                        Untuk memperbarui, gambar ulang tanda tangan di bawah ini:
                    </p>
                    @else
                    <p class="text-xs mb-2" style="color: var(--dash-text-faint);">
                        Gambar tanda tangan Anda di dalam kotak ini:
                    </p>
                    @endif

                    <div class="relative w-full max-w-sm rounded-xl overflow-hidden"
                         style="border: 1px solid var(--dash-input-border);">
                        <canvas id="signature-pad" width="380" height="180"
                                class="bg-white w-full touch-none cursor-crosshair block"></canvas>
                        <button type="button" id="clear-signature"
                                class="absolute top-2 right-2 px-2 py-1 text-xs font-semibold rounded-md transition-colors"
                                style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
                                onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Bersihkan
                        </button>
                    </div>
                    <input type="hidden" name="signature_base64" id="signature_base64">
                    <p class="mt-1.5 text-xs" style="color: var(--dash-text-faint);">
                        Gunakan mouse atau sentuh layar untuk menggambar.
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('employee.profile.show') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('scripts')
    @vite('resources/js/employee-profile-edit.js')
@endpush
@endsection
