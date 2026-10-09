@extends('layouts.dashboard')

@section('title', 'Edit Profile - MINDSIA')
@section('header_title', 'Edit Profile')

@push('head-scripts')
    @vite('resources/js/employee-profile-edit.js')
@endpush

@section('content')
<h1 class="sr-only">Edit Profile</h1>
<div class="max-w-3xl mx-auto">

    @if ($errors->any())
        <div class="mb-5 px-4 py-3 rounded-xl border text-sm" style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            {{-- Informasi Akun --}}
            <div class="px-6 sm:px-8 py-6">
                <div class="flex items-center gap-2.5 mb-5">
                    <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                    <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Informasi Akun</h3>
                </div>

                <div class="space-y-5">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Nama Panggilan <span class="text-xs font-normal" style="color: var(--dash-text-muted);">(tampil di dashboard)</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div>
                        <label for="telegram_chat_id" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Telegram Chat ID
                        </label>
                        <input type="text" name="telegram_chat_id" id="telegram_chat_id"
                               value="{{ old('telegram_chat_id', $user->telegram_chat_id) }}"
                               placeholder="Contoh: 123456789">
                        <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);">
                            Gunakan ID ini untuk menerima notifikasi dari bot Telegram.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled>
                        <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);">
                            Email tidak dapat diubah. Hubungi IT jika perlu mengubah email.
                        </p>
                    </div>
                </div>
            </div>

            @if($employee)
            {{-- Data Karyawan --}}
            <div class="px-6 sm:px-8 py-6 border-t" style="border-color: var(--dash-card-border);">
                <div class="flex items-center gap-2.5 mb-5">
                    <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                    <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Data Karyawan</h3>
                </div>

                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Nama Lengkap (HR)</label>
                            <input type="text" value="{{ $employee->full_name }}" disabled>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">Kode Karyawan</label>
                            <input type="text" value="{{ $employee->employee_code }}" disabled>
                        </div>
                    </div>

                    <div>
                        <label for="whatsapp_number" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            No. WhatsApp
                        </label>
                        <div class="flex">
                            <span class="input-prefix inline-flex items-center px-3 rounded-l-xl border border-r-0 text-sm">+62</span>
                            <input type="text" name="whatsapp_number" id="whatsapp_number"
                                   value="{{ old('whatsapp_number', preg_replace('/^\+?62/', '', $employee->whatsapp_number)) }}"
                                   placeholder="81234567890" minlength="8" maxlength="15"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                                   style="border-radius: 0 0.75rem 0.75rem 0 !important; width: 100%;">
                        </div>
                        <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);">
                            Gunakan nomor aktif untuk keperluan notifikasi sistem.
                        </p>
                    </div>

                    <div x-data="{ fileName: '' }">
                        <label for="photo" class="block text-sm font-medium mb-1.5" style="color: var(--dash-text-body);">
                            Foto Profil
                        </label>
                        <div class="flex items-center gap-3">
                            <label for="photo"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold cursor-pointer transition-colors shrink-0"
                                   style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text); border: 1px solid var(--dash-primary);">
                                <span class="material-symbols-outlined text-[16px]">upload</span>
                                Pilih File
                            </label>
                            <span class="text-sm truncate" style="color: var(--dash-text-muted);"
                                  x-text="fileName || 'Belum ada file dipilih'"></span>
                        </div>
                        <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/jpg"
                               class="sr-only"
                               @change="fileName = $event.target.files[0]?.name || ''">
                        <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);">Maks. 2MB. Format JPG, JPEG, PNG.</p>
                    </div>

                    <div class="pt-4 border-t" style="border-color: var(--dash-card-border);">
                        <label class="block text-sm font-medium mb-2" style="color: var(--dash-text-body);">Tanda Tangan</label>

                        @if($employee->signature_image)
                            <div class="mb-3 p-3 rounded-xl border"
                                 style="border-color: var(--dash-card-border); background-color: var(--dash-primary-bg);">
                                <p class="text-xs mb-2" style="color: var(--dash-text-muted);">Tanda Tangan Saat Ini:</p>
                                <img src="{{ Storage::url($employee->signature_image) }}" alt="Tanda Tangan" class="h-16 object-contain">
                            </div>
                            <p class="text-xs mb-2" style="color: var(--dash-text-muted);">
                                Gambar ulang untuk memperbarui:
                            </p>
                        @else
                            <p class="text-xs mb-2" style="color: var(--dash-text-muted);">
                                Gambar tanda tangan Anda di dalam kotak ini:
                            </p>
                        @endif

                        <div class="border rounded-2xl overflow-hidden w-full relative shadow-sm"
                             style="border-color: var(--dash-card-border);">
                            <canvas id="signature-pad" width="800" height="200"
                                    class="w-full touch-none cursor-crosshair"></canvas>
                            <button type="button" id="clear-signature"
                                    class="absolute top-2 right-2 text-xs px-2 py-1 rounded-lg border shadow-sm"
                                    style="background-color: var(--dash-card-bg); border-color: var(--dash-card-border); color: var(--dash-text-muted);">
                                Bersihkan
                            </button>
                        </div>
                        <input type="hidden" name="signature_base64" id="signature_base64">
                        <p class="mt-1.5 text-xs" style="color: var(--dash-text-muted);">
                            Gunakan mouse atau sentuh layar untuk menggambar.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Footer aksi --}}
            <div class="px-6 sm:px-8 py-4 border-t flex items-center justify-end gap-3"
                 style="border-color: var(--dash-card-border); background-color: var(--dash-primary-bg);">
                <a href="{{ route('employee.profile.show') }}"
                   class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors inline-flex items-center gap-1.5"
                   style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    Batal
                </a>
                <button type="submit"
                        class="px-4 py-2 text-sm font-semibold rounded-lg text-white transition-colors inline-flex items-center gap-1.5"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
