@extends('layouts.dashboard')

@section('title', 'Pendaftaran Program - MINDSIA')
@section('header_title', 'Pendaftaran Program')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('member.program.browse') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
            <span class="material-symbols-outlined text-[20px] mr-1">arrow_back</span>
            Kembali ke Katalog
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside text-sm font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-gray-100 bg-[#5586DB]/5">
            <h2 class="text-2xl font-bold font-jakarta text-gray-900">{{ $program->program_name }}</h2>
            <p class="text-sm text-gray-500 mt-1">Biaya Program: <span class="font-bold text-[#5586DB]">Rp {{ number_format($program->program_price, 0, ',', '.') }}</span></p>
        </div>
        @php
            $hasBranch    = $memberData->branch_id && $memberData->branch;
            $hasBirthPlace = !empty($memberData->birth_place);
            $hasBirthdate  = (bool) $memberData->birthdate;
            $existingRef = $memberData->referredBy;
        @endphp

        @php
            $regConfig = [
                'needsBranch'           => !$hasBranch,
                'needsBirth'            => !$hasBirthPlace,
                'memberProvinceId'      => optional($memberData->branch?->area?->region)->province_id ?? '',
                'memberBranchId'        => $memberData->branch_id ?? '',
                'memberBirthProvinceId' => '',
                'memberBirthPlace'      => $memberData->birth_place ?? '',
                'referredByCode'        => old('referred_by_code', $existingRef->employee_code ?? ''),
                'referredByName'        => $existingRef->full_name ?? '',
                'paymentPlanId'         => old('payment_plan_id') ?? '',
                'educationLevel'        => old('education_level', $memberData->education_level ?? ''),
                'classOrFaculty'        => old('class_or_faculty', $memberData->class_or_faculty ?? ''),
            ];
        @endphp
        <script>window.__regConfig = @json($regConfig);</script>

        <form action="{{ route('member.program.register.post', $program->id) }}" method="POST"
              x-data="programRegistration(window.__regConfig)">
            @csrf

            <div class="p-6 sm:p-8 space-y-8">

                {{-- ── Data Diri ── --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-1">Data Diri</h3>
                    <p class="text-sm text-gray-500 mb-6">Data yang sudah terisi tidak perlu diubah.</p>

                    <div class="space-y-5">

                        {{-- Cabang --}}
                        @if($hasBranch)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cabang MINDSIA</label>
                                <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    <span class="material-symbols-outlined text-[20px] text-green-500 shrink-0">check_circle</span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $memberData->branch->branch_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $memberData->branch->code_branches }}</p>
                                    </div>
                                </div>
                                <input type="hidden" name="branch_id" value="{{ $memberData->branch_id }}">
                            </div>
                        @else
                            <!-- Cabang Pilihan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi Cabang <span class="text-red-500">*</span></label>
                                <div class="relative" @click.away="showBranchProvinceDropdown = false">
                                    <div @click="showBranchProvinceDropdown = !showBranchProvinceDropdown"
                                         class="w-full px-3 py-2 bg-gray-50 border rounded-lg sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#5586DB] transition-all shadow-sm flex justify-between items-center cursor-pointer border-gray-300">
                                        <span x-text="selectedBranchProvinceName" class="truncate block pr-4"
                                              :class="branchProvinceId ? 'text-gray-900' : 'text-gray-500'"></span>
                                        <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                                    </div>
                                    <div x-show="showBranchProvinceDropdown" x-transition x-cloak
                                         class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                                        <div class="p-2 bg-gray-50 border-b border-gray-100">
                                            <div class="relative">
                                                <span class="material-symbols-outlined absolute left-3 top-2 text-gray-400 text-[16px]">search</span>
                                                <input type="text" x-model="branchProvinceSearch" placeholder="Cari provinsi..."
                                                       class="w-full pl-9 pr-3 py-1.5 bg-white border border-gray-200 rounded-md sm:text-sm focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB]">
                                            </div>
                                        </div>
                                        <ul class="max-h-48 overflow-y-auto py-1">
                                            <li x-show="filteredBranchProvinces.length === 0" class="px-4 py-2 sm:text-sm text-gray-500 text-center">Tidak ditemukan</li>
                                            <template x-for="p in filteredBranchProvinces" :key="p.id">
                                                <li @click="selectBranchProvince(p.id)" class="px-4 py-2 sm:text-sm text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer"
                                                    :class="branchProvinceId == p.id ? 'bg-[#EFF6FF] font-semibold' : ''" x-text="p.name"></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cabang Pilihan <span class="text-red-500">*</span></label>
                                <div class="relative" @click.away="showBranchDropdown = false">
                                    <div @click="if (branchProvinceId) showBranchDropdown = !showBranchDropdown"
                                         class="w-full px-3 py-2 bg-gray-50 border rounded-lg sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#5586DB] transition-all shadow-sm flex justify-between items-center cursor-pointer border-gray-300"
                                         :class="!branchProvinceId ? 'opacity-50 cursor-not-allowed' : ''">
                                        <span x-text="selectedBranchName" class="truncate block pr-4"
                                              :class="branchId ? 'text-gray-900' : 'text-gray-500'"></span>
                                        <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                                    </div>
                                    <div x-show="showBranchDropdown" x-transition x-cloak
                                         class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                                        <div class="p-2 bg-gray-50 border-b border-gray-100">
                                            <div class="relative">
                                                <span class="material-symbols-outlined absolute left-3 top-2 text-gray-400 text-[16px]">search</span>
                                                <input type="text" x-model="branchSearch" placeholder="Cari cabang..."
                                                       class="w-full pl-9 pr-3 py-1.5 bg-white border border-gray-200 rounded-md sm:text-sm focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB]">
                                            </div>
                                        </div>
                                        <ul class="max-h-48 overflow-y-auto py-1">
                                            <li x-show="branches.length === 0" class="px-4 py-2 sm:text-sm text-gray-500 text-center">Pilih Provinsi terlebih dahulu</li>
                                            <li x-show="branches.length > 0 && filteredBranches.length === 0" class="px-4 py-2 sm:text-sm text-gray-500 text-center">Tidak ditemukan</li>
                                            <template x-for="b in filteredBranches" :key="b.id">
                                                <li @click="selectBranch(b.id)" class="px-4 py-2 sm:text-sm text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer"
                                                    :class="branchId == b.id ? 'bg-[#EFF6FF] font-semibold' : ''" x-text="b.branch_name"></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                                <input type="hidden" name="branch_id" :value="branchId" required>
                            </div>
                        </div>
                        @endif

                        {{-- Tempat Lahir --}}
                        @if($hasBirthPlace)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                                <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    <span class="material-symbols-outlined text-[20px] text-green-500 shrink-0">check_circle</span>
                                    <p class="text-sm font-medium text-gray-900">{{ $memberData->birth_place }}</p>
                                </div>
                                <input type="hidden" name="birth_place" value="{{ $memberData->birth_place }}">
                            </div>
                        @else
                        <!-- Tempat Lahir -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi Lahir <span class="text-red-500">*</span></label>
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
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir <span class="text-red-500">*</span></label>
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
                                <input type="hidden" name="birth_place" :value="birthPlace" required>
                            </div>
                        </div>
                        @endif

                        {{-- Tanggal Lahir --}}
                        @if($hasBirthdate)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    <span class="material-symbols-outlined text-[20px] text-green-500 shrink-0">check_circle</span>
                                    <p class="text-sm font-medium text-gray-900">{{ $memberData->birthdate->translatedFormat('d F Y') }}</p>
                                </div>
                                <input type="hidden" name="birthdate" value="{{ $memberData->birthdate->format('Y-m-d') }}">
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="birthdate"
                                       value="{{ old('birthdate') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border"
                                       required>
                            </div>
                        @endif

                        {{-- Jenjang Pendidikan --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang Pendidikan <span class="text-sm font-normal text-gray-400">(Opsional)</span></label>
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

                        {{-- Kelas / Jurusan (kondisional) --}}
                        <div x-show="educationLevel && educationLevel !== 'Umum'" x-cloak>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <span x-text="classOrFacultyLabel"></span>
                                <span class="text-sm font-normal text-gray-400 ml-1">(Opsional)</span>
                            </label>
                            <input type="text" name="class_or_faculty" x-model="classOrFaculty"
                                   :placeholder="classOrFacultyPlaceholder"
                                   maxlength="100"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#5586DB] focus:ring-[#5586DB] sm:text-sm px-3 py-2 border">
                        </div>

                    </div>{{-- end space-y-5 --}}
                </div>

                <hr class="border-gray-100">

                {{-- ── Kode Referral MO ── --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-1">Kode Referral <span class="text-sm font-normal text-gray-400">(Opsional)</span></h3>
                    <p class="text-sm text-gray-500 mb-4">Masukkan kode atau cari nama Marketing Officer yang mereferensikan kamu.</p>
                    <div class="max-w-sm relative" @click.away="showEmployeeDropdown = false">
                        <div @click="showEmployeeDropdown = !showEmployeeDropdown"
                             class="w-full px-3 py-2 bg-white border rounded-lg sm:text-sm focus:outline-none focus:ring-2 focus:ring-[#5586DB] transition-all shadow-sm flex justify-between items-center cursor-pointer border-gray-300">
                            <span x-text="referredByName ? (referredByName + ' (' + referredByCode + ')') : (referredByCode || 'Cari MO (Opsional)...')" class="truncate block pr-4"
                                  :class="(referredByName || referredByCode) ? 'text-gray-900' : 'text-gray-500'"></span>
                            <span class="material-symbols-outlined text-[16px] text-gray-400">expand_more</span>
                        </div>
                        
                        <div x-show="showEmployeeDropdown" x-transition x-cloak
                             class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                            <div class="p-2 bg-gray-50 border-b border-gray-100">
                                <div class="relative">
                                    <span class="material-symbols-outlined absolute left-3 top-2 text-gray-400 text-[16px]">search</span>
                                    <input type="text" x-model="employeeSearch" @input.debounce.500ms="searchEmployee()"
                                           placeholder="Ketik nama atau kode..."
                                           class="w-full pl-9 pr-8 py-1.5 bg-white border border-gray-200 rounded-md sm:text-sm focus:outline-none focus:border-[#5586DB] focus:ring-1 focus:ring-[#5586DB]">
                                    <span x-show="searchingEmployee" class="absolute right-3 top-2 flex items-center">
                                        <span class="material-symbols-outlined text-gray-400 animate-spin text-[16px]">progress_activity</span>
                                    </span>
                                </div>
                            </div>
                            <ul class="max-h-48 overflow-y-auto py-1">
                                <li x-show="employeeSearch.length < 3" class="px-4 py-2 sm:text-sm text-gray-500 text-center">Ketik minimal 3 huruf</li>
                                <li x-show="employeeSearch.length >= 3 && !searchingEmployee && employees.length === 0" class="px-4 py-2 sm:text-sm text-gray-500 text-center">Tidak ditemukan</li>
                                <template x-for="emp in employees" :key="emp.employee_code">
                                    <li @click="selectEmployee(emp.employee_code, emp.full_name)" class="px-4 py-2 sm:text-sm text-gray-700 hover:bg-[#5586DB] hover:text-white cursor-pointer"
                                        :class="referredByCode == emp.employee_code ? 'bg-[#EFF6FF] font-semibold' : ''">
                                        <div x-text="emp.full_name"></div>
                                        <div class="text-xs opacity-75" x-text="emp.employee_code"></div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        
                        <div x-show="referredByCode" class="mt-2 flex items-center justify-between">
                            <p class="text-xs font-medium text-green-600 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                <span x-text="referredByName ? referredByName : referredByCode"></span>
                            </p>
                            <button type="button" @click="clearEmployee()" class="text-xs text-red-500 hover:text-red-700">Hapus</button>
                        </div>
                        
                        <input type="hidden" name="referred_by_code" :value="referredByCode">

                        @error('referred_by_code')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- ── Metode Pembayaran ── --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Metode Pembayaran</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($paymentPlans as $plan)
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none transition-all"
                                   :class="paymentPlanId == '{{ $plan->id }}' ? 'border-[#5586DB] ring-1 ring-[#5586DB]' : 'border-gray-200'">
                                <input type="radio" name="payment_plan_id" value="{{ $plan->id }}" class="sr-only" required
                                       x-model="paymentPlanId">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-gray-900">{{ $plan->label }}</span>
                                        <span class="mt-1 flex items-center text-xs text-gray-500">DP: Rp {{ number_format($plan->dp_amount, 0, ',', '.') }}</span>
                                        @if($plan->installment_count)
                                            <span class="mt-1 text-xs text-gray-500">{{ $plan->installment_count }}x Rp {{ number_format($plan->per_installment_amount, 0, ',', '.') }}</span>
                                        @endif
                                    </span>
                                </span>
                                <span class="material-symbols-outlined text-[#5586DB] absolute right-4 top-4" x-show="paymentPlanId == '{{ $plan->id }}'" x-cloak>check_circle</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="bg-gray-50 px-6 py-4 sm:px-8 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('member.program.browse') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-[#5586DB] border border-transparent rounded-lg shadow-[0_4px_12px_-2px_rgba(85,134,219,0.3)] hover:bg-[#4370BC] transition-colors inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    Daftar Program
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
