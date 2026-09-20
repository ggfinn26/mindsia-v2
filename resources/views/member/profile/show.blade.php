@extends('layouts.dashboard')

@section('title', 'Profile Saya - MINDSIA')
@section('header_title', 'Profile Saya')

@section('content')
<div class="max-w-4xl mx-auto">
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-start gap-3">
            <span class="material-symbols-outlined text-green-500 mt-0.5">check_circle</span>
            <div>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
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

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <form action="{{ route('member.profile.avatar') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-center gap-6">
                <div class="h-24 w-24 rounded-full bg-[#EEF4FF] flex items-center justify-center overflow-hidden border-2 border-gray-100 shrink-0">
                    @if($member->telegram_photo_id)
                        <img src="{{ $member->telegram_photo_id }}" alt="Avatar" class="h-full w-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-[#5586DB] text-5xl">account_circle</span>
                    @endif
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-1">Foto Profil</h3>
                    <p class="text-xs text-gray-500 mb-3">Maksimal 2MB. Format: JPG, PNG.</p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center sm:justify-start">
                        <input type="file" name="avatar" id="avatar" accept="image/*" class="w-full sm:w-auto text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-300 rounded-lg px-3 py-2 bg-white" required>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 shadow-sm whitespace-nowrap">
                            Upload Foto
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('member.profile.update') }}" method="POST"
              x-data="programRegistration({
                  needsBranch: false,
                  needsBirth: true,
                  memberBirthProvinceId: @json(old('birth_place_province_id', $member->birth_place_province_id ?? '')),
                  memberBirthPlace: @json(old('birth_place', $member->birth_place ?? '')),
                  educationLevel: @json(old('education_level', $member->education_level ?? '')),
                  classOrFaculty: @json(old('class_or_faculty', $member->class_or_faculty ?? '')),
              })">
            @csrf
            @method('PUT')

            <div class="p-6 sm:p-8">
                <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Informasi Pribadi</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $member->full_name) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border" required>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $member->email) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                        <p class="mt-1 text-xs text-gray-500">Jika diubah, Anda harus memverifikasi email ulang.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi Lahir</label>
                        <div class="relative" @click.away="showBirthProvinceDropdown = false">
                            <div @click="showBirthProvinceDropdown = !showBirthProvinceDropdown"
                                 class="w-full px-3 py-2 bg-white border rounded-lg sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#5586DB] transition-all shadow-sm flex justify-between items-center cursor-pointer border-gray-300">
                                <span x-text="selectedBirthProvinceName" class="truncate block pr-4"
                                      :class="birthProvinceId ? 'text-gray-900' : 'text-gray-500'"></span>
                                <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                            </div>
                            <div x-show="showBirthProvinceDropdown" x-transition x-cloak
                                 class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                                <div class="p-2 bg-gray-50 border-b border-gray-100">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-2 text-gray-400 text-[16px]">search</span>
                                        <input type="text" x-model="birthProvinceSearch" placeholder="Cari provinsi..."
                                               class="w-full pl-9 pr-3 py-1.5 bg-white border border-gray-200 rounded-md sm:text-sm focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB]">
                                    </div>
                                </div>
                                <ul class="max-h-48 overflow-y-auto py-1">
                                    <li x-show="filteredBirthProvinces.length === 0" class="px-4 py-2 sm:text-sm text-gray-500 text-center">Tidak ditemukan</li>
                                    <template x-for="p in filteredBirthProvinces" :key="p.id">
                                        <li @click="selectBirthProvince(p.id)" class="px-4 py-2 sm:text-sm text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer"
                                            :class="birthProvinceId == p.id ? 'bg-[#EFF6FF] font-semibold' : ''" x-text="p.nama"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" name="birth_place_province_id" :value="birthProvinceId">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <div class="relative" @click.away="showBirthPlaceDropdown = false">
                            <div @click="if (birthProvinceId) showBirthPlaceDropdown = !showBirthPlaceDropdown"
                                 class="w-full px-3 py-2 bg-white border rounded-lg sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#5586DB] transition-all shadow-sm flex justify-between items-center cursor-pointer border-gray-300"
                                 :class="!birthProvinceId ? 'opacity-50 cursor-not-allowed bg-gray-50' : ''">
                                <span x-text="selectedBirthPlaceName" class="truncate block pr-4"
                                      :class="birthPlace ? 'text-gray-900' : 'text-gray-500'"></span>
                                <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                            </div>
                            <div x-show="showBirthPlaceDropdown" x-transition x-cloak
                                 class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                                <div class="p-2 bg-gray-50 border-b border-gray-100">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-2 text-gray-400 text-[16px]">search</span>
                                        <input type="text" x-model="birthPlaceSearch" placeholder="Cari kota/kabupaten..."
                                               class="w-full pl-9 pr-3 py-1.5 bg-white border border-gray-200 rounded-md sm:text-sm focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB]">
                                    </div>
                                </div>
                                <ul class="max-h-48 overflow-y-auto py-1">
                                    <li x-show="indoCities.length === 0" class="px-4 py-2 sm:text-sm text-gray-500 text-center">Pilih Provinsi terlebih dahulu</li>
                                    <li x-show="indoCities.length > 0 && filteredBirthPlaces.length === 0" class="px-4 py-2 sm:text-sm text-gray-500 text-center">Tidak ditemukan</li>
                                    <template x-for="c in filteredBirthPlaces" :key="c.id">
                                        <li @click="selectBirthPlace(c.nama)" class="px-4 py-2 sm:text-sm text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer"
                                            :class="birthPlace == c.nama ? 'bg-[#EFF6FF] font-semibold' : ''" x-text="c.nama"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" name="birth_place" :value="birthPlace">
                    </div>
                    <div>
                        <label for="birthdate" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate', optional($member->birthdate)->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                    </div>
                </div>

                {{-- Jenjang Pendidikan --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang Pendidikan <span class="text-xs text-gray-400">(Opsional)</span></label>
                        <div class="relative" @click.away="showEducationDropdown = false">
                            <div @click="showEducationDropdown = !showEducationDropdown"
                                 class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg sm:text-sm shadow-sm flex justify-between items-center cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#5586DB]">
                                <span x-text="selectedEducationLabel" :class="educationLevel ? 'text-gray-900' : 'text-gray-500'" class="truncate block pr-4"></span>
                                <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                            </div>
                            <div x-show="showEducationDropdown" x-transition x-cloak
                                 class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                                <ul class="max-h-56 overflow-y-auto py-1">
                                    <template x-for="group in educationOptions" :key="group.group">
                                        <li>
                                            <div class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-gray-50" x-text="group.group"></div>
                                            <template x-for="item in group.items" :key="item">
                                                <div @click="selectEducationLevel(item)"
                                                     class="px-4 py-2 sm:text-sm text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer"
                                                     :class="educationLevel === item ? 'bg-[#EFF6FF] font-semibold' : ''"
                                                     x-text="item"></div>
                                            </template>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" name="education_level" :value="educationLevel">
                    </div>

                    <div x-show="educationLevel && educationLevel !== 'Umum'" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span x-text="classOrFacultyLabel"></span>
                            <span class="text-xs text-gray-400 ml-1">(Opsional)</span>
                        </label>
                        <input type="text" name="class_or_faculty" x-model="classOrFaculty"
                               :placeholder="classOrFacultyPlaceholder"
                               maxlength="100"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <div>
                        <label for="whatsapp_number" class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
                        <input type="text" name="whatsapp_number" id="whatsapp_number" value="{{ old('whatsapp_number', $member->whatsapp_number) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border" required>
                    </div>

                    <div>
                        <label for="instagram" class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                        <input type="text" name="instagram" id="instagram" value="{{ old('instagram', $member->instagram) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                    </div>
                </div>

                <div class="mb-6">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea name="address" id="address" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">{{ old('address', $member->address) }}</textarea>
                </div>

                <hr class="my-8 border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Data Orang Tua</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="father_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Ayah</label>
                        <input type="text" name="father_name" id="father_name" value="{{ old('father_name', $member->father_name) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Ibu</label>
                        <input type="text" name="mother_name" id="mother_name" value="{{ old('mother_name', $member->mother_name) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label for="father_whatsapp" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Ayah</label>
                        <input type="text" name="father_whatsapp" id="father_whatsapp" value="{{ old('father_whatsapp', $member->father_whatsapp) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label for="mother_whatsapp" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Ibu</label>
                        <input type="text" name="mother_whatsapp" id="mother_whatsapp" value="{{ old('mother_whatsapp', $member->mother_whatsapp) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label for="father_occupation" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Ayah</label>
                        <input type="text" name="father_occupation" id="father_occupation" value="{{ old('father_occupation', $member->father_occupation) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                    </div>
                    <div>
                        <label for="mother_occupation" class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan Ibu</label>
                        <input type="text" name="mother_occupation" id="mother_occupation" value="{{ old('mother_occupation', $member->mother_occupation) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 sm:px-8 border-t border-gray-100 flex items-center justify-end">
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[#5586DB] border border-transparent rounded-lg shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] hover:bg-[#4370BC] focus:outline-none transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
