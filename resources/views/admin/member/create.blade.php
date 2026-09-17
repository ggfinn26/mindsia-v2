@extends('layouts.dashboard')

@section('title', 'Registrasi Member Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Member & Marketing</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Registrasi Member Baru</h2>
        </div>
        <a href="{{ route('members.index') }}" class="inline-flex min-h-10 items-center gap-2 border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="flex gap-3 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('members.store') }}" method="POST" id="memberForm" class="border border-slate-200 bg-white">
        @csrf

        <!-- Section: Data Pribadi -->
        <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Data Pribadi</h3>
            <p class="text-xs text-slate-500 mt-0.5">Informasi pribadi calon member</p>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required
                       class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                       placeholder="Nama sesuai identitas" />
                @error('full_name')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select name="gender" required class="w-full border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                        <option value="">-- Pilih --</option>
                        <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" name="birthdate" value="{{ old('birthdate') }}" required
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                    @error('birthdate')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                           placeholder="email@contoh.com" />
                    @error('email')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
                    <input type="tel" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                           placeholder="08xxxxxxxxxx" />
                    @error('whatsapp_number')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Instagram <span class="text-slate-400">(Opsional)</span></label>
                <input type="text" name="instagram" value="{{ old('instagram') }}"
                       class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                       placeholder="@username" />
                @error('instagram')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Alamat <span class="text-slate-400">(Opsional)</span></label>
                <textarea name="address" rows="2"
                          class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Section: Data Orang Tua -->
        <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Data Orang Tua</h3>
            <p class="text-xs text-slate-500 mt-0.5">Informasi orang tua / wali</p>
        </div>

        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Ayah <span class="text-slate-400">(Opsional)</span></label>
                    <input type="text" name="father_name" value="{{ old('father_name') }}"
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                    @error('father_name')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Pekerjaan Ayah <span class="text-slate-400">(Opsional)</span></label>
                    <input type="text" name="father_occupation" value="{{ old('father_occupation') }}"
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                    @error('father_occupation')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Ibu <span class="text-slate-400">(Opsional)</span></label>
                    <input type="text" name="mother_name" value="{{ old('mother_name') }}"
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                    @error('mother_name')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Pekerjaan Ibu <span class="text-slate-400">(Opsional)</span></label>
                    <input type="text" name="mother_occupation" value="{{ old('mother_occupation') }}"
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                    @error('mother_occupation')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">WA Ayah <span class="text-slate-400">(Opsional)</span></label>
                    <input type="tel" name="father_whatsapp" value="{{ old('father_whatsapp') }}"
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                           placeholder="08xxxxxxxxxx" />
                    @error('father_whatsapp')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">WA Ibu <span class="text-slate-400">(Opsional)</span></label>
                    <input type="tel" name="mother_whatsapp" value="{{ old('mother_whatsapp') }}"
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]"
                           placeholder="08xxxxxxxxxx" />
                    @error('mother_whatsapp')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section: Institusi & Program -->
        <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Institusi & Program</h3>
            <p class="text-xs text-slate-500 mt-0.5">Asal institusi dan program yang diminati</p>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Institusi / Cabang <span class="text-red-500">*</span></label>
                <select name="institution_id" required class="w-full border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                    <option value="">-- Pilih Institusi --</option>
                    @foreach(\App\Models\Institution::orderBy('institution_name')->get() as $inst)
                        <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->institution_name }}</option>
                    @endforeach
                </select>
                @error('institution_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Program <span class="text-slate-400">(Opsional)</span></label>
                <select name="program_id" class="w-full border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                    <option value="">-- Pilih Program --</option>
                    @foreach(\App\Models\Program::where('is_active', true)->orderBy('program_name')->get() as $prog)
                        <option value="{{ $prog->id }}" {{ old('program_id') == $prog->id ? 'selected' : '' }}>{{ $prog->program_name }}</option>
                    @endforeach
                </select>
                @error('program_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Section: Akun Login -->
        <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Akun Login Member</h3>
            <p class="text-xs text-slate-500 mt-0.5">Opsional — buatkan akun agar member bisa login ke portal</p>
        </div>

        <div class="p-6 space-y-5">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="create_account" value="1" id="createAccountCheckbox"
                       class="rounded border-slate-300 text-[#215aac] focus:ring-[#215aac]"
                       {{ old('create_account') ? 'checked' : '' }} />
                <div>
                    <span class="text-sm font-semibold text-slate-800">Buat akun login member</span>
                    <p class="text-xs text-slate-500">Centang untuk membuat akun login. Email verifikasi akan dikirim otomatis.</p>
                </div>
            </label>

            <!-- Account creation info (shown when checkbox is checked) -->
            <div id="accountInfoPanel" class="hidden border border-blue-200 bg-blue-50/50 px-5 py-4 space-y-3">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-blue-600 text-[20px]">info</span>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold">Akun akan dibuat secara otomatis</p>
                        <p class="text-xs mt-1">Password sementara akan di-generate dan email verifikasi akan dikirim ke alamat email yang diisi di atas. Member harus verifikasi email dan mengganti password saat login pertama.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 flex gap-3 justify-end">
            <a href="{{ route('members.index') }}" class="px-6 py-2.5 text-sm font-semibold text-slate-600 border border-slate-300 hover:bg-slate-50">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-[#215aac] text-white text-sm font-semibold hover:bg-[#194a91]">
                Daftar Member
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox = document.getElementById('createAccountCheckbox');
        const panel = document.getElementById('accountInfoPanel');

        function togglePanel() {
            panel.classList.toggle('hidden', !checkbox.checked);
        }

        checkbox.addEventListener('change', togglePanel);
        togglePanel();
    });
</script>
@endsection
