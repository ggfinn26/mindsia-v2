@extends('layouts.dashboard')

@section('title', 'Edit Member')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-[#725c00]">Member & Marketing</p>
            <h2 class="font-jakarta text-2xl font-bold tracking-tight text-slate-900">Edit Data Member</h2>
        </div>
        <a href="{{ route('members.show', $member) }}" class="inline-flex min-h-10 items-center gap-2 border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
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

    <form action="{{ route('members.update', $member) }}" method="POST" class="border border-slate-200 bg-white">
        @csrf @method('PUT')

        <!-- Section: Data Pribadi -->
        <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Data Pribadi</h3>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name', $member->full_name) }}" required
                       class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                @error('full_name')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select name="gender" required class="w-full border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                        <option value="">-- Pilih --</option>
                        <option value="L" {{ old('gender', $member->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $member->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" name="birthdate" value="{{ old('birthdate', $member->birthdate?->format('Y-m-d')) }}" required
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                    @error('birthdate')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $member->email) }}" required
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                    @error('email')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
                    <input type="tel" name="whatsapp_number" value="{{ old('whatsapp_number', $member->whatsapp_number) }}" required
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                    @error('whatsapp_number')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Instagram <span class="text-slate-400">(Opsional)</span></label>
                <input type="text" name="instagram" value="{{ old('instagram', $member->instagram) }}"
                       class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                @error('instagram')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Alamat <span class="text-slate-400">(Opsional)</span></label>
                <textarea name="address" rows="2"
                          class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">{{ old('address', $member->address) }}</textarea>
                @error('address')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Section: Data Orang Tua -->
        <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Data Orang Tua</h3>
        </div>

        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Ayah</label>
                    <input type="text" name="father_name" value="{{ old('father_name', $member->father_name) }}"
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Pekerjaan Ayah</label>
                    <input type="text" name="father_occupation" value="{{ old('father_occupation', $member->father_occupation) }}"
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Nama Ibu</label>
                    <input type="text" name="mother_name" value="{{ old('mother_name', $member->mother_name) }}"
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Pekerjaan Ibu</label>
                    <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $member->mother_occupation) }}"
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">WA Ayah</label>
                    <input type="tel" name="father_whatsapp" value="{{ old('father_whatsapp', $member->father_whatsapp) }}"
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">WA Ibu</label>
                    <input type="tel" name="mother_whatsapp" value="{{ old('mother_whatsapp', $member->mother_whatsapp) }}"
                           class="w-full border border-slate-300 px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]" />
                </div>
            </div>
        </div>

        <!-- Section: Institusi & Program -->
        <div class="border-b border-slate-200 bg-slate-50/50 px-6 py-4">
            <h3 class="font-jakarta text-base font-bold text-slate-900">Institusi & Program</h3>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Institusi / Cabang <span class="text-red-500">*</span></label>
                <select name="institution_id" required class="w-full border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                    <option value="">-- Pilih Institusi --</option>
                    @foreach(\App\Models\Institution::orderBy('institution_name')->get() as $inst)
                        <option value="{{ $inst->id }}" {{ old('institution_id', $member->institution_id) == $inst->id ? 'selected' : '' }}>{{ $inst->institution_name }}</option>
                    @endforeach
                </select>
                @error('institution_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Program</label>
                <select name="program_id" class="w-full border border-slate-300 bg-white px-4 py-2.5 text-sm focus:border-[#215aac] focus:ring-1 focus:ring-[#215aac]">
                    <option value="">-- Pilih Program --</option>
                    @foreach(\App\Models\Program::where('is_active', true)->orderBy('program_name')->get() as $prog)
                        <option value="{{ $prog->id }}" {{ old('program_id', $member->program_id) == $prog->id ? 'selected' : '' }}>{{ $prog->program_name }}</option>
                    @endforeach
                </select>
                @error('program_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="border-t border-slate-200 bg-slate-50 px-6 py-4 flex gap-3 justify-end">
            <a href="{{ route('members.show', $member) }}" class="px-6 py-2.5 text-sm font-semibold text-slate-600 border border-slate-300 hover:bg-slate-50">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-[#215aac] text-white text-sm font-semibold hover:bg-[#194a91]">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
