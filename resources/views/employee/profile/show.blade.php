@extends('layouts.dashboard')

@section('title', 'Profile Saya - MINDSIA')
@section('header_title', 'Profile Saya')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6"
     x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">

    {{-- Page Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1"
               style="color: var(--dash-primary-text);">Pengaturan Akun</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Profile Saya</h1>
        </div>
        <a href="{{ route('employee.profile.edit') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold shrink-0 transition-colors"
           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-text-heading)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.color='var(--dash-text-muted)'">
            <span class="material-symbols-outlined text-[16px]">edit</span>
            Edit Profil
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
         style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
        <span class="material-symbols-outlined text-[18px]">check_circle</span>
        {{ session('success') }}
    </div>
    @endif

    {{-- Identitas Akun --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        {{-- Avatar + Nama --}}
        <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center gap-5"
             style="border-bottom: 1px solid var(--dash-card-border);">
            <div class="shrink-0">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $user->name }}"
                         class="w-20 h-20 rounded-full object-cover"
                         style="border: 2px solid var(--dash-card-border);">
                @else
                    <div class="w-20 h-20 rounded-full flex items-center justify-center text-3xl font-bold font-jakarta shrink-0"
                         style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $user->name }}</h2>
                <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">{{ $user->email }}</p>
                <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold"
                     style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[13px]">shield</span>
                    {{ $user->roles->pluck('name')->join(', ') ?: 'Tidak ada Role' }}
                </div>
            </div>
        </div>

        {{-- Info Sistem --}}
        <div class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Informasi Sistem</h2>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Status Akun</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                    {{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Telegram Chat ID</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                    {{ $user->telegram_chat_id ?: 'Belum diatur' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Terakhir Login</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                    {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Belum pernah' }}
                </p>
            </div>
        </div>

        @if($employee)
        {{-- Data Karyawan --}}
        <div class="px-6 py-4"
             style="border-top: 1px solid var(--dash-card-border); border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Data Karyawan</h2>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Kode Karyawan</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $employee->employee_code }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Nama Lengkap</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $employee->full_name }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Jenis Kelamin</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $employee->gender == 'M' ? 'Laki-Laki' : 'Perempuan' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Tanggal Lahir</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ \Carbon\Carbon::parse($employee->birthdate)->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">No. WhatsApp</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $employee->whatsapp_number }}</p>
            </div>
            <div>
                <p class="text-xs font-medium uppercase tracking-wide mb-1" style="color: var(--dash-text-faint);">Penempatan</p>
                <p class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                    @if($employee->is_hq)
                        Head Quarter (HQ)
                    @else
                        {{ $employee->branch ? $employee->branch->name : 'Belum di-assign' }}
                    @endif
                </p>
            </div>
        </div>
        @else
        <div class="px-6 py-5" style="border-top: 1px solid var(--dash-card-border);">
            <p class="text-sm" style="color: var(--dash-text-muted);">
                Akun ini belum dihubungkan dengan profil karyawan. Jika ini sebuah kesalahan, hubungi tim HR/IT.
            </p>
        </div>
        @endif
    </div>

    @if($employee)

    {{-- Riwayat Pendidikan --}}
    <div x-data="{ addEdu: false }" class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Riwayat Pendidikan</h2>
            <button @click="addEdu = !addEdu" type="button"
                    class="inline-flex items-center gap-1 text-xs font-semibold transition-colors"
                    style="color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[14px]">add</span> Tambah
            </button>
        </div>

        {{-- Form Tambah --}}
        <div x-show="addEdu" x-collapse class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <form method="POST" action="{{ route('employees.educations.store', $employee) }}"
                  class="grid gap-3 sm:grid-cols-4">
                @csrf
                <div>
                    <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Jenjang <span class="text-red-500">*</span></label>
                    <select name="level" required
                            class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        @foreach (['SD','SMP','SMA','D3','S1','S2','S3'] as $lvl)
                            <option value="{{ $lvl }}">{{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Institusi <span class="text-red-500">*</span></label>
                    <input type="text" name="institution" required maxlength="200"
                           class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Jurusan</label>
                    <input type="text" name="major" maxlength="100"
                           class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Tahun Lulus</label>
                    <div class="flex gap-2">
                        <input type="number" name="graduation_year" min="1950" max="{{ date('Y') }}"
                               placeholder="{{ date('Y') }}"
                               class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'">
                        <button type="submit"
                                class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity whitespace-nowrap"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[13px]">save</span> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if ($employee->educations->isNotEmpty())
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Jenjang</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                        style="color: var(--dash-text-muted);">Institusi</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide hidden sm:table-cell"
                        style="color: var(--dash-text-muted);">Jurusan</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide hidden sm:table-cell"
                        style="color: var(--dash-text-muted);">Lulus</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employee->educations->sortByDesc('graduation_year') as $edu)
                @php $eduLevels = ['sd'=>'SD','smp'=>'SMP','sma'=>'SMA/SMK','d1'=>'D1','d2'=>'D2','d3'=>'D3','d4'=>'D4','s1'=>'S1','s2'=>'S2','s3'=>'S3']; @endphp
                <tr x-data="{ editEdu: false }"
                    style="border-bottom: 1px solid var(--dash-card-border);">
                    <td class="px-5 py-3 font-semibold text-sm" style="color: var(--dash-text-heading);">
                        {{ $eduLevels[$edu->level] ?? strtoupper($edu->level) }}
                    </td>
                    <td class="px-5 py-3 text-sm" style="color: var(--dash-text-body);">{{ $edu->institution ?? '—' }}</td>
                    <td class="px-5 py-3 text-sm hidden sm:table-cell" style="color: var(--dash-text-muted);">{{ $edu->major ?? '—' }}</td>
                    <td class="px-5 py-3 text-sm hidden sm:table-cell" style="color: var(--dash-text-muted);">{{ $edu->graduation_year ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <button @click="editEdu = !editEdu" type="button"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md transition-colors"
                                    style="color: var(--dash-primary); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                    onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                    onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                <span class="material-symbols-outlined text-[13px]">edit</span> Edit
                            </button>
                            <button type="button"
                                    @click="showConfirmModal = true; confirmTitle = 'Hapus Pendidikan'; confirmMessage = 'Yakin ingin menghapus data pendidikan ini?'; confirmActionUrl = '{{ route('employees.educations.destroy', [$employee, $edu]) }}'"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-white rounded-md hover:opacity-90 transition-opacity"
                                    style="background-color: #dc2626;">
                                <span class="material-symbols-outlined text-[13px]">delete</span> Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                {{-- Edit row --}}
                <tr x-show="editEdu" x-collapse
                    style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                    <td colspan="5" class="px-5 py-4">
                        <form method="POST" action="{{ route('employees.educations.update', [$employee, $edu]) }}"
                              class="grid gap-3 sm:grid-cols-4">
                            @csrf @method('PATCH')
                            <select name="level" required
                                    class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                                    style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                    onfocus="this.style.borderColor='var(--dash-primary)'"
                                    onblur="this.style.borderColor='var(--dash-input-border)'">
                                @foreach (['SD','SMP','SMA','D3','S1','S2','S3'] as $lvl)
                                    <option value="{{ $lvl }}" @selected($edu->level === $lvl)>{{ $lvl }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="institution" value="{{ $edu->institution }}" required maxlength="200"
                                   placeholder="Institusi"
                                   class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'">
                            <input type="text" name="major" value="{{ $edu->major }}" maxlength="100"
                                   placeholder="Jurusan"
                                   class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                                   style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                   onfocus="this.style.borderColor='var(--dash-primary)'"
                                   onblur="this.style.borderColor='var(--dash-input-border)'">
                            <div class="flex gap-2">
                                <input type="number" name="graduation_year" value="{{ $edu->graduation_year }}"
                                       min="1950" max="{{ date('Y') }}"
                                       class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'">
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity whitespace-nowrap"
                                        style="background-color: var(--dash-primary);">
                                    <span class="material-symbols-outlined text-[13px]">save</span> Simpan
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="px-5 py-10 text-center">
            <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
                  style="color: var(--dash-text-faint);">school</span>
            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada riwayat pendidikan.</p>
        </div>
        @endif
    </div>

    {{-- Pengalaman Kerja --}}
    <div x-data="{ addWork: false }" class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        <div class="px-6 py-4 flex items-center justify-between"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Pengalaman Kerja Sebelumnya</h2>
            <button @click="addWork = !addWork" type="button"
                    class="inline-flex items-center gap-1 text-xs font-semibold transition-colors"
                    style="color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[14px]">add</span> Tambah
            </button>
        </div>

        {{-- Form Tambah --}}
        <div x-show="addWork" x-collapse class="px-6 py-4"
             style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <form method="POST" action="{{ route('employees.work-histories.store', $employee) }}"
                  class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @csrf
                <div>
                    <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Perusahaan <span class="text-red-500">*</span></label>
                    <input type="text" name="company_name" required maxlength="200"
                           class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Posisi</label>
                    <input type="text" name="position" maxlength="100"
                           class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Tanggal Mulai</label>
                    <input type="date" name="start_date"
                           class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'">
                </div>
                <div>
                    <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Tanggal Selesai</label>
                    <input type="date" name="end_date"
                           class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'">
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide"
                           style="color: var(--dash-text-faint);">Alasan Keluar</label>
                    <div class="flex gap-2">
                        <input type="text" name="reason_leaving" maxlength="500"
                               class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">
                        <button type="submit"
                                class="inline-flex items-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity whitespace-nowrap"
                                style="background-color: var(--dash-primary);">
                            <span class="material-symbols-outlined text-[13px]">save</span> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if ($employee->workHistories->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide"
                            style="color: var(--dash-text-muted);">Perusahaan</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide hidden sm:table-cell"
                            style="color: var(--dash-text-muted);">Posisi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide hidden md:table-cell"
                            style="color: var(--dash-text-muted);">Periode</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide hidden lg:table-cell"
                            style="color: var(--dash-text-muted);">Alasan Keluar</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employee->workHistories->sortByDesc('start_date') as $work)
                    <tr x-data="{ editWork: false }"
                        style="border-bottom: 1px solid var(--dash-card-border);"
                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td class="px-5 py-3 font-semibold text-sm" style="color: var(--dash-text-heading);">{{ $work->company_name }}</td>
                        <td class="px-5 py-3 text-sm hidden sm:table-cell" style="color: var(--dash-text-body);">{{ $work->position ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm hidden md:table-cell" style="color: var(--dash-text-muted);">
                            {{ $work->start_date?->format('M Y') ?? '—' }}
                            @if($work->end_date) – {{ $work->end_date->format('M Y') }}
                            @else – sekarang
                            @endif
                        </td>
                        <td class="px-5 py-3 text-sm hidden lg:table-cell" style="color: var(--dash-text-muted);">{{ $work->reason_leaving ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="editWork = !editWork" type="button"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md transition-colors"
                                        style="color: var(--dash-primary); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                                        onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                                        onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                                    <span class="material-symbols-outlined text-[13px]">edit</span> Edit
                                </button>
                                <button type="button"
                                        @click="showConfirmModal = true; confirmTitle = 'Hapus Pengalaman Kerja'; confirmMessage = 'Yakin ingin menghapus riwayat kerja ini?'; confirmActionUrl = '{{ route('employees.work-histories.destroy', [$employee, $work]) }}'"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-white rounded-md hover:opacity-90 transition-opacity"
                                        style="background-color: #dc2626;">
                                    <span class="material-symbols-outlined text-[13px]">delete</span> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    {{-- Edit row --}}
                    <tr x-show="editWork" x-collapse
                        style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                        <td colspan="5" class="px-5 py-4">
                            <form method="POST" action="{{ route('employees.work-histories.update', [$employee, $work]) }}"
                                  class="grid gap-3 sm:grid-cols-3">
                                @csrf @method('PATCH')
                                <input type="text" name="company_name" value="{{ $work->company_name }}" required maxlength="200"
                                       placeholder="Perusahaan"
                                       class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'">
                                <input type="text" name="position" value="{{ $work->position }}" maxlength="100"
                                       placeholder="Posisi"
                                       class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                       onfocus="this.style.borderColor='var(--dash-primary)'"
                                       onblur="this.style.borderColor='var(--dash-input-border)'">
                                <div class="flex gap-2">
                                    <input type="date" name="start_date" value="{{ $work->start_date?->format('Y-m-d') }}"
                                           class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'">
                                    <input type="date" name="end_date" value="{{ $work->end_date?->format('Y-m-d') }}"
                                           class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'">
                                </div>
                                <div class="sm:col-span-2">
                                    <input type="text" name="reason_leaving" value="{{ $work->reason_leaving }}" maxlength="500"
                                           placeholder="Alasan keluar"
                                           class="block w-full px-3 py-2 text-sm rounded-lg focus:outline-none"
                                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                                           onfocus="this.style.borderColor='var(--dash-primary)'"
                                           onblur="this.style.borderColor='var(--dash-input-border)'">
                                </div>
                                <div>
                                    <button type="submit"
                                            class="inline-flex w-full items-center justify-center gap-1 px-3 py-2 rounded-lg text-xs font-semibold text-white hover:opacity-90 transition-opacity"
                                            style="background-color: var(--dash-primary);">
                                        <span class="material-symbols-outlined text-[13px]">save</span> Simpan
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-5 py-10 text-center">
            <span class="material-symbols-outlined block mx-auto mb-3 text-[48px]"
                  style="color: var(--dash-text-faint);">business_center</span>
            <p class="text-sm font-semibold" style="color: var(--dash-text-muted);">Belum ada riwayat pekerjaan.</p>
        </div>
        @endif
    </div>

    @endif

    {{-- Modal Konfirmasi Hapus --}}
    <template x-teleport="body">
        <div x-show="showConfirmModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
             style="display: none;">
            <div class="fixed inset-0 bg-gray-900/50" @click="showConfirmModal = false"></div>
            <div class="relative z-10 w-full max-w-sm rounded-2xl overflow-hidden shadow-xl"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-6 pt-6 pb-4">
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                             style="background-color: #fee2e2;">
                            <span class="material-symbols-outlined text-[20px] text-red-600">warning</span>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 flex justify-end gap-3"
                     style="border-top: 1px solid var(--dash-card-border);">
                    <button type="button" @click="showConfirmModal = false"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold transition-colors"
                            style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
                            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                            onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                        Batal
                    </button>
                    <button type="button" @click="$refs.confirmForm.submit()"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                            style="background-color: #dc2626;">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        <span x-text="confirmActionText"></span>
                    </button>
                </div>
                <form x-ref="confirmForm" :action="confirmActionUrl" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="_method" :value="confirmMethod">
                </form>
            </div>
        </div>
    </template>

</div>
@endsection
