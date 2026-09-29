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
<body class="w-full h-screen overflow-hidden bg-[#F8FAFC] text-[#111827] font-inter antialiased"
      x-data="memberRegisterPortal()" @input="checkValidities()" @change="checkValidities()">
    <div class="relative w-full h-full overflow-hidden">
        <!-- Left Cover -->
        <div class="absolute left-0 top-0 w-full flex flex-col items-center justify-center text-center overflow-hidden z-20 h-[15vh] md:h-full md:w-[40%] lg:w-[45%] bg-[#5586DB]">
            <div class="absolute inset-0 bg-gradient-to-br from-[#5586DB]/90 to-[#00AACC]/90"></div>
            <div class="absolute inset-0 animated-pattern pointer-events-none"></div>

            <div class="relative z-10 w-full flex flex-col items-center md:items-center">
                <div class="max-w-md px-10 text-left w-full">
                    <img src="/img/logo/mindsia-logo.webp" alt="Mindsia Logo" class="h-10 md:h-20 w-auto mb-2 md:mb-8 filter brightness-0 invert opacity-90 drop-shadow-md">
                    <h2 class="hidden md:block font-jakarta font-extrabold text-[36px] md:text-[48px] tracking-tight text-white mb-6 leading-[1.1]">Student <br><span class="text-[#F8FAFC]/90">Portal</span></h2>
                    <p class="hidden md:block text-[16px] text-white/80 leading-relaxed font-medium">Akses materi belajar, pantau progress akademik, dan kelola pendaftaran program MINDSIA Anda di sini.</p>
                    <h2 class="md:hidden font-jakarta font-extrabold text-[22px] tracking-tight text-white leading-tight">Student Portal</h2>
                </div>
            </div>

            <div class="hidden md:block absolute bottom-10 left-10 text-[12px] font-bold text-white/40 uppercase tracking-widest font-jakarta">
                Student Access Only
            </div>
        </div>

        <!-- Right Form -->
        <div class="absolute left-0 md:left-auto right-0 bottom-0 w-full md:w-[60%] lg:w-[55%] bg-white shadow-[-10px_0_30px_rgba(17,24,39,0.02)] z-30 flex flex-col h-[85vh] md:h-full">
            <div class="relative w-full h-full overflow-y-auto overflow-x-hidden p-6 md:p-16 lg:p-24 flex flex-col justify-start md:justify-center pt-20 md:pt-16">
                <!-- Back Button -->
                <a href="{{ route('member.landing') }}?transition=back"
                        class="absolute top-6 left-6 md:top-10 md:left-10 text-[#4B5563] hover:text-[#111827] transition-colors flex items-center gap-1.5 font-bold text-sm z-40">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span class="hidden md:inline">Kembali</span>
                </a>

                <div class="max-w-[520px] w-full mx-auto py-6">


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
                                   :class="inputClass(1, 'full_name')">
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
                                    :class="inputClass(1, 'gender')">
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
                            <input type="date" id="birthdate" name="birthdate"
                                   x-model="form.birthdate"
                                   class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                   :class="inputClass(1, 'birthdate')">
                            <p x-show="hasError(1, 'birthdate')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium" x-text="birthdateErrorText()"></p>
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
                                       value="{{ old('whatsapp_number') }}" placeholder="8xxxxxxxxxx"
                                       @input="$el.value = $el.value.replace(/[^0-9]/g, '').replace(/^0+/, ''); delete _uniquenessErrors['whatsapp_number']; checkValidities()"
                                       class="w-full px-3.5 py-2.5 border border-l-0 rounded-r-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                       :class="inputClass(1, 'whatsapp_number')" minlength="8" maxlength="15">
                            </div>
                            <p x-show="hasError(1, 'whatsapp_number')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium" x-text="whatsappErrorText()"></p>
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
                                   :class="inputClass(1, 'email')"
                                   @input="delete _uniquenessErrors['email']; checkValidities()">
                            <p x-show="hasError(1, 'email')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium" x-text="emailErrorText()"></p>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                                Provinsi Lahir <span class="text-red-600">*</span>
                            </label>
                            <div class="relative" @click.away="showIndoProvinceDropdown = false">
                                <div @click="showIndoProvinceDropdown = !showIndoProvinceDropdown"
                                     class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm flex justify-between items-center cursor-pointer"
                                     :class="hasError(1, 'birth_place_province_id') && showErrors1 ? 'border-red-500 bg-red-50' : 'border-[#E5E7EB]'">
                                    <span x-text="getSelectedIndoProvinceName()" class="truncate block pr-4"
                                          :class="form.birth_place_province_id ? 'text-[#111827]' : 'text-[#9CA3AF]'"></span>
                                    <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                                </div>

                                <div x-show="showIndoProvinceDropdown" x-transition x-cloak
                                     class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                    <div class="p-2 bg-gray-50 border-b border-gray-100">
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-[16px]">search</span>
                                            <input type="text" x-model="indoProvinceSearch"
                                                   placeholder="Cari provinsi..."
                                                   class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB] transition-all">
                                        </div>
                                    </div>
                                    <ul class="max-h-48 overflow-y-auto py-1">
                                        <li x-show="filteredIndoProvinces.length === 0" class="px-4 py-3 text-[12px] text-gray-500 text-center">Tidak ditemukan</li>
                                        <template x-for="p in filteredIndoProvinces" :key="p.id">
                                            <li @click="selectIndoProvince(p.id)"
                                                class="px-4 py-2 text-[12px] text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors"
                                                :class="form.birth_place_province_id == p.id ? 'bg-[#EFF6FF] font-semibold' : ''"
                                                x-text="p.nama"></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            <p x-show="hasError(1, 'birth_place_province_id')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            <input type="hidden" id="birth_place_province_id" name="birth_place_province_id" :value="form.birth_place_province_id">
                        </div>
                        <div>
                            <label class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                                Tempat Lahir <span class="text-red-600">*</span>
                            </label>
                            <div class="relative" @click.away="showIndoCityDropdown = false">
                                <div @click="if (form.birth_place_province_id) showIndoCityDropdown = !showIndoCityDropdown"
                                     class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm flex justify-between items-center cursor-pointer"
                                     :class="[!form.birth_place_province_id ? 'bg-gray-50 cursor-not-allowed' : '', hasError(1, 'birth_place') && showErrors1 ? 'border-red-500 bg-red-50' : 'border-[#E5E7EB]']">
                                    <span x-text="getSelectedIndoCityName()" class="truncate block pr-4"
                                          :class="form.birth_place ? 'text-[#111827]' : 'text-[#9CA3AF]'"></span>
                                    <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                                </div>

                                <div x-show="showIndoCityDropdown" x-transition x-cloak
                                     class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                    <div class="p-2 bg-gray-50 border-b border-gray-100">
                                        <div class="relative">
                                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-[16px]">search</span>
                                            <input type="text" x-model="indoCitySearch"
                                                   placeholder="Cari kota/kabupaten..."
                                                   class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB] transition-all">
                                        </div>
                                    </div>
                                    <ul class="max-h-48 overflow-y-auto py-1">
                                        <li x-show="indoCities.length === 0" class="px-4 py-3 text-[12px] text-gray-500 text-center">Pilih Provinsi terlebih dahulu</li>
                                        <li x-show="indoCities.length > 0 && filteredIndoCities.length === 0" class="px-4 py-3 text-[12px] text-gray-500 text-center">Tidak ditemukan</li>
                                        <template x-for="c in filteredIndoCities" :key="c.id">
                                            <li @click="selectIndoCity(c.nama)"
                                                class="px-4 py-2 text-[12px] text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors"
                                                :class="form.birth_place == c.nama ? 'bg-[#EFF6FF] font-semibold' : ''"
                                                x-text="c.nama"></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            <p x-show="hasError(1, 'birth_place')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            <input type="hidden" id="birth_place" name="birth_place" :value="form.birth_place">
                            @error('birth_place')
                                <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>



                    <div>
                        <label for="address" class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Alamat Lengkap <span class="text-red-600">*</span>
                        </label>
                        <textarea id="address" name="address" rows="2"
                                  class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm"
                                  :class="inputClass(1, 'address')">{{ old('address') }}</textarea>
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
                            Provinsi <span class="text-red-600">*</span>
                        </label>
                        <div class="relative" @click.away="showProvinceDropdown = false">
                            <div @click="showProvinceDropdown = !showProvinceDropdown"
                                 class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm flex justify-between items-center cursor-pointer"
                                 :class="hasError(2, 'province_id') && showErrors2 ? 'border-red-500 bg-red-50' : 'border-[#E5E7EB]'">
                                <span x-text="getSelectedProvinceName()" class="truncate block pr-4"
                                      :class="form.province_id ? 'text-[#111827]' : 'text-[#9CA3AF]'"></span>
                                <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                            </div>

                            <div x-show="showProvinceDropdown" x-transition x-cloak
                                 class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                <div class="p-2 bg-gray-50 border-b border-gray-100">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-[16px]">search</span>
                                        <input type="text" x-model="provinceSearch"
                                               placeholder="Cari provinsi..."
                                               class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB] transition-all">
                                    </div>
                                </div>
                                <ul class="max-h-48 overflow-y-auto py-1">
                                    <li x-show="filteredProvinces.length === 0" class="px-4 py-3 text-[12px] text-gray-500 text-center">Tidak ditemukan</li>
                                    <template x-for="p in filteredProvinces" :key="p.id">
                                        <li @click="selectProvince(p.id)"
                                            class="px-4 py-2 text-[12px] text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors"
                                            :class="form.province_id == p.id ? 'bg-[#EFF6FF] font-semibold' : ''"
                                            x-text="p.name"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <p x-show="hasError(2, 'province_id')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                        <input type="hidden" id="province_id" name="province_id" :value="form.province_id">
                    </div>

                    <div>
                        <label class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            Cabang MINDSIA <span class="text-red-600">*</span>
                        </label>
                        <div class="relative" @click.away="showBranchDropdown = false">
                            <div @click="showBranchDropdown = !showBranchDropdown"
                                 class="w-full px-3.5 py-2.5 bg-white border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm flex justify-between items-center cursor-pointer"
                                 :class="hasError(2, 'branch_id') && showErrors2 ? 'border-red-500 bg-red-50' : 'border-[#E5E7EB]'">
                                <span x-text="getSelectedBranchName()" class="truncate block pr-4"
                                      :class="form.branch_id ? 'text-[#111827]' : 'text-[#9CA3AF]'"></span>
                                <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                            </div>

                            <div x-show="showBranchDropdown" x-transition x-cloak
                                 class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                <div class="p-2 bg-gray-50 border-b border-gray-100">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-gray-400 text-[16px]">search</span>
                                        <input type="text" x-model="branchSearch"
                                               placeholder="Cari cabang..."
                                               class="w-full pl-9 pr-3 py-2 bg-white border border-gray-200 rounded-lg text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB] transition-all">
                                    </div>
                                </div>
                                <ul class="max-h-48 overflow-y-auto py-1">
                                    <li x-show="branches.length === 0" class="px-4 py-3 text-[12px] text-gray-500 text-center">Pilih Provinsi terlebih dahulu</li>
                                    <li x-show="branches.length > 0 && filteredBranches.length === 0" class="px-4 py-3 text-[12px] text-gray-500 text-center">Tidak ditemukan</li>
                                    <template x-for="b in filteredBranches" :key="b.id">
                                        <li @click="selectBranch(b.id)"
                                            class="px-4 py-2 text-[12px] text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors"
                                            :class="form.branch_id == b.id ? 'bg-[#EFF6FF] font-semibold' : ''"
                                            x-text="b.branch_name"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <p x-show="hasError(2, 'branch_id')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                        <input type="hidden" name="branch_id" :value="form.branch_id">
                        @error('branch_id')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenjang Pendidikan --}}
                    <div>
                        <label class="block mb-1 text-[12px] font-bold text-[#4B5563]">Jenjang Pendidikan <span class="text-[#9CA3AF] font-normal">(Opsional)</span></label>
                        <div class="relative" @click.away="showEducationDropdown = false">
                            <div @click="showEducationDropdown = !showEducationDropdown"
                                 class="w-full px-3.5 py-2.5 bg-white border border-[#E5E7EB] rounded-xl text-[13px] shadow-sm flex justify-between items-center cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#5586DB]/20 focus:border-[#5586DB] transition-all">
                                <span x-text="selectedEducationLabel" :class="form.education_level ? 'text-[#111827]' : 'text-[#9CA3AF]'" class="truncate block pr-4"></span>
                                <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                            </div>
                            <div x-show="showEducationDropdown" x-transition x-cloak
                                 class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden">
                                <ul class="max-h-56 overflow-y-auto py-1">
                                    <template x-for="group in educationOptions" :key="group.group">
                                        <li>
                                            <div class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-gray-50" x-text="group.group"></div>
                                            <template x-for="item in group.items" :key="item">
                                                <div @click="selectEducationLevel(item)"
                                                     class="px-4 py-2 text-[12px] text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer transition-colors"
                                                     :class="form.education_level === item ? 'bg-[#EFF6FF] font-semibold' : ''"
                                                     x-text="item"></div>
                                            </template>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" name="education_level" :value="form.education_level">
                    </div>

                    {{-- Kelas / Jurusan (kondisional) --}}
                    <div x-show="form.education_level && form.education_level !== 'Umum'" x-cloak>
                        <label class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                            <span x-text="classOrFacultyLabel"></span>
                            <span class="text-[#9CA3AF] font-normal ml-1">(Opsional)</span>
                        </label>
                        <input type="text" name="class_or_faculty" x-model="form.class_or_faculty"
                               :placeholder="classOrFacultyPlaceholder"
                               maxlength="100"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-[#5586DB]/20 focus:border-[#5586DB] transition-all shadow-sm">
                    </div>

                    {{-- Institusi (disembunyikan saat Umum) --}}
                    <div x-show="form.education_level !== 'Umum'" x-cloak>
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
                                               @keydown.enter.prevent
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
                        <p class="mt-2 text-[11px] text-[#6B7280] leading-relaxed">
                            Jika institusi tidak ditemukan, ketik <span class="font-semibold text-[#374151]">"tidak ditemukan"</span> pada kolom pencarian.<br>
                            Jika sudah lulus, tidak bersekolah, atau tidak berkuliah, pilih <span class="font-semibold text-[#374151]">"Tidak Sekolah/Sudah Lulus"</span>.
                        </p>
                        <div x-show="showCustomInstitution" x-cloak class="mt-3">
                            <label class="block mb-1 text-[12px] font-bold text-[#4B5563]">
                                Nama Institusimu <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="institution_name_custom" name="institution_name_custom"
                                   placeholder="Tulis nama sekolah/kampusmu..."
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#E5E7EB] rounded-xl text-[13px] focus:outline-none focus:ring-2 focus:ring-[#5586DB]/20 focus:border-[#5586DB] transition-all shadow-sm">
                        </div>
                        <input type="hidden" id="institution_id" name="institution_id" x-model="form.institution_id">
                        <p x-show="hasError(2, 'institution_id')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                        @error('institution_id')
                            <p class="mt-1 text-[11px] text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Badge Umum --}}
                    <div x-show="form.education_level === 'Umum'" x-cloak>
                        <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl">
                            <span class="material-symbols-outlined text-[20px] text-blue-500 shrink-0">check_circle</span>
                            <div>
                                <p class="text-[13px] font-medium text-gray-900">Tidak Kuliah/Sekolah</p>
                                <p class="text-[11px] text-gray-400">Dipilih otomatis untuk jenjang Umum</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-[#E5E7EB] pt-5">
                        <h3 class="font-jakarta font-bold text-[14px] text-[#111827] mb-4">Data Orang Tua</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="father_name" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Nama Ayah <span class="text-red-600">*</span></label>
                                <input type="text" id="father_name" name="father_name" value="{{ old('father_name') }}"
                                       class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm"
                                       :class="hasError(2,'father_name') ? 'border-red-500 bg-red-50' : 'bg-[#F8FAFC] border-[#E5E7EB]'">
                                <p x-show="hasError(2,'father_name')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            </div>
                            <div>
                                <label for="mother_name" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Nama Ibu <span class="text-red-600">*</span></label>
                                <input type="text" id="mother_name" name="mother_name" value="{{ old('mother_name') }}"
                                       class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm"
                                       :class="hasError(2,'mother_name') ? 'border-red-500 bg-red-50' : 'bg-[#F8FAFC] border-[#E5E7EB]'">
                                <p x-show="hasError(2,'mother_name')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            </div>
                            <div>
                                <label for="father_occupation" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Pekerjaan Ayah <span class="text-red-600">*</span></label>
                                <input type="text" id="father_occupation" name="father_occupation" value="{{ old('father_occupation') }}"
                                       class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm"
                                       :class="hasError(2,'father_occupation') ? 'border-red-500 bg-red-50' : 'bg-[#F8FAFC] border-[#E5E7EB]'">
                                <p x-show="hasError(2,'father_occupation')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            </div>
                            <div>
                                <label for="mother_occupation" class="block mb-1 text-[12px] font-bold text-[#4B5563]">Pekerjaan Ibu <span class="text-red-600">*</span></label>
                                <input type="text" id="mother_occupation" name="mother_occupation" value="{{ old('mother_occupation') }}"
                                       class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm"
                                       :class="hasError(2,'mother_occupation') ? 'border-red-500 bg-red-50' : 'bg-[#F8FAFC] border-[#E5E7EB]'">
                                <p x-show="hasError(2,'mother_occupation')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            </div>
                            <div>
                                <label for="father_whatsapp" class="block mb-1 text-[12px] font-bold text-[#4B5563]">WhatsApp Ayah <span class="text-red-600">*</span></label>
                                <input type="text" id="father_whatsapp" name="father_whatsapp" value="{{ old('father_whatsapp') }}"
                                       class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm"
                                       :class="hasError(2,'father_whatsapp') ? 'border-red-500 bg-red-50' : 'bg-[#F8FAFC] border-[#E5E7EB]'" maxlength="20">
                                <p x-show="hasError(2,'father_whatsapp')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
                            </div>
                            <div>
                                <label for="mother_whatsapp" class="block mb-1 text-[12px] font-bold text-[#4B5563]">WhatsApp Ibu <span class="text-red-600">*</span></label>
                                <input type="text" id="mother_whatsapp" name="mother_whatsapp" value="{{ old('mother_whatsapp') }}"
                                       class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:border-[#5586DB] focus:ring-2 focus:ring-[#5586DB]/20 transition-all shadow-sm"
                                       :class="hasError(2,'mother_whatsapp') ? 'border-red-500 bg-red-50' : 'bg-[#F8FAFC] border-[#E5E7EB]'" maxlength="20">
                                <p x-show="hasError(2,'mother_whatsapp')" x-cloak class="mt-1 text-[11px] text-red-600 font-medium">Bagian ini wajib diisi.</p>
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
                                   x-model="regPassword"
                                   class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm pr-10"
                                   :class="inputClass(3, 'password')">
                            <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
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
                                   x-model="regPasswordConfirm"
                                   class="w-full px-3.5 py-2.5 border rounded-xl text-[13px] focus:outline-none focus:ring-2 transition-all shadow-sm pr-10"
                                   :class="inputClass(3, 'password_confirmation')">
                            <button type="button" onclick="const type = this.previousElementSibling.type === 'password' ? 'text' : 'password'; document.querySelectorAll('input[type=password], input[data-is-pwd]').forEach(i => { i.type = type; i.setAttribute('data-is-pwd', '1'); }); document.querySelectorAll('button.pwd-toggle').forEach(b => { if(b.innerHTML.includes('material-symbols-outlined')) { b.innerHTML = type === 'password' ? '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility</span>' : '<span class=\'material-symbols-outlined text-[18px] text-gray-500\'>visibility_off</span>'; } else { const eye = b.querySelector('.eye'); const eyeOff = b.querySelector('.eye-off'); if(eye && eyeOff) { eye.classList.toggle('hidden', type === 'text'); eyeOff.classList.toggle('hidden', type === 'password'); } } });" class="absolute inset-y-0 right-0 pr-3 flex items-center pwd-toggle pwd-toggle">
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
                        <button type="button"
                                @click="showErrors1 = true; showErrors2 = true; showErrors3 = true; if (step1Valid && step2Valid && step3Valid) $el.closest('form').submit();"
                                class="flex-1 ml-4 bg-[#5586DB] text-white font-bold py-2.5 px-4 rounded-xl hover:bg-[#00AACC] transition-all shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] flex justify-center items-center gap-2">
                            Daftar <span class="material-symbols-outlined text-[16px]">person_add</span>
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-8 text-center border-t border-[#E5E7EB] pt-6 pb-4">
                <p class="text-[14px] text-[#4B5563]">
                    Sudah punya akun?
                    <a href="{{ route('member.landing', ['view' => 'login', 'transition' => 'back']) }}" class="font-bold text-[#5586DB] hover:text-[#00AACC] transition-colors">Masuk di sini</a>
                </p>
            </div>
            </div>
        </div>
    </div>

    @php
        $errorStep = $errors->hasAny(['password','password_confirmation']) ? 3
            : ($errors->hasAny(['branch_id','institution_id','institution_name_custom','father_name','mother_name','father_occupation','mother_occupation','father_whatsapp','mother_whatsapp']) ? 2
            : ($errors->any() ? 1 : 0));
    @endphp
    <script>
        window.__memberRegisterConfig = {
            provinces: @json($provinces ?? []),
            old: {
                province_id: @json(old('province_id', '')),
                branch_id: @json(old('branch_id', '')),
                institution_id: @json(old('institution_id', '')),
                birthdate: @json(old('birthdate', '')),
                birth_place_province_id: @json(old('birth_place_province_id', '')),
                birth_place: @json(old('birth_place', '')),
                education_level: @json(old('education_level', '')),
                class_or_faculty: @json(old('class_or_faculty', '')),
            },
            errorStep: @json($errorStep),
        };
        // memberRegisterPortal() defined in resources/js/member-register.js
    </script>
</body>
</html>
