@extends('layouts.dashboard')

@section('title', 'Registrasi Member Baru - MINDSIA')
@section('header_title', 'Registrasi Member Baru')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('members.index') }}" class="dash-back mb-3 inline-flex items-center gap-1.5 text-xs font-semibold">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Data Member
        </a>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Registrasi Member Baru</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Lengkapi data identitas, institusi pendidikan, dan program keanggotaan baru.</p>
        </div>
    </div>

    {{-- Error validation banner --}}
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <div class="flex items-center gap-2 mb-2 font-semibold">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>Terdapat kesalahan pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('members.store') }}" method="POST" id="memberForm"
          class="rounded-2xl shadow-sm"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @csrf

        {{-- Section 1: Data Pribadi --}}
        <div class="px-6 py-4 rounded-t-2xl border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Data Pribadi</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Informasi identitas lengkap calon member</p>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                       placeholder="Nama lengkap sesuai identitas" />
                @error('full_name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select name="gender" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Tempat Lahir <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="birth_place" value="{{ old('birth_place') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                           placeholder="Kota kelahiran" />
                    @error('birth_place') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('birthdate') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                           placeholder="email@contoh.com" />
                    @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Nomor WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center rounded-l-lg border border-r-0 px-3 text-sm font-bold"
                              style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-input-border); color: var(--dash-text-muted);">+62</span>
                        <input type="tel" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required
                               placeholder="8xxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                               class="w-full min-w-0 flex-1 rounded-r-lg px-4 py-2.5 text-sm focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                               minlength="8" maxlength="15" />
                    </div>
                    @error('whatsapp_number') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Instagram <span style="color: var(--dash-text-faint);">(Opsional)</span>
                    </label>
                    <input type="text" name="instagram" value="{{ old('instagram') }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                           placeholder="@username" />
                    @error('instagram') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div></div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Alamat Domisili <span class="text-red-500">*</span>
                </label>
                <textarea name="address" rows="2" required
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('address') }}</textarea>
                @error('address') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Section 2: Data Orang Tua --}}
        <div class="px-6 py-4 border-b border-t"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Data Orang Tua / Wali</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Informasi kontak dan pekerjaan orang tua</p>
        </div>

        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Nama Ayah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="father_name" value="{{ old('father_name') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('father_name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Pekerjaan Ayah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="father_occupation" value="{{ old('father_occupation') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('father_occupation') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Nama Ibu <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="mother_name" value="{{ old('mother_name') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('mother_name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Pekerjaan Ibu <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="mother_occupation" value="{{ old('mother_occupation') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('mother_occupation') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        WhatsApp Ayah <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="father_whatsapp" value="{{ old('father_whatsapp') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                           placeholder="08xxxxxxxxxx" minlength="8" maxlength="15" />
                    @error('father_whatsapp') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        WhatsApp Ibu <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="mother_whatsapp" value="{{ old('mother_whatsapp') }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                           placeholder="08xxxxxxxxxx" minlength="8" maxlength="15" />
                    @error('mother_whatsapp') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Cabang & Institusi --}}
        <div class="px-6 py-4 border-b border-t"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Cabang & Institusi</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Cabang MINDSIA dan asal sekolah/kampus member</p>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Cabang <span class="text-red-500">*</span>
                </label>
                <select name="branch_id" required
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Pilih Cabang</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>
                @error('branch_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Sekolah / Institusi <span class="text-red-500">*</span>
                </label>
                <select name="institution_id"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Pilih dari daftar (atau isi manual di bawah)</option>
                    @foreach(\App\Models\Institution::orderBy('institution_name')->get() as $inst)
                        <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->institution_name }}</option>
                    @endforeach
                </select>
                @error('institution_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Atau ketik nama sekolah <span style="color: var(--dash-text-faint);">(jika tidak ada di daftar)</span>
                </label>
                <input type="text" name="institution_name_custom" value="{{ old('institution_name_custom') }}"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                       placeholder="Nama sekolah lainnya" />
                @error('institution_name_custom') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Jenjang Pendidikan <span style="color: var(--dash-text-faint);">(Opsional)</span>
                    </label>
                    <select name="education_level"
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Jenjang</option>
                        @foreach(['SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3','Umum'] as $level)
                            <option value="{{ $level }}" {{ old('education_level') === $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                    @error('education_level') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Kelas / Jurusan <span style="color: var(--dash-text-faint);">(Opsional)</span>
                    </label>
                    <input type="text" name="class_or_faculty" value="{{ old('class_or_faculty') }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                           placeholder="Contoh: XII IPA 3 / Teknik Informatika" />
                    @error('class_or_faculty') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section 4: Program & Referral --}}
        <div class="px-6 py-4 border-b border-t"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Program & Referral</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Pilihan program bimbingan dan referensi marketer</p>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Program <span style="color: var(--dash-text-faint);">(Opsional)</span>
                </label>
                <select name="program_id"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Pilih Program</option>
                    @foreach(\App\Models\Program::where('is_active', true)->orderBy('program_name')->get() as $prog)
                        <option value="{{ $prog->id }}" {{ old('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->program_name }}</option>
                    @endforeach
                </select>
                @error('program_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Referral Marketer <span style="color: var(--dash-text-faint);">(Opsional)</span>
                </label>
                <select name="referred_by_employee_id"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Tidak Ada</option>
                    @foreach(\App\Models\Employee::orderBy('full_name')->get() as $emp)
                        <option value="{{ $emp->id }}" {{ old('referred_by_employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('referred_by_employee_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Section 5: Akun Login --}}
        <div class="px-6 py-4 border-b border-t"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Akun Login Member</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Opsional - buatkan akun agar member bisa login ke portal</p>
        </div>

        <div class="p-6 space-y-5">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="create_account" value="1" id="createAccountCheckbox"
                       class="mt-0.5 rounded focus:ring-0"
                       style="accent-color: var(--dash-primary);"
                       {{ old('create_account') ? 'checked' : '' }} />
                <div>
                    <span class="text-sm font-semibold block" style="color: var(--dash-text-heading);">Buat akun login portal member</span>
                    <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Email verifikasi dan instruksi akses akan dikirim otomatis ke alamat email member.</p>
                </div>
            </label>

            <div id="accountInfoPanel" class="hidden rounded-xl p-4 border"
                 style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">info</span>
                    <div class="text-xs" style="color: var(--dash-text-body);">
                        <p class="font-semibold text-sm mb-1" style="color: var(--dash-text-heading);">Akun dibuat secara otomatis</p>
                        <p>Password sementara di-generate oleh sistem dan tautan verifikasi dikirim ke email terdaftar. Member wajib melakukan verifikasi dan memperbarui password saat login pertama kali.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions Footer --}}
        <div class="px-6 py-4 rounded-b-2xl flex items-center justify-end gap-3 border-t"
             style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
            <a href="{{ route('members.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                Daftar Member
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox = document.getElementById('createAccountCheckbox');
        const panel = document.getElementById('accountInfoPanel');
        if (checkbox && panel) {
            checkbox.addEventListener('change', () => panel.classList.toggle('hidden', !checkbox.checked));
            panel.classList.toggle('hidden', !checkbox.checked);
        }
    });
</script>
@endsection
