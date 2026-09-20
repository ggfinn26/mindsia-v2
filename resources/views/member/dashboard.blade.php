@extends('layouts.dashboard')

@section('title', 'Member Portal - MINDSIA')
@section('header_title', 'Member Portal')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    @if(!$memberData->branch_id)
    {{-- Branch selection card: shown when member has not yet chosen a branch --}}
    <div x-data="{
        provinceId: '',
        branches: [],
        loading: false,
        fetchBranches() {
            if (!this.provinceId) { this.branches = []; return; }
            this.loading = true;
            fetch('/api/public/branches?province_id=' + this.provinceId)
                .then(r => r.json())
                .then(data => { this.branches = data; this.loading = false; })
                .catch(() => { this.branches = []; this.loading = false; });
        }
    }" class="bg-amber-50 border border-amber-200 rounded-xl shadow-sm p-5 sm:p-6">
        <div class="flex items-start gap-3 mb-4">
            <span class="material-symbols-outlined text-amber-500 mt-0.5 shrink-0">location_on</span>
            <div>
                <h3 class="font-bold text-amber-800 font-jakarta text-base">Pilih Cabang MINDSIA</h3>
                <p class="text-sm text-amber-700 mt-0.5">Pilih cabang MINDSIA terdekat untuk melanjutkan ke pemilihan program.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('member.branch.update') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Province select --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Provinsi</label>
                    <select
                        x-model="provinceId"
                        @change="fetchBranches()"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 bg-white"
                    >
                        <option value="">-- Pilih Provinsi --</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Branch select (populated via AJAX) --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Cabang</label>
                    <div class="relative">
                        <select
                            name="branch_id"
                            :disabled="!provinceId || loading"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 bg-white disabled:bg-gray-50 disabled:text-gray-400"
                            required
                        >
                            <option value="">
                                <span x-show="!provinceId">-- Pilih provinsi dulu --</span>
                                <span x-show="provinceId && loading">Memuat cabang...</span>
                                <span x-show="provinceId && !loading">-- Pilih Cabang --</span>
                            </option>
                            <template x-for="branch in branches" :key="branch.id">
                                <option :value="branch.id" x-text="branch.branch_name"></option>
                            </template>
                        </select>
                        <span x-show="loading" class="absolute right-3 top-2.5">
                            <svg class="animate-spin h-4 w-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    :disabled="!provinceId || branches.length === 0"
                    class="inline-flex items-center gap-2 px-5 py-2 bg-amber-500 hover:bg-amber-600 disabled:bg-amber-200 text-white font-semibold rounded-lg text-sm transition-colors"
                >
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Simpan Pilihan Cabang
                </button>
            </div>
        </form>
    </div>
    @endif

    <div class="mb-2">
        <p class="text-gray-500 font-medium text-sm sm:text-base">Selamat datang kembali, <span class="font-bold text-gray-800">{{ $memberData->full_name ?? 'Member' }}</span> 👋</p>
    </div>

    @if(session('info'))
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl flex items-start gap-3">
            <span class="material-symbols-outlined text-blue-500 mt-0.5">info</span>
            <div>
                <p class="font-medium">{{ session('info') }}</p>
            </div>
        </div>
    @endif

    <!-- Highlight Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Profil Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center">
            <div class="h-16 w-16 rounded-full bg-[#EEF4FF] flex items-center justify-center mb-4 border border-blue-100">
                @if($memberData->telegram_photo_id)
                    <img src="{{ $memberData->telegram_photo_id }}" alt="Avatar" class="h-full w-full rounded-full object-cover">
                @else
                    <span class="material-symbols-outlined text-[#5586DB] text-4xl">account_circle</span>
                @endif
            </div>
            <h2 class="text-lg font-bold font-jakarta text-gray-900 mb-1">Profil Saya</h2>
            <p class="text-sm font-medium text-gray-700">{{ $memberData->full_name }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $member->email }}</p>
        </div>

        <!-- Status Card -->
        @php
            use App\Enums\Member\ActivationStatus;
            $activationStatus = $memberData->activation_status;
            $isDitangguhkan = $activationStatus === ActivationStatus::Ditangguhkan;
            $isAktif = $activationStatus === ActivationStatus::Aktif;
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center">
            <div class="h-16 w-16 rounded-full border flex items-center justify-center mb-4
                {{ $isDitangguhkan ? 'bg-red-50 border-red-100' : ($isAktif ? 'bg-green-50 border-green-100' : 'bg-yellow-50 border-yellow-100') }}">
                <span class="material-symbols-outlined text-4xl
                    {{ $isDitangguhkan ? 'text-red-500' : ($isAktif ? 'text-green-500' : 'text-yellow-500') }}">
                    {{ $isDitangguhkan ? 'block' : ($isAktif ? 'how_to_reg' : 'pending_actions') }}
                </span>
            </div>
            <h2 class="text-lg font-bold font-jakarta text-gray-900 mb-1">Status Akun</h2>
            @if($isDitangguhkan)
                <p class="text-sm font-bold text-red-600 bg-red-50 px-3 py-1 rounded-full border border-red-100">Ditangguhkan</p>
                <p class="text-xs text-gray-500 mt-2">Terdapat cicilan yang melewati jatuh tempo. Selesaikan pembayaran untuk memulihkan akses.</p>
            @elseif($isAktif)
                <p class="text-sm font-bold text-green-600 bg-green-50 px-3 py-1 rounded-full border border-green-100">Aktif</p>
            @else
                <p class="text-sm font-bold text-yellow-600 bg-yellow-50 px-3 py-1 rounded-full border border-yellow-100">Menunggu Aktivasi</p>
            @endif
        </div>

        <!-- Kelas Aktif Counter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center text-center relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-[#5586DB]/5 rounded-full pointer-events-none"></div>
            <div class="absolute -left-4 -bottom-4 w-16 h-16 bg-[#5586DB]/5 rounded-full pointer-events-none"></div>
            
            <h2 class="text-sm font-bold font-jakarta text-gray-500 uppercase tracking-wider mb-2">Kelas Aktif</h2>
            <div class="text-5xl font-black font-jakarta text-[#5586DB] tracking-tight my-2">
                {{ $activeClasses->count() }}
            </div>
            <p class="text-xs text-gray-500 font-medium mt-1">Program sedang berjalan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri (Lebar: 2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Section Progress Kelas & Kehadiran -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-lg font-bold font-jakarta text-gray-900">Perkembangan Kelas</h2>
                    <a href="{{ route('member.class.index') }}" class="text-sm font-semibold text-[#5586DB] hover:text-[#00AACC] transition-colors">Lihat Semua</a>
                </div>
                
                <div class="p-5 sm:p-6">
                    @if($activeClasses->isEmpty())
                        <div class="text-center py-8">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-gray-50 mb-4">
                                <span class="material-symbols-outlined text-gray-400 text-3xl">school</span>
                            </div>
                            <h3 class="text-base font-bold text-gray-800 font-jakarta mb-2">Belum Ada Kelas Aktif</h3>
                            <p class="text-sm text-gray-500 max-w-sm mx-auto">Anda belum terdaftar atau belum mulai mengikuti kelas. Silakan daftar program yang tersedia.</p>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($activeClasses as $memberClass)
                                <div class="border border-gray-100 rounded-lg p-4 sm:p-5 hover:border-[#5586DB]/30 hover:shadow-md transition-all duration-300">
                                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4">
                                        <div class="flex items-start gap-4">
                                            <div class="h-12 w-12 rounded-lg bg-[#5586DB]/10 flex items-center justify-center shrink-0 border border-[#5586DB]/20">
                                                <span class="material-symbols-outlined text-[#5586DB]">menu_book</span>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-900 font-jakarta leading-tight">{{ $memberClass->classRoom->program->program_name ?? 'Program' }}</h4>
                                                <p class="text-xs text-gray-500 mt-1">Kelas: <span class="font-semibold text-gray-700">{{ $memberClass->classRoom->class_name ?? '-' }}</span></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 self-start sm:self-auto bg-green-50 px-3 py-1.5 rounded-md border border-green-100">
                                            <span class="material-symbols-outlined text-green-600 text-[18px]">verified</span>
                                            <span class="text-xs font-bold text-green-700">Aktif</span>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                                        <!-- Progress Materi -->
                                        <div>
                                            <div class="flex justify-between items-end mb-2">
                                                <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Progress Materi</span>
                                                <span class="text-sm font-bold text-[#5586DB]">45%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden border border-gray-200">
                                                <div class="bg-gradient-to-r from-[#5586DB] to-[#00AACC] h-2.5 rounded-full" style="width: 45%"></div>
                                            </div>
                                            <p class="text-[11px] text-gray-500 mt-2 text-right">9 dari 20 Modul Selesai</p>
                                        </div>
                                        
                                        <!-- Kehadiran -->
                                        <div>
                                            <div class="flex justify-between items-end mb-2">
                                                <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Kehadiran (Attendance)</span>
                                                <span class="text-sm font-bold text-green-600">80%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden border border-gray-200">
                                                <div class="bg-green-500 h-2.5 rounded-full" style="width: 80%"></div>
                                            </div>
                                            <p class="text-[11px] text-gray-500 mt-2 text-right">8 Hadir, 2 Absen</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan (Lebar: 1/3) -->
        <div class="space-y-6">
            <!-- Program Tersedia -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h2 class="text-lg font-bold font-jakarta text-gray-900">Rekomendasi Program</h2>
                </div>
                <div class="p-5">
                    @if($availablePrograms->isEmpty())
                        <p class="text-sm text-gray-500 text-center py-4">Belum ada program baru saat ini.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($availablePrograms as $program)
                                <a href="{{ route('member.program.browse') }}" class="group block border border-gray-100 rounded-lg overflow-hidden hover:shadow-md transition-all">
                                    <div class="h-24 w-full bg-gray-200 relative overflow-hidden">
                                        <img src="{{ $program->image_url }}" alt="{{ $program->program_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                        <span class="absolute bottom-2 left-3 text-white text-xs font-bold px-2 py-0.5 bg-[#5586DB] rounded">{{ $program->program_code }}</span>
                                    </div>
                                    <div class="p-3 bg-white">
                                        <h3 class="font-bold text-gray-900 text-sm font-jakarta group-hover:text-[#5586DB] transition-colors line-clamp-1">{{ $program->program_name }}</h3>
                                        <p class="text-xs text-gray-500 mt-1 font-medium">Mulai dari Rp {{ number_format($program->program_price, 0, ',', '.') }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('member.program.browse') }}" class="mt-5 block w-full py-2 text-center text-sm font-bold text-[#5586DB] border border-[#5586DB]/30 rounded-lg hover:bg-[#5586DB]/5 transition-colors">
                            Lihat Semua Program
                        </a>
                    @endif
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-[#5586DB] to-[#00AACC] rounded-xl shadow-sm overflow-hidden text-white relative">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="p-6 relative z-10">
                    <h2 class="text-lg font-bold font-jakarta mb-2 text-white">Bantuan Administrasi?</h2>
                    <p class="text-sm text-white/80 mb-5 leading-relaxed">Hubungi admin untuk info pendaftaran, jadwal, atau bantuan sistem lainnya.</p>
                    <a href="https://wa.me/628123456789" target="_blank" class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 bg-white text-[#5586DB] font-bold rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">forum</span>
                        Chat Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
