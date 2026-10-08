@extends('layouts.dashboard')

@section('title', 'Profile Saya - MINDSIA')
@section('header_title', 'Profile Saya')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">

    @if(session('success'))
        <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium"
             style="background-color: #f0fdf4; border-color: #bbf7d0; color: #059669;">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Profile Header Card --}}
    <div class="rounded-2xl overflow-hidden" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        {{-- Avatar + Identity --}}
        <div class="px-6 py-6 sm:px-8 sm:py-7 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div class="flex items-center gap-5">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $user->name }}"
                         class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover shrink-0"
                         style="border: 2px solid var(--dash-primary-bg);">
                @else
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl flex items-center justify-center text-2xl sm:text-3xl font-bold font-jakarta shrink-0"
                         style="background-color: var(--dash-primary-bg); color: var(--dash-primary);">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                @endif

                <div>
                    <h2 class="text-lg sm:text-xl font-bold font-jakarta" style="color: var(--dash-text-heading);">
                        {{ $user->name }}
                    </h2>
                    <p class="text-sm mt-0.5" style="color: var(--dash-text-muted);">{{ $user->email }}</p>
                    <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold"
                         style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                        <span class="material-symbols-outlined text-[13px]">shield</span>
                        {{ $user->roles->pluck('name')->join(', ') ?: 'Tidak ada Role' }}
                    </div>
                </div>
            </div>

            <a href="{{ route('employee.profile.edit') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-colors shrink-0"
               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);"
               onmouseover="this.style.backgroundColor='var(--dash-primary-bg)'; this.style.borderColor='var(--dash-primary)'; this.style.color='var(--dash-primary-text)';"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'; this.style.borderColor='var(--dash-card-border)'; this.style.color='var(--dash-text-body)';">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Edit Profil
            </a>
        </div>

        {{-- Informasi Sistem --}}
        <div class="px-6 sm:px-8 py-6 border-t" style="border-color: var(--dash-card-border);">
            <div class="flex items-center gap-2.5 mb-5">
                <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Informasi Sistem</h3>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-8 gap-y-4">
                <div>
                    <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Status Akun</dt>
                    <dd class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                        @if($user->is_active)
                            <span class="inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background-color: #059669;"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background-color: #dc2626;"></span>
                                Non-Aktif
                            </span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Telegram Chat ID</dt>
                    <dd class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                        {{ $user->telegram_chat_id ?: 'Belum diatur' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Terakhir Login</dt>
                    <dd class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                        {{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Belum pernah' }}
                    </dd>
                </div>
            </dl>
        </div>

        @if($employee)
        {{-- Data Karyawan --}}
        <div class="px-6 sm:px-8 py-6 border-t" style="border-color: var(--dash-card-border);">
            <div class="flex items-center gap-2.5 mb-5">
                <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Data Karyawan</h3>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                <div>
                    <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Kode Karyawan</dt>
                    <dd class="text-sm font-semibold font-mono" style="color: var(--dash-text-heading);">{{ $employee->employee_code }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Nama Lengkap</dt>
                    <dd class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $employee->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Jenis Kelamin</dt>
                    <dd class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                        {{ $employee->gender == 'M' ? 'Laki-Laki' : 'Perempuan' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Tanggal Lahir</dt>
                    <dd class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                        {{ \Carbon\Carbon::parse($employee->birthdate)->format('d M Y') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">No. WhatsApp</dt>
                    <dd class="text-sm font-semibold" style="color: var(--dash-text-heading);">{{ $employee->whatsapp_number }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Penempatan</dt>
                    <dd class="text-sm font-semibold" style="color: var(--dash-text-heading);">
                        @if($employee->is_hq)
                            Head Quarter (HQ)
                        @else
                            {{ $employee->branch ? $employee->branch->name : 'Belum di-assign' }}
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
        @else
        <div class="px-6 sm:px-8 py-5 border-t" style="border-color: var(--dash-card-border);">
            <p class="text-sm" style="color: var(--dash-text-muted);">
                Akun ini belum dihubungkan dengan profil karyawan (HR Data). Hubungi tim HR/IT jika ini kesalahan.
            </p>
        </div>
        @endif
    </div>

    @if ($employee)

    {{-- Riwayat Pendidikan --}}
    <div x-data="{ addEdu: false }" class="mt-5 rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        <div class="flex items-center justify-between px-6 sm:px-8 py-4 border-b"
             style="border-color: var(--dash-card-border);">
            <div class="flex items-center gap-2.5">
                <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Riwayat Pendidikan</h3>
            </div>
            <button @click="addEdu = !addEdu" type="button"
                    class="inline-flex items-center gap-1 text-xs font-semibold transition-colors"
                    style="color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[15px]">add</span>
                Tambah
            </button>
        </div>

        <div x-show="addEdu" x-cloak class="px-6 sm:px-8 py-5 border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-primary-bg);">
            <form method="POST" action="{{ route('employees.educations.store', $employee) }}"
                  class="grid gap-3 sm:grid-cols-4">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold" style="color: var(--dash-text-body);">
                        Jenjang <span style="color: #dc2626;">*</span>
                    </label>
                    <select name="level" required>
                        @foreach (['SD','SMP','SMA','D3','S1','S2','S3'] as $lvl)
                            <option value="{{ $lvl }}">{{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold" style="color: var(--dash-text-body);">
                        Institusi <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" name="institution" required maxlength="200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold" style="color: var(--dash-text-body);">Jurusan</label>
                    <input type="text" name="major" maxlength="100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold" style="color: var(--dash-text-body);">Tahun Lulus</label>
                    <div class="flex gap-2">
                        <input type="number" name="graduation_year" min="1950" max="{{ date('Y') }}"
                               placeholder="{{ date('Y') }}">
                        <button type="submit"
                                class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold text-white whitespace-nowrap shrink-0"
                                style="background-color: var(--dash-primary);">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if ($employee->educations->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-6 sm:px-8 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Jenjang</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Institusi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Jurusan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Lulus</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employee->educations->sortByDesc('graduation_year') as $edu)
                            @php $eduLevels = ['sd'=>'SD','smp'=>'SMP','sma'=>'SMA/SMK','d1'=>'D1','d2'=>'D2','d3'=>'D3','d4'=>'D4','s1'=>'S1','s2'=>'S2','s3'=>'S3']; @endphp
                            <tr x-data="{ editEdu{{ $edu->id }}: false }"
                                style="border-bottom: 1px solid var(--dash-card-border);">
                                <td class="px-6 sm:px-8 py-3 font-semibold text-xs" style="color: var(--dash-primary);">
                                    {{ $eduLevels[$edu->level] ?? strtoupper($edu->level) }}
                                </td>
                                <td class="px-4 py-3 font-medium" style="color: var(--dash-text-heading);">{{ $edu->institution ?? '-' }}</td>
                                <td class="px-4 py-3" style="color: var(--dash-text-body);">{{ $edu->major ?? '-' }}</td>
                                <td class="px-4 py-3" style="color: var(--dash-text-muted);">{{ $edu->graduation_year ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="editEdu{{ $edu->id }} = !editEdu{{ $edu->id }}" type="button"
                                                class="text-xs font-semibold"
                                                style="color: var(--dash-primary);">Edit</button>
                                        <button type="button"
                                                @click="showConfirmModal = true; confirmTitle = 'Hapus Pendidikan'; confirmMessage = 'Yakin ingin menghapus data pendidikan ini?'; confirmActionUrl = '{{ route('employees.educations.destroy', [$employee, $edu]) }}'"
                                                class="text-xs font-semibold" style="color: #dc2626;">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr x-show="editEdu{{ $edu->id }}" x-cloak
                                style="background-color: var(--dash-primary-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <td colspan="5" class="px-6 sm:px-8 py-4">
                                    <form method="POST" action="{{ route('employees.educations.update', [$employee, $edu]) }}"
                                          class="grid gap-3 sm:grid-cols-4">
                                        @csrf @method('PATCH')
                                        <div>
                                            <select name="level" required>
                                                @foreach (['SD','SMP','SMA','D3','S1','S2','S3'] as $lvl)
                                                    <option value="{{ $lvl }}" @selected($edu->level === $lvl)>{{ $lvl }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <input type="text" name="institution" value="{{ $edu->institution }}" required maxlength="200">
                                        </div>
                                        <div>
                                            <input type="text" name="major" value="{{ $edu->major }}" maxlength="100">
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="number" name="graduation_year" value="{{ $edu->graduation_year }}"
                                                   min="1950" max="{{ date('Y') }}">
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold text-white shrink-0"
                                                    style="background-color: var(--dash-primary);">
                                                Simpan
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
            <div class="px-6 py-10 text-center">
                <span class="material-symbols-outlined text-4xl" style="color: var(--dash-text-faint);">school</span>
                <p class="mt-2 text-sm" style="color: var(--dash-text-muted);">Belum ada riwayat pendidikan.</p>
            </div>
        @endif
    </div>

    {{-- Pengalaman Kerja --}}
    <div x-data="{ addWork: false }" class="mt-5 rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">

        <div class="flex items-center justify-between px-6 sm:px-8 py-4 border-b"
             style="border-color: var(--dash-card-border);">
            <div class="flex items-center gap-2.5">
                <div class="w-[3px] h-5 rounded-full" style="background-color: var(--dash-primary);"></div>
                <h3 class="text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">Pengalaman Kerja Sebelumnya</h3>
            </div>
            <button @click="addWork = !addWork" type="button"
                    class="inline-flex items-center gap-1 text-xs font-semibold transition-colors"
                    style="color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[15px]">add</span>
                Tambah
            </button>
        </div>

        <div x-show="addWork" x-cloak class="px-6 sm:px-8 py-5 border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-primary-bg);">
            <form method="POST" action="{{ route('employees.work-histories.store', $employee) }}"
                  class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold" style="color: var(--dash-text-body);">
                        Perusahaan <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" name="company_name" required maxlength="200">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold" style="color: var(--dash-text-body);">Posisi</label>
                    <input type="text" name="position" maxlength="100">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold" style="color: var(--dash-text-body);">Tanggal Mulai</label>
                    <input type="date" name="start_date">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold" style="color: var(--dash-text-body);">Tanggal Selesai</label>
                    <input type="date" name="end_date">
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-xs font-semibold" style="color: var(--dash-text-body);">Alasan Keluar</label>
                    <div class="flex gap-2">
                        <input type="text" name="reason_leaving" maxlength="500">
                        <button type="submit"
                                class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-semibold text-white whitespace-nowrap shrink-0"
                                style="background-color: var(--dash-primary);">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if ($employee->workHistories->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--dash-card-border);">
                            <th class="px-6 sm:px-8 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Perusahaan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Posisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Periode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold" style="color: var(--dash-text-muted);">Alasan Keluar</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($employee->workHistories->sortByDesc('start_date') as $work)
                            <tr x-data="{ editWork{{ $work->id }}: false }"
                                style="border-bottom: 1px solid var(--dash-card-border);">
                                <td class="px-6 sm:px-8 py-3 font-semibold" style="color: var(--dash-text-heading);">{{ $work->company_name }}</td>
                                <td class="px-4 py-3" style="color: var(--dash-text-body);">{{ $work->position ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs" style="color: var(--dash-text-muted);">
                                    {{ $work->start_date?->format('M Y') ?? '-' }}
                                    @if ($work->end_date)
                                        s/d {{ $work->end_date->format('M Y') }}
                                    @else
                                        s/d sekarang
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs max-w-[180px] truncate" style="color: var(--dash-text-muted);">{{ $work->reason_leaving ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="editWork{{ $work->id }} = !editWork{{ $work->id }}" type="button"
                                                class="text-xs font-semibold"
                                                style="color: var(--dash-primary);">Edit</button>
                                        <button type="button"
                                                @click="showConfirmModal = true; confirmTitle = 'Hapus Pengalaman Kerja'; confirmMessage = 'Yakin ingin menghapus riwayat kerja ini?'; confirmActionUrl = '{{ route('employees.work-histories.destroy', [$employee, $work]) }}'"
                                                class="text-xs font-semibold" style="color: #dc2626;">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr x-show="editWork{{ $work->id }}" x-cloak
                                style="background-color: var(--dash-primary-bg); border-bottom: 1px solid var(--dash-card-border);">
                                <td colspan="5" class="px-6 sm:px-8 py-4">
                                    <form method="POST" action="{{ route('employees.work-histories.update', [$employee, $work]) }}"
                                          class="grid gap-3 sm:grid-cols-3">
                                        @csrf @method('PATCH')
                                        <div>
                                            <input type="text" name="company_name" value="{{ $work->company_name }}"
                                                   required maxlength="200" placeholder="Perusahaan">
                                        </div>
                                        <div>
                                            <input type="text" name="position" value="{{ $work->position }}"
                                                   maxlength="100" placeholder="Posisi">
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="date" name="start_date" value="{{ $work->start_date?->format('Y-m-d') }}">
                                            <input type="date" name="end_date" value="{{ $work->end_date?->format('Y-m-d') }}">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <input type="text" name="reason_leaving" value="{{ $work->reason_leaving }}"
                                                   maxlength="500" placeholder="Alasan keluar">
                                        </div>
                                        <div>
                                            <button type="submit"
                                                    class="w-full inline-flex items-center justify-center px-3 py-2 rounded-xl text-xs font-semibold text-white"
                                                    style="background-color: var(--dash-primary);">
                                                Simpan
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
            <div class="px-6 py-10 text-center">
                <span class="material-symbols-outlined text-4xl" style="color: var(--dash-text-faint);">business_center</span>
                <p class="mt-2 text-sm" style="color: var(--dash-text-muted);">Belum ada riwayat pekerjaan.</p>
            </div>
        @endif
    </div>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    <template x-teleport="body">
        <div x-show="showConfirmModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none;">
            <div class="fixed inset-0" style="background-color: rgba(0,0,0,0.5);" @click="showConfirmModal = false"></div>
            <div class="relative z-10 w-full max-w-md rounded-2xl overflow-hidden shadow-xl"
                 style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
                <div class="px-6 pt-6 pb-4 flex gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                         style="background-color: #fee2e2;">
                        <span class="material-symbols-outlined text-[20px]" style="color: #dc2626;">warning</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold font-jakarta" style="color: var(--dash-text-heading);" x-text="confirmTitle"></h3>
                        <p class="mt-1 text-sm" style="color: var(--dash-text-muted);" x-text="confirmMessage"></p>
                    </div>
                </div>
                <div class="px-6 pb-5 flex justify-end gap-3">
                    <button type="button" @click="showConfirmModal = false"
                            class="px-4 py-2 text-sm font-semibold rounded-xl"
                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        Batal
                    </button>
                    <button type="button" @click="$refs.confirmForm.submit()"
                            class="px-4 py-2 text-sm font-semibold rounded-xl text-white"
                            style="background-color: #dc2626;"
                            x-text="confirmActionText">
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
