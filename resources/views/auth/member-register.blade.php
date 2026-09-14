<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Student Portal - MINDSIA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0&display=swap" rel="stylesheet"></noscript>
    <style>
        .font-jakarta { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-inter { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="w-full flex flex-col md:flex-row bg-[#F8FAFC] text-[#111827] font-inter antialiased min-h-screen"
      x-data="memberRegisterPortal()" @input="checkValidities()" @change="checkValidities()">

    <!-- Left Cover -->
    <div class="hidden md:flex md:w-[45%] lg:w-1/2 flex-col items-center justify-center text-center relative overflow-hidden bg-[#5586DB] sticky top-0 h-screen">
        <div class="absolute inset-0 bg-gradient-to-br from-[#5586DB]/90 to-[#00AACC]/90"></div>

        <div class="relative z-10 max-w-md px-10 text-left">
            <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-20 w-auto mb-8 ml-10 filter brightness-0 invert opacity-90">
            <h2 class="font-jakarta font-extrabold text-[36px] md:text-[48px] tracking-tight text-white mb-6 leading-[1.1]">Student <br><span class="text-[#F8FAFC]/90">Portal</span></h2>
            <p class="text-[16px] text-white/80 leading-relaxed font-medium">Akses materi belajar, pantau progress akademik, dan kelola pendaftaran program MINDSIA Anda di sini.</p>
        </div>

        <div class="absolute bottom-10 left-10 text-[12px] font-bold text-white/50 uppercase tracking-widest font-jakarta">
            Student Access Only
        </div>
    </div>

    <!-- Right Form -->
    <div class="w-full md:w-[55%] lg:w-1/2 flex flex-col justify-start p-8 md:p-16 lg:p-20 overflow-y-auto bg-white relative shadow-[-10px_0_30px_rgba(17,24,39,0.02)]">
        <div class="max-w-[520px] w-full mx-auto py-6">
            <div class="md:hidden mb-8">
                <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-12 w-auto">
            </div>


            <h1 class="font-jakarta font-extrabold text-[28px] md:text-[32px] text-[#111827] mb-2 tracking-tight">Pendaftaran Member</h1>
            <p class="text-[15px] text-[#4B5563] mb-8 font-medium">Isi form di bawah untuk membuat akun baru.</p>

            <!-- Progress Indicator -->
            <div class="flex justify-between items-center mb-8 relative max-w-[80%]">
                <div class="absolute left-0 top-4 w-full h-1 bg-gray-200 rounded-full z-0"></div>
                <div class="absolute left-0 top-4 h-1 bg-[#5586DB] rounded-full z-0 transition-all duration-300" :style="'width: ' + ((step - 1) * 50) + '%'"></div>

                <div class="relative z-10 flex flex-col items-center gap-2 cursor-pointer" @click="step = 1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition-colors" :class="step >= 1 ? 'bg-[#5586DB] text-white shadow-md' : 'bg-gray-200 text-gray-400'">1</div>
                    <span class="text-[10px] font-bold uppercase tracking-wider transition-colors" :class="step >= 1 ? 'text-[#5586DB]' : 'text-gray-400'">Personal</span>
                </div>

                <div class="relative z-10 flex flex-col items-center gap-2 cursor-pointer" @click="step = 2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition-colors" :class="step >= 2 ? 'bg-[#5586DB] text-white shadow-md' : 'bg-gray-200 text-gray-400'">2</div>
                    <span class="text-[10px] font-bold uppercase tracking-wider transition-colors" :class="step >= 2 ? 'text-[#5586DB]' : 'text-gray-400'">Akademik</span>
                </div>

                <div class="relative z-10 flex flex-col items-center gap-2 cursor-pointer" @click="step = 3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition-colors" :class="step >= 3 ? 'bg-[#5586DB] text-white shadow-md' : 'bg-gray-200 text-gray-400'">3</div>
                    <span class="text-[10px] font-bold uppercase tracking-wider transition-colors" :class="step >= 3 ? 'text-[#5586DB]' : 'text-gray-400'">Akun</span>
                </div>
            </div>

            <form action="{{ route('member.register.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Step 1: Data Personal -->
                <div x-show="step === 1"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="space-y-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="full_name" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                                Nama Lengkap <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}"
                                   class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                   :class="inputClass(1, 'full_name')" required>
                            <p x-show="hasError(1, 'full_name')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            @error('full_name')
                                <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="gender" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                                Jenis Kelamin <span class="text-red-600">*</span>
                            </label>
                            <select id="gender" name="gender"
                                    class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                    :class="inputClass(1, 'gender')" required>
                                <option value="">Pilih...</option>
                                <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            <p x-show="hasError(1, 'gender')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            @error('gender')
                                <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="birthdate" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                                Tanggal Lahir <span class="text-red-600">*</span>
                            </label>
                            <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate') }}"
                                   max="{{ \Carbon\Carbon::now()->subYears(5)->format('Y-m-d') }}"
                                   class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                   :class="inputClass(1, 'birthdate')" required>
                            <p x-show="hasError(1, 'birthdate')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            @error('birthdate')
                                <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="whatsapp_number" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                                No. WhatsApp <span class="text-red-600">*</span>
                            </label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3.5 text-[13px] text-[#4B5563] bg-[#E5E7EB] border border-[#E5E7EB] rounded-l-xl font-bold">+62</span>
                                <input type="text" id="whatsapp_number" name="whatsapp_number"
                                       value="{{ old('whatsapp_number') }}" placeholder="8xxxxxxxxxx" maxlength="13"
                                       @input="$el.value = $el.value.replace(/[^0-9]/g, '').replace(/^0+/, ''); checkValidities()"
                                       class="w-full px-3.5 py-2.5 border border-l-0 rounded-r-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                       :class="inputClass(1, 'whatsapp_number')" required>
                            </div>
                            <p x-show="hasError(1, 'whatsapp_number')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Minimal 9 digit.</p>
                            @error('whatsapp_number')
                                <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                                Email <span class="text-red-600">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                   :class="inputClass(1, 'email')" required>
                            <p x-show="hasError(1, 'email')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            @error('email')
                                <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="instagram" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Instagram</label>
                            <input type="text" id="instagram" name="instagram" value="{{ old('instagram') }}"
                                   placeholder="@username"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-[#5586DB]/20 focus:border-[#5586DB] transition-all shadow-sm">
                            @error('instagram')
                                <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="province_id" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Provinsi <span class="text-red-600">*</span>
                        </label>
                        <select id="province_id" name="province_id"
                                x-model="form.province_id"
                                @change="fetchInstitutions(); checkValidities()"
                                class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                :class="inputClass(1, 'province_id')" required>
                            <option value="">Pilih Provinsi</option>
                            <template x-for="prov in provinces" :key="prov.id">
                                <option :value="prov.id" x-text="prov.name"></option>
                            </template>
                        </select>
                        <p x-show="hasError(1, 'province_id')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                    </div>

                    <div>
                        <label for="address" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Alamat Lengkap <span class="text-red-600">*</span>
                        </label>
                        <textarea id="address" name="address" rows="2"
                                  class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                  :class="inputClass(1, 'address')" required>{{ old('address') }}</textarea>
                        <p x-show="hasError(1, 'address')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                        @error('address')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="button" @click.prevent="nextStep(1)"
                                class="font-bold py-2.5 px-6 rounded-xl transition-all flex justify-center items-center gap-2"
                                :class="step1Valid ? 'bg-[#5586DB] text-white hover:bg-[#00AACC] shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)]' : 'bg-gray-200 text-gray-500'">
                            Lanjut <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Data Akademik -->
                <div x-cloak x-show="step === 2"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="space-y-4">

                    <div>
                        <label class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Institusi <span class="text-red-600">*</span>
                        </label>
                        <div class="relative" @click.away="showInstitutionDropdown = false">
                            <div @click="showInstitutionDropdown = !showInstitutionDropdown"
                                 class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm flex justify-between items-center cursor-pointer"
                                 :class="inputClass(2, 'institution_id')">
                                <span x-text="getSelectedInstitutionName()" class="truncate block pr-4"></span>
                                <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                            </div>

                            <div x-show="showInstitutionDropdown" x-transition x-cloak
                                 class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                <div class="p-2 bg-gray-50 border-b border-gray-100">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-[16px]">search</span>
                                        <input type="text" x-model="institutionSearch"
                                               placeholder="Ketik nama sekolah/kampus..."
                                               class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB] transition-all">
                                    </div>
                                </div>
                                <ul class="max-h-56 overflow-y-auto py-1">
                                    <li x-show="institutions.length === 0" class="px-4 py-3 text-[12px] text-gray-500 text-center">Pilih Provinsi terlebih dahulu</li>
                                    <li x-show="institutions.length > 0 && filteredInstitutions.length === 0" class="px-4 py-3 text-[12px] text-gray-500 text-center">Tidak ditemukan</li>
                                    <template x-for="inst in filteredInstitutions" :key="inst.id">
                                        <li @click="selectInstitution(inst.id)"
                                            class="px-4 py-2 text-[12px] text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors"
                                            x-text="inst.institution_name"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" id="institution_id" name="institution_id" x-model="form.institution_id">
                        <p x-show="hasError(2, 'institution_id')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                        @error('institution_id')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="referred_by_code" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Kode Referral <span class="text-[#9CA3AF] font-normal">(Opsional)</span>
                        </label>
                        <input type="text" id="referred_by_code" name="referred_by_code"
                               value="{{ old('referred_by_code') }}"
                               placeholder="Kode karyawan yang mereferensikan Anda"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm">
                        @error('referred_by_code')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t border-[#E5E7EB] pt-5">
                        <h3 class="font-jakarta font-bold text-[14px] text-[#111827] mb-4">Data Orang Tua <span class="text-[#9CA3AF] font-normal">(Opsional)</span></h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="father_name" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Nama Ayah</label>
                                <input type="text" id="father_name" name="father_name" value="{{ old('father_name') }}"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm">
                            </div>
                            <div>
                                <label for="mother_name" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Nama Ibu</label>
                                <input type="text" id="mother_name" name="mother_name" value="{{ old('mother_name') }}"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm">
                            </div>
                            <div>
                                <label for="father_occupation" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Pekerjaan Ayah</label>
                                <input type="text" id="father_occupation" name="father_occupation" value="{{ old('father_occupation') }}"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm">
                            </div>
                            <div>
                                <label for="mother_occupation" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Pekerjaan Ibu</label>
                                <input type="text" id="mother_occupation" name="mother_occupation" value="{{ old('mother_occupation') }}"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm">
                            </div>
                            <div>
                                <label for="father_whatsapp" class="block mb-1 text-[12px] font-bold text-[#4B5563]">WhatsApp Ayah</label>
                                <input type="text" id="father_whatsapp" name="father_whatsapp" value="{{ old('father_whatsapp') }}"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm">
                            </div>
                            <div>
                                <label for="mother_whatsapp" class="block mb-1 text-[12px] font-bold text-[#4B5563]">WhatsApp Ibu</label>
                                <input type="text" id="mother_whatsapp" name="mother_whatsapp" value="{{ old('mother_whatsapp') }}"
                                       class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between pt-4">
                        <button type="button" @click="step = 1"
                                class="bg-gray-100 text-[#4B5563] font-bold py-2.5 px-5 rounded-xl hover:bg-gray-200 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
                        </button>
                        <button type="button" @click.prevent="nextStep(2)"
                                class="font-bold py-2.5 px-6 rounded-xl transition-all flex items-center gap-2"
                                :class="step2Valid ? 'bg-[#5586DB] text-white hover:bg-[#00AACC] shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)]' : 'bg-gray-200 text-gray-500'">
                            Lanjut <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Akun -->
                <div x-cloak x-show="step === 3"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="space-y-4">

                    <div>
                        <label for="password" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Password <span class="text-red-600">*</span>
                        </label>
                        <div class="relative w-full">
                            <input type="password" id="password" name="password"
                                   class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm pr-10"
                                   :class="inputClass(3, 'password')" required>
                            <button type="button" onclick="const input = this.previousElementSibling; input.type = input.type === 'password' ? 'text' : 'password'; this.innerHTML = input.type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>';" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                            </button>
                        </div>
                        <p x-show="hasError(3, 'password')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium" x-text="passwordErrorText()"></p>
                        @error('password')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-[11px] text-gray-500">Minimal 8 karakter, mengandung huruf besar, angka, dan simbol.</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Konfirmasi Password <span class="text-red-600">*</span>
                        </label>
                        <div class="relative w-full">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm pr-10"
                                   :class="inputClass(3, 'password_confirmation')" required>
                            <button type="button" onclick="const input = this.previousElementSibling; input.type = input.type === 'password' ? 'text' : 'password'; this.innerHTML = input.type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>';" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <span class="material-symbols-outlined text-[18px] text-gray-500">visibility</span>
                            </button>
                        </div>
                        <p x-show="hasError(3, 'password_confirmation')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium" x-text="passwordConfirmErrorText()"></p>
                        @error('password_confirmation')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-between pt-4">
                        <button type="button" @click="step = 2"
                                class="bg-gray-100 text-[#4B5563] font-bold py-2.5 px-5 rounded-xl hover:bg-gray-200 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
                        </button>
                        <button type="submit"
                                class="flex-1 ml-4 bg-[#5586DB] text-white font-bold py-2.5 px-4 rounded-xl hover:bg-[#00AACC] transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] flex justify-center items-center gap-2">
                            Daftar <span class="material-symbols-outlined text-[16px]">person_add</span>
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-8 text-center border-t border-[#E5E7EB] pt-6 pb-4">
                <p class="text-[14px] text-[#4B5563]">
                    Sudah punya akun?
                    <a href="{{ route('member.login') }}" class="font-bold text-[#5586DB] hover:text-[#00AACC] transition-colors">Masuk</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        window.__memberRegisterConfig = { provinces: @json($provinces ?? []) };
        // memberRegisterPortal() defined in resources/js/member-register.js
    </script>
</body>
</html>
