@extends('layouts.dashboard')

@section('title', 'Profile Saya - MINDSIA')
@section('header_title', 'Profile Saya')

@section('content')
<h1 class="sr-only">Profile Saya</h1>
<div class="max-w-4xl mx-auto">
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-start gap-3">
            <span class="material-symbols-outlined text-green-500 mt-0.5">check_circle</span>
            <p class="font-medium text-sm">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Avatar Upload -->
    <div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] overflow-hidden mb-6">
        <form action="{{ route('member.profile.avatar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-center gap-6">
                <div class="h-24 w-24 rounded-full bg-[#EEF4FF] flex items-center justify-center overflow-hidden border-2 border-[color:var(--dash-card-border)] shrink-0">
                    @if($member->telegram_photo_id)
                        <img src="{{ $member->telegram_photo_id }}" alt="Avatar" class="h-full w-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-[#5586DB] text-5xl">account_circle</span>
                    @endif
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <h3 class="text-lg font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-1">Foto Profil</h3>
                    <p class="text-xs text-[color:var(--dash-text-muted)] mb-3">Maksimal 2MB. Format: JPG, PNG.</p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center sm:justify-start">
                        <input type="file" name="avatar" id="avatar" accept="image/*"
                            class="w-full sm:w-auto text-sm text-[color:var(--dash-text-muted)] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-[color:var(--dash-input-border)] rounded-lg px-3 py-2 bg-[var(--dash-card-bg)]"
                            required>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 shadow-sm whitespace-nowrap inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">upload</span>
                            Upload Foto
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Edit Profile Form -->
    <div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] overflow-hidden">
        <form action="{{ route('member.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 sm:p-8 space-y-8">

                <!-- Informasi Pribadi -->
                <div>
                    <h3 class="text-lg font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-4">Informasi Pribadi</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="full_name" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" id="full_name" name="full_name"
                                value="{{ old('full_name', $member->full_name) }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]"
                                required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Email</label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', $member->email) }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">
                            <p class="text-xs text-amber-600 mt-1">Mengubah email akan memerlukan verifikasi ulang.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="whatsapp_number" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">No. WhatsApp <span class="text-red-500">*</span></label>
                            <input type="text" id="whatsapp_number" name="whatsapp_number"
                                value="{{ old('whatsapp_number', $member->whatsapp_number) }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]"
                                required>
                        </div>
                        <div>
                            <label for="instagram" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Instagram</label>
                            <input type="text" id="instagram" name="instagram"
                                value="{{ old('instagram', $member->instagram) }}"
                                placeholder="username tanpa @"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="birth_place" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Tempat Lahir</label>
                            <input type="text" id="birth_place" name="birth_place"
                                value="{{ old('birth_place', $member->birth_place) }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">
                        </div>
                        <div>
                            <label for="birthdate" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Tanggal Lahir</label>
                            <input type="date" id="birthdate" name="birthdate"
                                value="{{ old('birthdate', $member->birthdate?->format('Y-m-d')) }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label for="address" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Alamat Lengkap</label>
                        <textarea id="address" name="address" rows="3"
                            class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">{{ old('address', $member->address) }}</textarea>
                    </div>
                </div>

                <hr class="border-[color:var(--dash-card-border)]">

                <!-- Data Akademik -->
                <div>
                    <h3 class="text-lg font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-4">Data Akademik & Cabang</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Cabang MINDSIA</label>
                            <input type="text" value="{{ $member->branch->branch_name ?? '-' }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] bg-[var(--dash-nav-hover-bg)] shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed"
                                readonly>
                            <p class="text-xs text-[color:var(--dash-text-faint)] mt-1">Dikelola oleh admin.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Institusi Asal</label>
                            <input type="text" value="{{ $member->institution->institution_name ?? $member->institution_name_custom ?? '-' }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] bg-[var(--dash-nav-hover-bg)] shadow-sm sm:text-sm px-3 py-2 border cursor-not-allowed"
                                readonly>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="education_level" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Jenjang Pendidikan</label>
                            <select id="education_level" name="education_level"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">
                                <option value="">— Pilih jenjang —</option>
                                @foreach(['SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3','Umum'] as $level)
                                    <option value="{{ $level }}" @selected(old('education_level', $member->education_level) === $level)>{{ $level }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="class_or_faculty" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Jurusan atau Kelas</label>
                            <input type="text" id="class_or_faculty" name="class_or_faculty"
                                value="{{ old('class_or_faculty', $member->class_or_faculty) }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">
                        </div>
                    </div>
                </div>

                <hr class="border-[color:var(--dash-card-border)]">

                <!-- Data Orang Tua -->
                <div>
                    <h3 class="text-lg font-bold text-[color:var(--dash-text-heading)] font-jakarta mb-4">Data Orang Tua</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="father_name" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Nama Ayah</label>
                            <input type="text" id="father_name" name="father_name"
                                value="{{ old('father_name', $member->father_name) }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">
                        </div>
                        <div>
                            <label for="mother_name" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Nama Ibu</label>
                            <input type="text" id="mother_name" name="mother_name"
                                value="{{ old('mother_name', $member->mother_name) }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label for="father_whatsapp" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">WhatsApp Ayah</label>
                            <input type="text" id="father_whatsapp" name="father_whatsapp"
                                value="{{ old('father_whatsapp', $member->father_whatsapp) }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">
                        </div>
                        <div>
                            <label for="mother_whatsapp" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">WhatsApp Ibu</label>
                            <input type="text" id="mother_whatsapp" name="mother_whatsapp"
                                value="{{ old('mother_whatsapp', $member->mother_whatsapp) }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="father_occupation" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Pekerjaan Ayah</label>
                            <input type="text" id="father_occupation" name="father_occupation"
                                value="{{ old('father_occupation', $member->father_occupation) }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">
                        </div>
                        <div>
                            <label for="mother_occupation" class="block text-sm font-medium text-[color:var(--dash-text-body)] mb-1">Pekerjaan Ibu</label>
                            <input type="text" id="mother_occupation" name="mother_occupation"
                                value="{{ old('mother_occupation', $member->mother_occupation) }}"
                                class="w-full rounded-lg border-[color:var(--dash-input-border)] shadow-sm sm:text-sm px-3 py-2 border focus:border-[#5586DB] focus:ring-[#5586DB]">
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end pt-2 border-t border-[color:var(--dash-card-border)]">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-[#5586DB] rounded-lg hover:bg-[#4370BC] shadow-sm transition-colors">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
