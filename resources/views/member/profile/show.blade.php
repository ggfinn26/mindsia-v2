@extends('layouts.dashboard')

@section('title', 'Profil Saya - MINDSIA')
@section('header_title', 'Profil Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('member.dashboard') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Dashboard
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Pengaturan Akun</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Profil Saya</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Kelola informasi pribadi, kontak, dan data akademik akun Anda.
        </p>
    </div>

    {{-- Flash & Error Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
         style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-success-text);">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm font-medium space-y-1"
         style="background-color: var(--dash-status-danger-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
        <div class="flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <span class="font-bold">Terjadi kesalahan pada input:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 pl-6">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Avatar Upload Card -->
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('member.profile.avatar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-center gap-6">
                <div class="h-24 w-24 rounded-full flex items-center justify-center overflow-hidden shrink-0"
                     style="background-color: var(--dash-primary-bg); border: 2px solid var(--dash-card-border);">
                    @if($member->telegram_photo_id)
                        <img src="{{ $member->telegram_photo_id }}" alt="Avatar" class="h-full w-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-5xl" style="color: var(--dash-primary);">account_circle</span>
                    @endif
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Foto Profil</h3>
                    <p class="text-xs mb-4" style="color: var(--dash-text-muted);">Maksimal 2MB. Format: JPG, PNG.</p>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <input type="file" name="avatar" id="avatar" accept="image/*"
                            class="dash-file-input"
                            required>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-semibold text-white rounded-lg transition-opacity hover:opacity-90 shadow-sm whitespace-nowrap shrink-0"
                            style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[16px]">upload</span>
                            Upload Foto
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Edit Profile Form Card -->
    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('member.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 sm:p-8 space-y-8">

                <!-- 1. Informasi Pribadi -->
                <div>
                    <h3 class="text-base sm:text-lg font-bold font-jakarta mb-4" style="color: var(--dash-text-heading);">Informasi Pribadi</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="full_name" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="full_name" name="full_name"
                                value="{{ old('full_name', $member->full_name) }}"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'"
                                required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Email</label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', $member->email) }}"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                            <p class="text-xs mt-1" style="color: var(--dash-status-warning-text);">Mengubah email akan memerlukan verifikasi ulang.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="whatsapp_number" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                                No. WhatsApp <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="whatsapp_number" name="whatsapp_number"
                                value="{{ old('whatsapp_number', $member->whatsapp_number) }}"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'"
                                required>
                        </div>
                        <div>
                            <label for="instagram" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Instagram</label>
                            <input type="text" id="instagram" name="instagram"
                                value="{{ old('instagram', $member->instagram) }}"
                                placeholder="username tanpa @"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="birth_place" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Tempat Lahir</label>
                            <input type="text" id="birth_place" name="birth_place"
                                value="{{ old('birth_place', $member->birth_place) }}"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                        <div>
                            <label for="birthdate" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Tanggal Lahir</label>
                            <input type="date" id="birthdate" name="birthdate"
                                value="{{ old('birthdate', $member->birthdate?->format('Y-m-d')) }}"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label for="address" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Alamat Lengkap</label>
                        <textarea id="address" name="address" rows="3"
                            class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('address', $member->address) }}</textarea>
                    </div>
                </div>

                <hr style="border-color: var(--dash-card-border);">

                <!-- 2. Data Akademik -->
                <div>
                    <h3 class="text-base sm:text-lg font-bold font-jakarta mb-4" style="color: var(--dash-text-heading);">Data Akademik & Cabang</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Cabang MINDSIA</label>
                            <input type="text" value="{{ $member->branch->branch_name ?? '-' }}"
                                class="w-full rounded-lg text-sm px-3 py-2 cursor-not-allowed focus:outline-none"
                                style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-muted);"
                                readonly>
                            <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Dikelola oleh admin.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Institusi Asal</label>
                            <input type="text" value="{{ $member->institution->institution_name ?? $member->institution_name_custom ?? '-' }}"
                                class="w-full rounded-lg text-sm px-3 py-2 cursor-not-allowed focus:outline-none"
                                style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-muted);"
                                readonly>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="education_level" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Jenjang Pendidikan</label>
                            <select id="education_level" name="education_level"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                                <option value="">Pilih Jenjang</option>
                                @foreach(['SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3','Umum'] as $level)
                                    <option value="{{ $level }}" @selected(old('education_level', $member->education_level) === $level)>{{ $level }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="class_or_faculty" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Jurusan atau Kelas</label>
                            <input type="text" id="class_or_faculty" name="class_or_faculty"
                                value="{{ old('class_or_faculty', $member->class_or_faculty) }}"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                    </div>
                </div>

                <hr style="border-color: var(--dash-card-border);">

                <!-- 3. Data Orang Tua -->
                <div>
                    <h3 class="text-base sm:text-lg font-bold font-jakarta mb-4" style="color: var(--dash-text-heading);">Data Orang Tua</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="father_name" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Nama Ayah</label>
                            <input type="text" id="father_name" name="father_name"
                                value="{{ old('father_name', $member->father_name) }}"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                        <div>
                            <label for="mother_name" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Nama Ibu</label>
                            <input type="text" id="mother_name" name="mother_name"
                                value="{{ old('mother_name', $member->mother_name) }}"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="father_whatsapp" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">WhatsApp Ayah</label>
                            <input type="text" id="father_whatsapp" name="father_whatsapp"
                                value="{{ old('father_whatsapp', $member->father_whatsapp) }}"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                        <div>
                            <label for="mother_whatsapp" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">WhatsApp Ibu</label>
                            <input type="text" id="mother_whatsapp" name="mother_whatsapp"
                                value="{{ old('mother_whatsapp', $member->mother_whatsapp) }}"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="father_occupation" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Pekerjaan Ayah</label>
                            <input type="text" id="father_occupation" name="father_occupation"
                                value="{{ old('father_occupation', $member->father_occupation) }}"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                        <div>
                            <label for="mother_occupation" class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Pekerjaan Ibu</label>
                            <input type="text" id="mother_occupation" name="mother_occupation"
                                value="{{ old('mother_occupation', $member->mother_occupation) }}"
                                class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                onfocus="this.style.borderColor='var(--dash-primary)'"
                                onblur="this.style.borderColor='var(--dash-input-border)'">
                        </div>
                    </div>
                </div>

                <!-- Submit Action Footer -->
                <div class="flex justify-end pt-4 border-t" style="border-color: var(--dash-card-border);">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-lg shadow-sm transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
