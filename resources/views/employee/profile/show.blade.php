@extends('layouts.dashboard')

@section('title', 'Profile Saya - MINDSIA')
@section('header_title', 'Profile Saya')

@section('content')
<div class="max-w-3xl mx-auto" x-data="{ showConfirmModal: false, confirmTitle: '', confirmMessage: '', confirmActionUrl: '', confirmMethod: 'DELETE', confirmActionText: 'Hapus' }">
    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                @if($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover border border-gray-200 shrink-0">
                @else
                    <div class="w-20 h-20 bg-[#EEF4FF] rounded-full flex items-center justify-center text-[#5586DB] text-3xl font-bold shrink-0">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h2 class="text-xl font-bold text-gray-900 font-jakarta">{{ $user->name }}</h2>
                    <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
                    <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700">
                        <span class="material-symbols-outlined text-[14px]">shield</span>
                        {{ $user->roles->pluck('name')->join(', ') ?: 'Tidak ada Role' }}
                    </div>
                </div>
            </div>
            
            <div class="shrink-0">
                <a href="{{ route('employee.profile.edit') }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 active:bg-gray-100 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Edit Profil
                </a>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Informasi Sistem</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Status Akun</p>
                    <p class="font-medium text-gray-900">{{ $user->is_active ? 'Aktif' : 'Non-Aktif' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Telegram Chat ID</p>
                    <p class="font-medium text-gray-900">{{ $user->telegram_chat_id ?: 'Belum diatur' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Terakhir Login</p>
                    <p class="font-medium text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('d M Y, H:i') : 'Belum pernah' }}</p>
                </div>
            </div>

            @if($employee)
                <hr class="my-8 border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 font-jakarta mb-4">Data Karyawan</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Kode Karyawan</p>
                        <p class="font-medium text-gray-900">{{ $employee->employee_code }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Nama Lengkap</p>
                        <p class="font-medium text-gray-900">{{ $employee->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Jenis Kelamin</p>
                        <p class="font-medium text-gray-900">{{ $employee->gender == 'M' ? 'Laki-Laki' : 'Perempuan' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tanggal Lahir</p>
                        <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($employee->birthdate)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">No. WhatsApp</p>
                        <p class="font-medium text-gray-900">{{ $employee->whatsapp_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Penempatan</p>
                        <p class="font-medium text-gray-900">
                            @if($employee->is_hq)
                                Head Quarter (HQ)
                            @else
                                {{ $employee->branch ? $employee->branch->name : 'Belum di-assign' }}
                            @endif
                        </p>
                    </div>
                </div>
            @else
                <hr class="my-8 border-gray-100">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-sm text-gray-600">Akun ini belum dihubungkan dengan profil karyawan (HR Data). Jika Anda merasa ini sebuah kesalahan, silakan hubungi tim HR/IT.</p>
                </div>
            @endif
        </div>
    </div>

    @if ($employee)
    {{-- Riwayat Pendidikan --}}
    <div x-data="{ addEdu: false }" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900 font-jakarta">Riwayat Pendidikan</h3>
            <button @click="addEdu = !addEdu" type="button"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#5586DB] hover:underline">
                <span class="material-symbols-outlined text-[16px]">add</span> Tambah
            </button>
        </div>

        <div x-show="addEdu" x-cloak class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <form method="POST" action="{{ route('employees.educations.store', $employee) }}"
                  class="grid gap-3 sm:grid-cols-4">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Jenjang <span class="text-red-500">*</span></label>
                    <select name="level" required class="block w-full border-gray-300 rounded-lg text-sm focus:border-[#5586DB] focus:ring-[#5586DB]">
                        @foreach (['SD','SMP','SMA','D3','S1','S2','S3'] as $lvl)
                            <option value="{{ $lvl }}">{{ $lvl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Institusi <span class="text-red-500">*</span></label>
                    <input type="text" name="institution" required maxlength="200"
                           class="block w-full border-gray-300 rounded-lg text-sm focus:border-[#5586DB] focus:ring-[#5586DB]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Jurusan</label>
                    <input type="text" name="major" maxlength="100"
                           class="block w-full border-gray-300 rounded-lg text-sm focus:border-[#5586DB] focus:ring-[#5586DB]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Tahun Lulus</label>
                    <div class="flex gap-2">
                        <input type="number" name="graduation_year" min="1950" max="{{ date('Y') }}"
                               placeholder="{{ date('Y') }}"
                               class="block w-full border-gray-300 rounded-lg text-sm focus:border-[#5586DB] focus:ring-[#5586DB]">
                        <button type="submit"
                                class="inline-flex items-center bg-[#5586DB] hover:bg-[#4070c5] text-white px-3 py-2 rounded-lg text-xs font-semibold">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if ($employee->educations->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Jenjang</th>
                            <th class="px-6 py-3 text-left">Institusi</th>
                            <th class="px-6 py-3 text-left">Jurusan</th>
                            <th class="px-6 py-3 text-left">Lulus</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($employee->educations->sortByDesc('graduation_year') as $edu)
                            @php $eduLevels = ['sd'=>'SD','smp'=>'SMP','sma'=>'SMA/SMK','d1'=>'D1','d2'=>'D2','d3'=>'D3','d4'=>'D4','s1'=>'S1','s2'=>'S2','s3'=>'S3']; @endphp
                            <tr x-data="{ editEdu{{ $edu->id }}: false }">
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $eduLevels[$edu->level] ?? strtoupper($edu->level) }}</td>
                                <td class="px-6 py-3 text-gray-700">{{ $edu->institution ?? '—' }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $edu->major ?? '—' }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $edu->graduation_year ?? '—' }}</td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="editEdu{{ $edu->id }} = !editEdu{{ $edu->id }}" type="button"
                                                class="text-xs font-semibold text-[#5586DB] hover:underline">Edit</button>
                                        <button type="button"
                                                @click="showConfirmModal = true; confirmTitle = 'Hapus Pendidikan'; confirmMessage = 'Yakin ingin menghapus data pendidikan ini?'; confirmActionUrl = '{{ route('employees.educations.destroy', [$employee, $edu]) }}'"
                                                class="text-xs font-semibold text-red-500 hover:text-red-700">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr x-show="editEdu{{ $edu->id }}" x-cloak class="bg-gray-50">
                                <td colspan="5" class="px-6 py-3">
                                    <form method="POST" action="{{ route('employees.educations.update', [$employee, $edu]) }}"
                                          class="grid gap-3 sm:grid-cols-4">
                                        @csrf @method('PATCH')
                                        <div>
                                            <select name="level" required class="block w-full border-gray-300 rounded-lg text-sm">
                                                @foreach (['SD','SMP','SMA','D3','S1','S2','S3'] as $lvl)
                                                    <option value="{{ $lvl }}" @selected($edu->level === $lvl)>{{ $lvl }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <input type="text" name="institution" value="{{ $edu->institution }}" required maxlength="200"
                                                   class="block w-full border-gray-300 rounded-lg text-sm">
                                        </div>
                                        <div>
                                            <input type="text" name="major" value="{{ $edu->major }}" maxlength="100"
                                                   class="block w-full border-gray-300 rounded-lg text-sm">
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="number" name="graduation_year" value="{{ $edu->graduation_year }}"
                                                   min="1950" max="{{ date('Y') }}"
                                                   class="block w-full border-gray-300 rounded-lg text-sm">
                                            <button type="submit"
                                                    class="inline-flex items-center bg-[#5586DB] hover:bg-[#4070c5] text-white px-3 py-2 rounded-lg text-xs font-semibold">
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
            <div class="px-6 py-8 text-center">
                <span class="material-symbols-outlined text-3xl text-gray-300">school</span>
                <p class="mt-2 text-sm text-gray-500">Belum ada data pendidikan. Tambahkan riwayat pendidikan Anda.</p>
            </div>
        @endif
    </div>

    {{-- Pengalaman Kerja Sebelumnya --}}
    <div x-data="{ addWork: false }" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-6">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900 font-jakarta">Pengalaman Kerja Sebelumnya</h3>
            <button @click="addWork = !addWork" type="button"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#5586DB] hover:underline">
                <span class="material-symbols-outlined text-[16px]">add</span> Tambah
            </button>
        </div>

        <div x-show="addWork" x-cloak class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <form method="POST" action="{{ route('employees.work-histories.store', $employee) }}"
                  class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @csrf
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Perusahaan <span class="text-red-500">*</span></label>
                    <input type="text" name="company_name" required maxlength="200"
                           class="block w-full border-gray-300 rounded-lg text-sm focus:border-[#5586DB] focus:ring-[#5586DB]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Posisi</label>
                    <input type="text" name="position" maxlength="100"
                           class="block w-full border-gray-300 rounded-lg text-sm focus:border-[#5586DB] focus:ring-[#5586DB]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Tanggal Mulai</label>
                    <input type="date" name="start_date"
                           class="block w-full border-gray-300 rounded-lg text-sm focus:border-[#5586DB] focus:ring-[#5586DB]">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Tanggal Selesai</label>
                    <input type="date" name="end_date"
                           class="block w-full border-gray-300 rounded-lg text-sm focus:border-[#5586DB] focus:ring-[#5586DB]">
                </div>
                <div class="lg:col-span-2">
                    <label class="mb-1 block text-xs font-semibold text-gray-700">Alasan Keluar</label>
                    <div class="flex gap-2">
                        <input type="text" name="reason_leaving" maxlength="500"
                               class="block w-full border-gray-300 rounded-lg text-sm focus:border-[#5586DB] focus:ring-[#5586DB]">
                        <button type="submit"
                                class="inline-flex items-center bg-[#5586DB] hover:bg-[#4070c5] text-white px-3 py-2 rounded-lg text-xs font-semibold whitespace-nowrap">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if ($employee->workHistories->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500">
                        <tr>
                            <th class="px-6 py-3 text-left">Perusahaan</th>
                            <th class="px-6 py-3 text-left">Posisi</th>
                            <th class="px-6 py-3 text-left">Periode</th>
                            <th class="px-6 py-3 text-left">Alasan Keluar</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($employee->workHistories->sortByDesc('start_date') as $work)
                            <tr x-data="{ editWork{{ $work->id }}: false }">
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $work->company_name }}</td>
                                <td class="px-6 py-3 text-gray-700">{{ $work->position ?? '—' }}</td>
                                <td class="px-6 py-3 text-gray-600">
                                    {{ $work->start_date?->format('M Y') ?? '—' }}
                                    @if ($work->end_date) — {{ $work->end_date->format('M Y') }}
                                    @else — sekarang
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-gray-500">{{ $work->reason_leaving ?? '—' }}</td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button @click="editWork{{ $work->id }} = !editWork{{ $work->id }}" type="button"
                                                class="text-xs font-semibold text-[#5586DB] hover:underline">Edit</button>
                                        <button type="button"
                                                @click="showConfirmModal = true; confirmTitle = 'Hapus Pengalaman Kerja'; confirmMessage = 'Yakin ingin menghapus riwayat kerja ini?'; confirmActionUrl = '{{ route('employees.work-histories.destroy', [$employee, $work]) }}'"
                                                class="text-xs font-semibold text-red-500 hover:text-red-700">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr x-show="editWork{{ $work->id }}" x-cloak class="bg-gray-50">
                                <td colspan="5" class="px-6 py-3">
                                    <form method="POST" action="{{ route('employees.work-histories.update', [$employee, $work]) }}"
                                          class="grid gap-3 sm:grid-cols-3">
                                        @csrf @method('PATCH')
                                        <div>
                                            <input type="text" name="company_name" value="{{ $work->company_name }}" required maxlength="200"
                                                   placeholder="Perusahaan" class="block w-full border-gray-300 rounded-lg text-sm">
                                        </div>
                                        <div>
                                            <input type="text" name="position" value="{{ $work->position }}" maxlength="100"
                                                   placeholder="Posisi" class="block w-full border-gray-300 rounded-lg text-sm">
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="date" name="start_date" value="{{ $work->start_date?->format('Y-m-d') }}"
                                                   class="block w-full border-gray-300 rounded-lg text-sm">
                                            <input type="date" name="end_date" value="{{ $work->end_date?->format('Y-m-d') }}"
                                                   class="block w-full border-gray-300 rounded-lg text-sm">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <input type="text" name="reason_leaving" value="{{ $work->reason_leaving }}" maxlength="500"
                                                   placeholder="Alasan keluar" class="block w-full border-gray-300 rounded-lg text-sm">
                                        </div>
                                        <div>
                                            <button type="submit"
                                                    class="inline-flex w-full items-center justify-center bg-[#5586DB] hover:bg-[#4070c5] text-white px-3 py-2 rounded-lg text-xs font-semibold">
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
            <div class="px-6 py-8 text-center">
                <span class="material-symbols-outlined text-3xl text-gray-300">business_center</span>
                <p class="mt-2 text-sm text-gray-500">Belum ada riwayat pekerjaan. Tambahkan pengalaman kerja Anda.</p>
            </div>
        @endif
    </div>
    @endif

    <!-- Modal Konfirmasi Hapus -->
    <template x-teleport="body">
        <div x-show="showConfirmModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display: none;">
            <div class="fixed inset-0 bg-gray-900/50" @click="showConfirmModal = false"></div>
            <div class="relative z-10 w-full max-w-md transform overflow-hidden rounded-lg bg-white text-left shadow-xl">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600">warning</span>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900" x-text="confirmTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" x-text="confirmMessage"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" @click="$refs.confirmForm.submit()" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto" x-text="confirmActionText"></button>
                    <button type="button" @click="showConfirmModal = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
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
