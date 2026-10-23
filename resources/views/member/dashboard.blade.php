@extends('layouts.dashboard')

@section('title', 'Dashboard Siswa - MINDSIA')
@section('header_title', 'Dashboard Siswa')

@section('content')
<div class="w-full max-w-7xl mx-auto space-y-6">

    {{-- Page Header Pola A --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Portal Siswa</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Dashboard Siswa</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Selamat datang kembali, <span class="font-bold" style="color: var(--dash-text-heading);">{{ $memberData->full_name ?? 'Member' }}</span>
        </p>
    </div>

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
    }" class="rounded-2xl p-5 sm:p-6 shadow-sm"
       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                 style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text); border: 1px solid var(--dash-card-border);">
                <span class="material-symbols-outlined text-[22px]">location_on</span>
            </div>
            <div>
                <h3 class="font-bold font-jakarta text-base" style="color: var(--dash-text-heading);">Pilih Cabang MINDSIA</h3>
                <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">Pilih cabang MINDSIA terdekat untuk melanjutkan ke pemilihan program.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('member.branch.update') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Province select --}}
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Provinsi</label>
                    <select
                        x-model="provinceId"
                        @change="fetchBranches()"
                        class="w-full rounded-lg px-3 py-2 text-sm transition-colors"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'"
                    >
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Branch select (populated via AJAX) --}}
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color: var(--dash-text-body);">Cabang</label>
                    <div class="relative">
                        <select
                            name="branch_id"
                            :disabled="!provinceId || loading"
                            class="w-full rounded-lg px-3 py-2 text-sm transition-colors disabled:opacity-50"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'"
                            required
                        >
                            <option value="">
                                <span x-show="!provinceId">Pilih provinsi dahulu</span>
                                <span x-show="provinceId && loading">Memuat cabang...</span>
                                <span x-show="provinceId && !loading">Pilih Cabang</span>
                            </option>
                            <template x-for="branch in branches" :key="branch.id">
                                <option :value="branch.id" x-text="branch.branch_name"></option>
                            </template>
                        </select>
                        <span x-show="loading" class="absolute right-3 top-2.5" style="color: var(--dash-primary);">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-opacity disabled:opacity-50"
                    style="background-color: var(--dash-primary);"
                >
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Simpan Pilihan Cabang
                </button>
            </div>
        </form>
    </div>
    @endif

    @if(session('info'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
         style="background-color: var(--dash-status-info-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-info-text);">
        <span class="material-symbols-outlined text-[18px]">info</span>
        <p>{{ session('info') }}</p>
    </div>
    @endif

    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
         style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-success-text);">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium"
         style="background-color: var(--dash-status-danger-bg); border: 1px solid var(--dash-card-border); color: var(--dash-status-danger-text);">
        <span class="material-symbols-outlined text-[18px]">error</span>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <!-- Highlight Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Profil Card -->
        <div class="rounded-2xl shadow-sm p-6 flex flex-col items-center justify-center text-center transition-all"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="h-16 w-16 rounded-full flex items-center justify-center mb-4 overflow-hidden shrink-0"
                 style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-card-border);">
                @if($memberData->telegram_photo_id)
                    <img src="{{ $memberData->telegram_photo_id }}" alt="Avatar" class="h-full w-full rounded-full object-cover">
                @else
                    <span class="material-symbols-outlined text-4xl" style="color: var(--dash-primary);">account_circle</span>
                @endif
            </div>
            <h2 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Profil Saya</h2>
            <p class="text-sm font-medium" style="color: var(--dash-text-body);">{{ $memberData->full_name }}</p>
            <p class="text-xs mt-1" style="color: var(--dash-text-muted);">{{ $member->email }}</p>
        </div>

        <!-- Status Card -->
        @php
            use App\Enums\Member\ActivationStatus;
            $activationStatus = $memberData->activation_status;
            $isDitangguhkan = $activationStatus === ActivationStatus::Ditangguhkan;
            $isAktif = $activationStatus === ActivationStatus::Aktif;
        @endphp
        <div class="rounded-2xl shadow-sm p-6 flex flex-col items-center justify-center text-center transition-all"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="h-16 w-16 rounded-full flex items-center justify-center mb-4 shrink-0"
                 style="background-color: {{ $isDitangguhkan ? 'var(--dash-status-danger-bg)' : ($isAktif ? 'var(--dash-status-success-bg)' : 'var(--dash-status-warning-bg)') }};
                        border: 1px solid var(--dash-card-border);
                        color: {{ $isDitangguhkan ? 'var(--dash-status-danger-text)' : ($isAktif ? 'var(--dash-status-success-text)' : 'var(--dash-status-warning-text)') }};">
                <span class="material-symbols-outlined text-4xl">
                    {{ $isDitangguhkan ? 'block' : ($isAktif ? 'how_to_reg' : 'pending_actions') }}
                </span>
            </div>
            <h2 class="text-base sm:text-lg font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Status Akun</h2>
            @if($isDitangguhkan)
                <span class="text-xs font-bold px-3 py-1 rounded-full"
                      style="background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text); border: 1px solid var(--dash-card-border);">
                    Ditangguhkan
                </span>
                <p class="text-xs mt-2" style="color: var(--dash-text-muted);">
                    Terdapat cicilan yang melewati jatuh tempo. Selesaikan pembayaran untuk memulihkan akses.
                </p>
            @elseif($isAktif)
                <span class="text-xs font-bold px-3 py-1 rounded-full"
                      style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid var(--dash-card-border);">
                    Aktif
                </span>
            @else
                <span class="text-xs font-bold px-3 py-1 rounded-full"
                      style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text); border: 1px solid var(--dash-card-border);">
                    Menunggu Aktivasi
                </span>
            @endif
        </div>

        <!-- Kelas Aktif Counter -->
        <div class="rounded-2xl shadow-sm p-6 flex flex-col items-center justify-center text-center relative overflow-hidden"
             style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
            <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full pointer-events-none"
                 style="background-color: var(--dash-primary-bg); opacity: 0.5;"></div>
            <div class="absolute -left-4 -bottom-4 w-16 h-16 rounded-full pointer-events-none"
                 style="background-color: var(--dash-primary-bg); opacity: 0.5;"></div>
            
            <h2 class="text-xs font-bold font-jakarta uppercase tracking-wider mb-2" style="color: var(--dash-text-muted);">Kelas Aktif</h2>
            <div class="text-5xl font-black font-jakarta tracking-tight my-2" style="color: var(--dash-primary);">
                {{ $activeClasses->count() }}
            </div>
            <p class="text-xs font-medium mt-1" style="color: var(--dash-text-muted);">Program sedang berjalan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri (Lebar: 2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Section Progress Kelas & Kehadiran -->
            <div class="rounded-2xl overflow-hidden shadow-sm"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-5 sm:p-6 flex justify-between items-center"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="text-base sm:text-lg font-bold font-jakarta" style="color: var(--dash-text-heading);">Perkembangan Kelas</h2>
                    <a href="{{ route('member.class.index') }}"
                       class="text-sm font-semibold transition-opacity hover:opacity-80"
                       style="color: var(--dash-primary);">Lihat Semua</a>
                </div>
                
                <div class="p-5 sm:p-6">
                    @if($activeClasses->isEmpty())
                        <div class="text-center py-10">
                            <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
                                  style="color: var(--dash-text-faint);">school</span>
                            <h3 class="text-base font-bold font-jakarta mb-1" style="color: var(--dash-text-heading);">Belum Ada Kelas Aktif</h3>
                            <p class="text-sm max-w-sm mx-auto" style="color: var(--dash-text-muted);">Anda belum terdaftar atau belum mulai mengikuti kelas. Silakan daftar program yang tersedia.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($activeClasses as $memberClass)
                                <div class="rounded-xl p-4 sm:p-5 transition-all duration-200"
                                     style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                     onmouseover="this.style.borderColor='var(--dash-primary)'"
                                     onmouseout="this.style.borderColor='var(--dash-card-border)'">
                                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-4">
                                        <div class="flex items-start gap-4">
                                            <div class="h-12 w-12 rounded-xl flex items-center justify-center shrink-0"
                                                 style="background-color: var(--dash-primary-bg); color: var(--dash-primary); border: 1px solid var(--dash-card-border);">
                                                <span class="material-symbols-outlined text-2xl">menu_book</span>
                                            </div>
                                            <div>
                                                <h4 class="font-bold font-jakarta leading-tight" style="color: var(--dash-text-heading);">
                                                    {{ $memberClass->classRoom->program->program_name ?? 'Program' }}
                                                </h4>
                                                <p class="text-xs mt-1" style="color: var(--dash-text-muted);">
                                                    Kelas: <span class="font-semibold" style="color: var(--dash-text-body);">{{ $memberClass->classRoom->class_name ?? '-' }}</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="inline-flex items-center gap-1.5 self-start sm:self-auto px-3 py-1 rounded-full text-xs font-bold"
                                             style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text); border: 1px solid var(--dash-card-border);">
                                            <span class="material-symbols-outlined text-[16px]">verified</span>
                                            Aktif
                                        </div>
                                    </div>
                                    
                                    @php
                                        $prog = $memberClass->progress_summary;
                                        $att  = $memberClass->attendance_summary;
                                    @endphp
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4 pt-4"
                                         style="border-top: 1px solid var(--dash-card-border);">
                                        <!-- Progress Materi -->
                                        <div>
                                            <div class="flex justify-between items-end mb-2">
                                                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Progress Materi</span>
                                                @if($prog['rate'] !== null)
                                                    <span class="text-sm font-bold" style="color: var(--dash-primary);">{{ $prog['rate'] }}%</span>
                                                @endif
                                            </div>
                                            @if($prog['total'] > 0)
                                                <div class="w-full rounded-full h-2.5 overflow-hidden"
                                                     style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-card-border);">
                                                    <div class="h-2.5 rounded-full transition-all"
                                                         style="width: {{ $prog['rate'] }}%; background-color: var(--dash-primary);"></div>
                                                </div>
                                                <p class="text-[11px] mt-2 text-right" style="color: var(--dash-text-muted);">
                                                    {{ $prog['completed'] }} dari {{ $prog['total'] }} Materi Selesai
                                                </p>
                                            @else
                                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Belum ada materi</p>
                                            @endif
                                        </div>

                                        <!-- Kehadiran -->
                                        <div>
                                            <div class="flex justify-between items-end mb-2">
                                                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--dash-text-muted);">Kehadiran</span>
                                                @if($att['rate'] !== null)
                                                    <span class="text-sm font-bold" style="color: var(--dash-status-success-text);">{{ $att['rate'] }}%</span>
                                                @endif
                                            </div>
                                            @if($att['total'] > 0)
                                                <div class="w-full rounded-full h-2.5 overflow-hidden"
                                                     style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-card-border);">
                                                    <div class="h-2.5 rounded-full transition-all"
                                                         style="width: {{ $att['rate'] }}%; background-color: #059669;"></div>
                                                </div>
                                                <p class="text-[11px] mt-2 text-right" style="color: var(--dash-text-muted);">
                                                    {{ $att['attended'] }} Hadir &middot; {{ $att['absent'] }} Tidak Hadir
                                                </p>
                                            @else
                                                <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Belum ada pertemuan</p>
                                            @endif
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
            <div class="rounded-2xl overflow-hidden shadow-sm"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="p-5"
                     style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <h2 class="text-base sm:text-lg font-bold font-jakarta" style="color: var(--dash-text-heading);">Rekomendasi Program</h2>
                </div>
                <div class="p-5">
                    @if($availablePrograms->isEmpty())
                        <div class="text-center py-6">
                            <span class="material-symbols-outlined block mx-auto mb-2 text-[36px]"
                                  style="color: var(--dash-text-faint);">category</span>
                            <p class="text-sm" style="color: var(--dash-text-muted);">Belum ada program baru saat ini.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($availablePrograms as $program)
                                <a href="{{ route('member.program.browse') }}"
                                   class="group block rounded-xl overflow-hidden transition-all duration-200"
                                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                   onmouseover="this.style.borderColor='var(--dash-primary)'"
                                   onmouseout="this.style.borderColor='var(--dash-card-border)'">
                                    <div class="h-28 w-full relative overflow-hidden" style="background-color: var(--dash-input-bg);">
                                        <img src="{{ $program->image_url }}" alt="{{ $program->program_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                        <span class="absolute bottom-2 left-3 text-white text-xs font-bold px-2.5 py-0.5 rounded"
                                              style="background-color: var(--dash-primary);">{{ $program->program_code }}</span>
                                    </div>
                                    <div class="p-3.5" style="background-color: var(--dash-card-bg);">
                                        <h3 class="font-bold text-sm font-jakarta transition-colors line-clamp-1"
                                            style="color: var(--dash-text-heading);">{{ $program->program_name }}</h3>
                                        <p class="text-xs mt-1 font-medium" style="color: var(--dash-text-muted);">
                                            Mulai dari Rp {{ number_format($program->program_price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('member.program.browse') }}"
                           class="mt-5 block w-full py-2.5 text-center text-sm font-semibold rounded-lg transition-colors"
                           style="color: var(--dash-primary); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                            Lihat Semua Program
                        </a>
                    @endif
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="rounded-2xl shadow-sm overflow-hidden text-white relative p-6"
                 style="background: linear-gradient(135deg, var(--dash-primary) 0%, #00aacc 100%);">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative z-10">
                    <h2 class="text-lg font-bold font-jakarta mb-2 text-white">Bantuan Administrasi?</h2>
                    <p class="text-sm text-white/90 mb-5 leading-relaxed">Hubungi admin untuk info pendaftaran, jadwal, atau bantuan sistem lainnya.</p>
                    <a href="https://wa.me/628123456789" target="_blank"
                       class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 font-bold rounded-lg shadow-sm transition-opacity hover:opacity-95"
                       style="background-color: #ffffff; color: #0f172a;">
                        <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">forum</span>
                        Chat Admin
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
