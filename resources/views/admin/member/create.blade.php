@extends('layouts.dashboard')

@section('title', 'Registrasi Member Baru')
@section('header_title', 'Registrasi Member')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    <div class="flex items-center gap-4">
        <a href="{{ route('members.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-0.5" style="color: var(--dash-primary-text);">Member & Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Registrasi Member Baru</h1>
        </div>
    </div>

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl border text-sm"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px] flex-shrink-0 mt-0.5">error</span>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('members.store') }}" method="POST" id="memberForm"
          class="rounded-2xl overflow-hidden"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @csrf

        {{-- Section: Data Pribadi --}}
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Data Pribadi</h3>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Informasi pribadi calon member</p>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                    Nama Lengkap <span style="color:#dc2626;">*</span>
                </label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                       placeholder="Nama sesuai identitas"
                       class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                @error('full_name')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Jenis Kelamin <span style="color:#dc2626;">*</span>
                    </label>
                    <select name="gender" required data-no-enhance
                            class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        <option value="">Pilih</option>
                        <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Tempat Lahir <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" name="birth_place" value="{{ old('birth_place') }}" required
                           placeholder="Kota"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                    @error('birth_place')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Tanggal Lahir <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" required
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                    @error('birthdate')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Email <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           placeholder="email@contoh.com"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                    @error('email')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Nomor WhatsApp <span style="color:#dc2626;">*</span>
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center rounded-l-lg px-3 text-sm font-bold shrink-0"
                              style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border); border-right: none; color: var(--dash-text-muted);">+62</span>
                        <input type="tel" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required
                               placeholder="8xxxxxxxxxx" minlength="8" maxlength="15"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                               class="flex-1 min-w-0 rounded-r-lg px-3 py-2 text-sm focus:outline-none"
                               style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                    </div>
                    @error('whatsapp_number')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Instagram <span class="font-normal" style="color: var(--dash-text-muted);">(Opsional)</span>
                    </label>
                    <input type="text" name="instagram" value="{{ old('instagram') }}"
                           placeholder="@username"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                    @error('instagram')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div></div>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                    Alamat <span style="color:#dc2626;">*</span>
                </label>
                <textarea name="address" rows="2" required
                          class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">{{ old('address') }}</textarea>
                @error('address')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Section: Data Orang Tua --}}
        <div class="px-6 py-4" style="border-top: 1px solid var(--dash-card-border); border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Data Orang Tua</h3>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Informasi orang tua / wali</p>
        </div>

        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Nama Ayah <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" name="father_name" value="{{ old('father_name') }}" required
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                    @error('father_name')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Pekerjaan Ayah <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" name="father_occupation" value="{{ old('father_occupation') }}" required
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                    @error('father_occupation')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Nama Ibu <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" name="mother_name" value="{{ old('mother_name') }}" required
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                    @error('mother_name')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Pekerjaan Ibu <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="text" name="mother_occupation" value="{{ old('mother_occupation') }}" required
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                    @error('mother_occupation')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        WA Ayah <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="tel" name="father_whatsapp" value="{{ old('father_whatsapp') }}" required
                           placeholder="08xxxxxxxxxx" minlength="8" maxlength="15"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                    @error('father_whatsapp')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        WA Ibu <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="tel" name="mother_whatsapp" value="{{ old('mother_whatsapp') }}" required
                           placeholder="08xxxxxxxxxx" minlength="8" maxlength="15"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                    @error('mother_whatsapp')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Section: Cabang & Institusi --}}
        <div class="px-6 py-4" style="border-top: 1px solid var(--dash-card-border); border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Cabang & Institusi</h3>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Cabang MINDSIA dan asal sekolah member</p>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                    Cabang <span style="color:#dc2626;">*</span>
                </label>
                <select name="branch_id" required
                        class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Pilih Cabang</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->branch_name }}
                        </option>
                    @endforeach
                </select>
                @error('branch_id')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                    Sekolah / Institusi <span style="color:#dc2626;">*</span>
                </label>
                <select name="institution_id"
                        class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Pilih dari daftar (atau isi manual di bawah)</option>
                    @foreach(\App\Models\Institution::orderBy('institution_name')->get() as $inst)
                        <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->institution_name }}</option>
                    @endforeach
                </select>
                @error('institution_id')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                    Atau ketik nama sekolah <span class="font-normal" style="color: var(--dash-text-muted);">(jika tidak ada di daftar)</span>
                </label>
                <input type="text" name="institution_name_custom" value="{{ old('institution_name_custom') }}"
                       placeholder="Nama sekolah lainnya"
                       class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                       style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                @error('institution_name_custom')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Jenjang Pendidikan <span class="font-normal" style="color: var(--dash-text-muted);">(Opsional)</span>
                    </label>
                    <select name="education_level" data-no-enhance
                            class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                            style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                        <option value="">Pilih</option>
                        @foreach(['SD','SMP','SMA','SMK','D1','D2','D3','D4','S1','S2','S3','Umum'] as $level)
                            <option value="{{ $level }}" {{ old('education_level') === $level ? 'selected' : '' }}>{{ $level }}</option>
                        @endforeach
                    </select>
                    @error('education_level')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                        Kelas / Jurusan <span class="font-normal" style="color: var(--dash-text-muted);">(Opsional)</span>
                    </label>
                    <input type="text" name="class_or_faculty" value="{{ old('class_or_faculty') }}"
                           placeholder="Contoh: XII IPA 3 / Teknik Informatika"
                           class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);" />
                    @error('class_or_faculty')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Section: Program & Referral --}}
        <div class="px-6 py-4" style="border-top: 1px solid var(--dash-card-border); border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Program & Referral</h3>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Program yang diminati dan marketer yang mereferral</p>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                    Program <span class="font-normal" style="color: var(--dash-text-muted);">(Opsional)</span>
                </label>
                <select name="program_id"
                        class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Pilih Program</option>
                    @foreach(\App\Models\Program::where('is_active', true)->orderBy('program_name')->get() as $prog)
                        <option value="{{ $prog->id }}" {{ old('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->program_name }}</option>
                    @endforeach
                </select>
                @error('program_id')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1.5" style="color: var(--dash-text-body);">
                    Referral Marketer <span class="font-normal" style="color: var(--dash-text-muted);">(Opsional)</span>
                </label>
                <select name="referred_by_employee_id"
                        class="block w-full rounded-lg px-3 py-2 text-sm focus:outline-none"
                        style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-body);">
                    <option value="">Tidak Ada</option>
                    @foreach(\App\Models\Employee::orderBy('full_name')->get() as $emp)
                        <option value="{{ $emp->id }}" {{ old('referred_by_employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('referred_by_employee_id')<p class="text-xs mt-1" style="color:#dc2626;">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Section: Akun Login --}}
        <div class="px-6 py-4" style="border-top: 1px solid var(--dash-card-border); border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-jakarta text-sm font-bold" style="color: var(--dash-text-heading);">Akun Login Member</h3>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Opsional — buatkan akun agar member bisa login ke portal</p>
        </div>

        <div class="p-6 space-y-4">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="create_account" value="1" id="createAccountCheckbox"
                       class="h-4 w-4 rounded"
                       {{ old('create_account') ? 'checked' : '' }} />
                <div>
                    <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">Buat akun login member</span>
                    <p class="text-xs" style="color: var(--dash-text-muted);">Email verifikasi akan dikirim otomatis.</p>
                </div>
            </label>

            <div id="accountInfoPanel" class="hidden rounded-xl px-4 py-3"
                 style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[20px]" style="color: var(--dash-primary);">info</span>
                    <div class="text-sm" style="color: var(--dash-primary-text);">
                        <p class="font-semibold">Akun dibuat otomatis</p>
                        <p class="text-xs mt-1">Password sementara di-generate, email verifikasi dikirim ke alamat di atas. Member harus verifikasi email dan ganti password saat login pertama.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 px-6 py-4"
             style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <a href="{{ route('members.index') }}"
               class="px-5 py-2 rounded-lg text-sm font-medium transition-colors inline-flex items-center gap-1.5"
               style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[16px]">close</span>Batal</a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[16px]">person_add</span>Daftar Member
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox = document.getElementById('createAccountCheckbox');
        const panel = document.getElementById('accountInfoPanel');
        checkbox.addEventListener('change', () => panel.classList.toggle('hidden', !checkbox.checked));
        panel.classList.toggle('hidden', !checkbox.checked);
    });
</script>
@endpush
