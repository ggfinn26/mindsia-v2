@extends('layouts.dashboard')

@section('title', 'Pendaftaran Program - MINDSIA')
@section('header_title', 'Pendaftaran Program')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('member.program.browse') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Katalog Program
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Portal Siswa</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Pendaftaran Program</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Lengkapi formulir pendaftaran untuk mengikuti program pembelajaran pilihan Anda.
        </p>
    </div>

    @if ($errors->any())
    <div class="px-4 py-3 rounded-xl text-sm font-medium space-y-1"
         style="background-color: var(--dash-status-danger-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
        <div class="flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined text-[18px]">error</span>
            <span class="font-bold">Terjadi kesalahan pada formulir:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 pl-6">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="rounded-2xl overflow-hidden shadow-sm"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="p-6 sm:p-8"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-xl sm:text-2xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $program->program_name }}</h2>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
                Biaya Program: <span class="font-bold" style="color: var(--dash-primary);">Rp {{ number_format($program->program_price, 0, ',', '.') }}</span>
            </p>
        </div>
        @php
            $hasBranch      = $memberData->branch_id && $memberData->branch;
            $hasBirthPlace  = !empty($memberData->birth_place);
            $hasBirthdate   = (bool) $memberData->birthdate;
            $existingRef    = $memberData->referredBy;
            $hasEducation   = !empty($memberData->education_level);
            $educationIsUmum = $memberData->education_level === 'Umum';
            $hasClassFaculty = !empty($memberData->class_or_faculty);
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

        <form action="{{ route('member.program.register.post', $program->id) }}" method="POST" novalidate
              x-data="programRegistration(window.__regConfig)">
            @csrf

            <div class="p-6 sm:p-8 space-y-8">

                {{-- ── Data Diri ── --}}
                <div>
                    <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Data Diri</h3>
                    <p class="text-sm mb-6" style="color: var(--dash-text-muted);">Data yang sudah terisi otomatis diambil dari profil Anda.</p>

                    <div class="space-y-5">

                        {{-- Cabang --}}
                        @if($hasBranch)
                            <div>
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Cabang MINDSIA</label>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-lg"
                                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-input-border);">
                                    <span class="material-symbols-outlined text-[20px] shrink-0" style="color: var(--dash-status-success-text);">check_circle</span>
                                    <div>
                                        <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $memberData->branch->branch_name }}</p>
                                        <p class="text-xs" style="color: var(--dash-text-muted);">{{ $memberData->branch->code_branches }}</p>
                                    </div>
                                </div>
                                <input type="hidden" name="branch_id" value="{{ $memberData->branch_id }}">
                            </div>
                        @else
                            <!-- Cabang Pilihan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                                    Provinsi Cabang <span class="text-red-500">*</span>
                                </label>
                                <div class="relative" @click.away="showBranchProvinceDropdown = false">
                                    <div @click="showBranchProvinceDropdown = !showBranchProvinceDropdown"
                                         class="w-full px-3 py-2 rounded-lg text-sm transition-all shadow-sm flex justify-between items-center cursor-pointer focus:outline-none"
                                         style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);">
                                        <span x-text="selectedBranchProvinceName" class="truncate block pr-4"
                                              :style="branchProvinceId ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'"></span>
                                        <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-faint);">expand_more</span>
                                    </div>
                                    <div x-show="showBranchProvinceDropdown" x-transition x-cloak
                                         class="absolute z-50 w-full mt-1 rounded-lg shadow-lg overflow-hidden"
                                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                        <div class="p-2" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                            <div class="relative">
                                                <span class="material-symbols-outlined absolute left-3 top-2 text-[16px]" style="color: var(--dash-text-faint);">search</span>
                                                <input type="text" x-model="branchProvinceSearch" placeholder="Cari provinsi..."
                                                       style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                       class="w-full pr-3 py-1.5 rounded-md text-sm focus:outline-none">
                                            </div>
                                        </div>
                                        <ul class="max-h-48 overflow-y-auto py-1">
                                            <li x-show="filteredBranchProvinces.length === 0" class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                            <template x-for="p in filteredBranchProvinces" :key="p.id">
                                                <li @click="selectBranchProvince(p.id)" class="px-4 py-2 text-sm cursor-pointer transition-colors"
                                                    :style="branchProvinceId == p.id ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                                    x-text="p.name"></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                                    Cabang Pilihan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative" @click.away="showBranchDropdown = false">
                                    <div @click="if (branchProvinceId) showBranchDropdown = !showBranchDropdown"
                                         class="w-full px-3 py-2 rounded-lg text-sm transition-all shadow-sm flex justify-between items-center cursor-pointer focus:outline-none"
                                         :class="!branchProvinceId ? 'opacity-50 cursor-not-allowed' : ''"
                                         style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);">
                                        <span x-text="selectedBranchName" class="truncate block pr-4"
                                              :style="branchId ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'"></span>
                                        <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-faint);">expand_more</span>
                                    </div>
                                    <div x-show="showBranchDropdown" x-transition x-cloak
                                         class="absolute z-50 w-full mt-1 rounded-lg shadow-lg overflow-hidden"
                                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                        <div class="p-2" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                            <div class="relative">
                                                <span class="material-symbols-outlined absolute left-3 top-2 text-[16px]" style="color: var(--dash-text-faint);">search</span>
                                                <input type="text" x-model="branchSearch" placeholder="Cari cabang..."
                                                       style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                       class="w-full pr-3 py-1.5 rounded-md text-sm focus:outline-none">
                                            </div>
                                        </div>
                                        <ul class="max-h-48 overflow-y-auto py-1">
                                            <li x-show="branches.length === 0" class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Pilih Provinsi terlebih dahulu</li>
                                            <li x-show="branches.length > 0 && filteredBranches.length === 0" class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                            <template x-for="b in filteredBranches" :key="b.id">
                                                <li @click="selectBranch(b.id)" class="px-4 py-2 text-sm cursor-pointer transition-colors"
                                                    :style="branchId == b.id ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                                    x-text="b.branch_name"></li>
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
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Tempat Lahir</label>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-lg"
                                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-input-border);">
                                    <span class="material-symbols-outlined text-[20px] shrink-0" style="color: var(--dash-status-success-text);">check_circle</span>
                                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $memberData->birth_place }}</p>
                                </div>
                                <input type="hidden" name="birth_place" value="{{ $memberData->birth_place }}">
                            </div>
                        @else
                        <!-- Tempat Lahir -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                                    Provinsi Lahir <span class="text-red-500">*</span>
                                </label>
                                <div class="relative" @click.away="showBirthProvinceDropdown = false">
                                    <div @click="showBirthProvinceDropdown = !showBirthProvinceDropdown"
                                         class="w-full px-3 py-2 rounded-lg text-sm transition-all shadow-sm flex justify-between items-center cursor-pointer focus:outline-none"
                                         style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);">
                                        <span x-text="selectedBirthProvinceName" class="truncate block pr-4"
                                              :style="birthProvinceId ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'"></span>
                                        <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-faint);">expand_more</span>
                                    </div>
                                    <div x-show="showBirthProvinceDropdown" x-transition x-cloak
                                         class="absolute z-50 w-full mt-1 rounded-lg shadow-lg overflow-hidden"
                                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                        <div class="p-2" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                            <div class="relative">
                                                <span class="material-symbols-outlined absolute left-3 top-2 text-[16px]" style="color: var(--dash-text-faint);">search</span>
                                                <input type="text" x-model="birthProvinceSearch" placeholder="Cari provinsi..."
                                                       style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                       class="w-full pr-3 py-1.5 rounded-md text-sm focus:outline-none">
                                            </div>
                                        </div>
                                        <ul class="max-h-48 overflow-y-auto py-1">
                                            <li x-show="filteredBirthProvinces.length === 0" class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                            <template x-for="p in filteredBirthProvinces" :key="p.id">
                                                <li @click="selectBirthProvince(p.id)" class="px-4 py-2 text-sm cursor-pointer transition-colors"
                                                    :style="birthProvinceId == p.id ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                                    x-text="p.nama"></li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                                    Tempat Lahir <span class="text-red-500">*</span>
                                </label>
                                <div class="relative" @click.away="showBirthPlaceDropdown = false">
                                    <div @click="if (birthProvinceId) showBirthPlaceDropdown = !showBirthPlaceDropdown"
                                         class="w-full px-3 py-2 rounded-lg text-sm transition-all shadow-sm flex justify-between items-center cursor-pointer focus:outline-none"
                                         :class="!birthProvinceId ? 'opacity-50 cursor-not-allowed' : ''"
                                         style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);">
                                        <span x-text="selectedBirthPlaceName" class="truncate block pr-4"
                                              :style="birthPlace ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'"></span>
                                        <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-faint);">expand_more</span>
                                    </div>
                                    <div x-show="showBirthPlaceDropdown" x-transition x-cloak
                                         class="absolute z-50 w-full mt-1 rounded-lg shadow-lg overflow-hidden"
                                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                        <div class="p-2" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                            <div class="relative">
                                                <span class="material-symbols-outlined absolute left-3 top-2 text-[16px]" style="color: var(--dash-text-faint);">search</span>
                                                <input type="text" x-model="birthPlaceSearch" placeholder="Cari kota/kabupaten..."
                                                       style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                                       class="w-full pr-3 py-1.5 rounded-md text-sm focus:outline-none">
                                            </div>
                                        </div>
                                        <ul class="max-h-48 overflow-y-auto py-1">
                                            <li x-show="indoCities.length === 0" class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Pilih Provinsi terlebih dahulu</li>
                                            <li x-show="indoCities.length > 0 && filteredBirthPlaces.length === 0" class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                            <template x-for="c in filteredBirthPlaces" :key="c.id">
                                                <li @click="selectBirthPlace(c.nama)" class="px-4 py-2 text-sm cursor-pointer transition-colors"
                                                    :style="birthPlace == c.nama ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                                    x-text="c.nama"></li>
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
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Tanggal Lahir</label>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-lg"
                                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-input-border);">
                                    <span class="material-symbols-outlined text-[20px] shrink-0" style="color: var(--dash-status-success-text);">check_circle</span>
                                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $memberData->birthdate->translatedFormat('d F Y') }}</p>
                                </div>
                                <input type="hidden" name="birthdate" value="{{ $memberData->birthdate->format('Y-m-d') }}">
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                                    Tanggal Lahir <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="birthdate"
                                       value="{{ old('birthdate') }}"
                                       class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'"
                                       required>
                            </div>
                        @endif

                        {{-- Jenjang Pendidikan --}}
                        @if($hasEducation)
                            <div>
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Jenjang Pendidikan</label>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-lg"
                                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-input-border);">
                                    <span class="material-symbols-outlined text-[20px] shrink-0" style="color: var(--dash-status-success-text);">check_circle</span>
                                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $memberData->education_level }}</p>
                                </div>
                                <input type="hidden" name="education_level" value="{{ $memberData->education_level }}">
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                                    Jenjang Pendidikan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative" @click.away="showEducationDropdown = false">
                                    <div @click="showEducationDropdown = !showEducationDropdown"
                                         class="w-full px-3 py-2 rounded-lg text-sm shadow-sm flex justify-between items-center cursor-pointer focus:outline-none"
                                         style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);">
                                        <span x-text="selectedEducationLabel" :style="educationLevel ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'" class="truncate block pr-4"></span>
                                        <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-faint);">expand_more</span>
                                    </div>
                                    <div x-show="showEducationDropdown" x-transition x-cloak
                                         class="absolute z-50 w-full mt-1 rounded-lg shadow-lg overflow-hidden"
                                         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                        <ul class="max-h-56 overflow-y-auto py-1">
                                            <template x-for="group in educationOptions" :key="group.group">
                                                <li>
                                                    <div class="px-4 py-1.5 text-[10px] font-bold uppercase tracking-wider"
                                                         style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-faint);"
                                                         x-text="group.group"></div>
                                                    <template x-for="item in group.items" :key="item">
                                                        <div @click="selectEducationLevel(item)"
                                                             class="px-4 py-2 text-sm cursor-pointer transition-colors"
                                                             :style="educationLevel === item ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary); font-weight: 600;' : 'color: var(--dash-text-body);'"
                                                             x-text="item"></div>
                                                    </template>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                                <input type="hidden" name="education_level" :value="educationLevel">
                            </div>
                        @endif

                        {{-- Kelas / Jurusan --}}
                        @if($hasEducation && ($educationIsUmum || $hasClassFaculty))
                            <div>
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                                    {{ $educationIsUmum ? 'Kelas / Jurusan' : ($memberData->education_level === 'S1' || $memberData->education_level === 'S2' || $memberData->education_level === 'S3' || $memberData->education_level === 'D1' || $memberData->education_level === 'D2' || $memberData->education_level === 'D3' || $memberData->education_level === 'D4' ? 'Jurusan / Fakultas' : 'Kelas') }}
                                </label>
                                <div class="flex items-center gap-3 px-4 py-3 rounded-lg"
                                     style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-input-border);">
                                    <span class="material-symbols-outlined text-[20px] shrink-0" style="color: var(--dash-status-success-text);">check_circle</span>
                                    <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $educationIsUmum ? '-' : $memberData->class_or_faculty }}</p>
                                </div>
                                <input type="hidden" name="class_or_faculty" value="{{ $educationIsUmum ? '-' : $memberData->class_or_faculty }}">
                            </div>
                        @elseif($hasEducation && !$educationIsUmum && !$hasClassFaculty)
                            <div>
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                                    {{ in_array($memberData->education_level, ['S1','S2','S3','D1','D2','D3','D4']) ? 'Jurusan / Fakultas' : 'Kelas' }}
                                    <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="class_or_faculty"
                                       value="{{ old('class_or_faculty') }}"
                                       placeholder="{{ in_array($memberData->education_level, ['S1','S2','S3','D1','D2','D3','D4']) ? 'Contoh: Teknik Informatika' : 'Contoh: XII IPA 1' }}"
                                       maxlength="100" required
                                       class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'">
                            </div>
                        @else
                            {{-- education belum diisi — tampil kondisional via Alpine --}}
                            <div x-show="educationLevel && educationLevel !== 'Umum'" x-cloak>
                                <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">
                                    <span x-text="classOrFacultyLabel"></span> <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="class_or_faculty" x-model="classOrFaculty"
                                       :placeholder="classOrFacultyPlaceholder"
                                       :required="educationLevel && educationLevel !== 'Umum'"
                                       maxlength="100"
                                       class="w-full rounded-lg text-sm px-3 py-2 transition-colors focus:outline-none"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'">
                            </div>
                        @endif

                    </div>{{-- end space-y-5 --}}
                </div>

                <hr style="border-color: var(--dash-card-border);">

                {{-- ── Kode Referral MO ── --}}
                <div>
                    <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">
                        Kode Referral <span class="text-xs font-normal" style="color: var(--dash-text-faint);">(Opsional)</span>
                    </h3>
                    <p class="text-sm mb-4" style="color: var(--dash-text-muted);">Marketing Officer yang mereferensikan Anda ke program ini.</p>

                    @if($existingRef)
                        <div class="max-w-md p-3.5 rounded-xl shadow-sm flex items-center gap-3"
                             style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-input-border);">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                                 style="background-color: var(--dash-primary-bg); color: var(--dash-primary); border: 1px solid var(--dash-card-border);">
                                <span class="material-symbols-outlined text-[18px]">badge</span>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-sm font-bold truncate font-jakarta" style="color: var(--dash-text-heading);">
                                        {{ $existingRef->full_name }}
                                    </p>
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold"
                                          style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid var(--dash-card-border);">
                                        <span class="material-symbols-outlined text-[11px]">verified</span>
                                        Terhubung
                                    </span>
                                </div>
                                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                    Kode MO: <span class="font-mono font-semibold" style="color: var(--dash-text-body);">{{ $existingRef->employee_code }}</span>
                                </p>
                            </div>
                        </div>
                        <input type="hidden" name="referred_by_code" value="{{ $existingRef->employee_code }}">
                    @else
                        <div class="max-w-md relative" @click.away="showEmployeeDropdown = false">
                            <div @click="showEmployeeDropdown = !showEmployeeDropdown; if (showEmployeeDropdown) loadAllEmployees()"
                                 class="w-full px-3 py-2.5 rounded-lg text-sm shadow-sm flex justify-between items-center cursor-pointer focus:outline-none transition-all"
                                 style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border);">
                                <span x-text="referredByName ? (referredByName + ' (' + referredByCode + ')') : (referredByCode || 'Cari MO (Opsional)...')" class="truncate block pr-4"
                                      :style="(referredByName || referredByCode) ? 'color: var(--dash-text-heading);' : 'color: var(--dash-text-muted);'"></span>
                                <span class="material-symbols-outlined text-[16px]" style="color: var(--dash-text-faint);">expand_more</span>
                            </div>
                            
                            <div x-show="showEmployeeDropdown" x-transition x-cloak
                                 class="absolute z-50 w-full mt-1 rounded-lg shadow-lg overflow-hidden"
                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                <div class="p-2" style="background-color: var(--dash-nav-hover-bg); border-bottom: 1px solid var(--dash-card-border);">
                                    <div class="relative">
                                        <span class="material-symbols-outlined absolute left-3 top-2 text-[16px]" style="color: var(--dash-text-faint);">search</span>
                                        <input type="text" x-model="employeeSearch" @input="searchEmployee()"
                                               placeholder="Cari nama atau kode MO..."
                                               style="padding-left: 2.5rem !important; background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                               class="w-full pr-8 py-1.5 rounded-md text-sm focus:outline-none">
                                        <span x-show="searchingEmployee" class="absolute right-3 top-2 flex items-center">
                                            <span class="material-symbols-outlined animate-spin text-[16px]" style="color: var(--dash-text-faint);">progress_activity</span>
                                        </span>
                                    </div>
                                </div>
                                <ul class="max-h-48 overflow-y-auto py-1">
                                    <li x-show="searchingEmployee" class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Memuat...</li>
                                    <li x-show="!searchingEmployee && employees.length === 0" class="px-4 py-2 text-sm text-center" style="color: var(--dash-text-muted);">Tidak ditemukan</li>
                                    <template x-for="emp in employees" :key="emp.employee_code">
                                        <li @click="selectEmployee(emp.employee_code, emp.full_name)" class="px-4 py-2 text-sm cursor-pointer transition-colors"
                                            :style="referredByCode == emp.employee_code ? 'background-color: var(--dash-primary-bg); color: var(--dash-primary); font-weight: 600;' : 'color: var(--dash-text-body);'">
                                            <div x-text="emp.full_name"></div>
                                            <div class="text-xs opacity-75" x-text="emp.employee_code"></div>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                            
                            <div x-show="referredByCode" x-cloak
                                 class="mt-3 flex items-center p-3.5 rounded-xl transition-all shadow-sm"
                                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0"
                                         style="background-color: var(--dash-primary-bg); color: var(--dash-primary); border: 1px solid var(--dash-card-border);">
                                        <span class="material-symbols-outlined text-[18px]">badge</span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <p class="text-sm font-bold truncate font-jakarta" style="color: var(--dash-text-heading);"
                                               x-text="referredByName || 'Marketing Officer'"></p>
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold"
                                                  style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid var(--dash-card-border);">
                                                <span class="material-symbols-outlined text-[11px]">verified</span>
                                                Terpilih
                                            </span>
                                        </div>
                                        <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">
                                            Kode MO: <span class="font-mono font-semibold" style="color: var(--dash-text-body);" x-text="referredByCode"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="referred_by_code" :value="referredByCode">

                            @error('referred_by_code')
                                <p class="mt-1.5 text-xs font-medium" style="color: var(--dash-status-danger-text);">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <hr style="border-color: var(--dash-card-border);">

                {{-- ── Kode Diskon ── --}}
                <div>
                    <h3 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">
                        Kode Diskon <span class="text-xs font-normal" style="color: var(--dash-text-faint);">(Opsional)</span>
                    </h3>
                    <p class="text-sm mb-4" style="color: var(--dash-text-muted);">Masukkan kode promo jika Anda memilikinya. Potongan akan diverifikasi saat pembayaran.</p>
                    <div class="max-w-md">
                        <input type="text" name="discount_code" id="discount_code"
                               value="{{ old('discount_code') }}"
                               placeholder="Contoh: PROMO2026"
                               oninput="this.value = this.value.toUpperCase()"
                               class="w-full px-4 py-2.5 rounded-lg text-sm font-mono uppercase focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'" />
                        @error('discount_code')
                            <p class="mt-1.5 text-xs font-medium" style="color: var(--dash-status-danger-text);">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <hr style="border-color: var(--dash-card-border);">

                {{-- ── Metode Pembayaran ── --}}
                <div>
                    <h3 class="text-base sm:text-lg font-bold font-jakarta mb-2" style="color: var(--dash-text-heading);">Pilihan Skema Pembayaran</h3>
                    <p class="text-sm mb-4" style="color: var(--dash-text-muted);">Pilih rencana pembayaran yang sesuai dengan preferensi Anda.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($paymentPlans as $plan)
                            <label class="relative flex cursor-pointer rounded-xl p-4 shadow-sm focus:outline-none transition-all duration-200"
                                   :style="paymentPlanId == '{{ $plan->id }}'
                                       ? 'background-color: var(--dash-card-bg); border: 2px solid var(--dash-primary); box-shadow: 0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent);'
                                       : 'background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);'">
                                <input type="radio" name="payment_plan_id" value="{{ $plan->id }}" class="sr-only" required
                                       x-model="paymentPlanId">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $plan->label }}</span>
                                        <span class="mt-1.5 flex items-center text-xs" style="color: var(--dash-text-muted);">
                                            DP: <strong class="ml-1" style="color: var(--dash-text-heading);">Rp {{ number_format($plan->dp_amount, 0, ',', '.') }}</strong>
                                        </span>
                                        @if($plan->installment_count)
                                            <span class="mt-1 text-xs" style="color: var(--dash-text-muted);">
                                                {{ $plan->installment_count }}x Rp {{ number_format($plan->per_installment_amount, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </span>
                                </span>
                                <span class="material-symbols-outlined absolute right-4 top-4 text-[20px]"
                                      style="color: var(--dash-primary);"
                                      x-show="paymentPlanId == '{{ $plan->id }}'" x-cloak>check_circle</span>
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="px-6 py-4 sm:px-8 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('member.program.browse') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                   style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-lg shadow-sm transition-opacity hover:opacity-90"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                    Daftar Program
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
