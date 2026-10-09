@extends('layouts.dashboard')

@section('title', 'Edit Member')
@section('header_title', 'Edit Member')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-4xl mx-auto space-y-6">

    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Data Member</h1>
        </div>
        <a href="{{ route('members.show', $member) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold rounded-lg transition-colors shrink-0"
           style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="flex gap-3 px-4 py-3 rounded-xl text-sm"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <span class="material-symbols-outlined text-[20px] shrink-0">error</span>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('members.update', $member) }}" method="POST"
          class="rounded-2xl overflow-hidden"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @csrf @method('PUT')

        {{-- Section: Data Pribadi --}}
        <div class="px-6 py-4" style="border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Data Pribadi</h3>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name', $member->full_name) }}" required
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                @error('full_name')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select name="gender" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih</option>
                        <option value="L" {{ old('gender', $member->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $member->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" name="birthdate" value="{{ old('birthdate', $member->birthdate?->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'" />
                    @error('birthdate')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $member->email) }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('email')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Nomor WhatsApp <span class="text-red-500">*</span></label>
                    <div class="flex rounded-lg overflow-hidden" style="border: 1px solid var(--dash-input-border);">
                        <span class="inline-flex items-center px-3 text-sm font-bold shrink-0"
                              style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted); border-right: 1px solid var(--dash-input-border);">+62</span>
                        <input type="tel" name="whatsapp_number" value="{{ old('whatsapp_number', $member->whatsapp_number) }}" required
                               placeholder="8xxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                               class="w-full min-w-0 flex-1 px-4 py-2.5 text-sm focus:outline-none"
                               style="background-color: var(--dash-input-bg); color: var(--dash-text-heading);"
                               minlength="8" maxlength="15" />
                    </div>
                    @error('whatsapp_number')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Instagram <span style="color: var(--dash-text-faint);">(Opsional)</span></label>
                <input type="text" name="instagram" value="{{ old('instagram', $member->instagram) }}"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'" />
                @error('instagram')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Alamat <span style="color: var(--dash-text-faint);">(Opsional)</span></label>
                <textarea name="address" rows="2"
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'">{{ old('address', $member->address) }}</textarea>
                @error('address')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Section: Data Orang Tua --}}
        <div class="px-6 py-4" style="border-top: 1px solid var(--dash-card-border); border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Data Orang Tua</h3>
        </div>

        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Nama Ayah</label>
                    <input type="text" name="father_name" value="{{ old('father_name', $member->father_name) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'" />
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Pekerjaan Ayah</label>
                    <input type="text" name="father_occupation" value="{{ old('father_occupation', $member->father_occupation) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Nama Ibu</label>
                    <input type="text" name="mother_name" value="{{ old('mother_name', $member->mother_name) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'" />
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Pekerjaan Ibu</label>
                    <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $member->mother_occupation) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">WA Ayah</label>
                    <input type="tel" name="father_whatsapp" value="{{ old('father_whatsapp', $member->father_whatsapp) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'"
                           minlength="8" maxlength="15" />
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">WA Ibu</label>
                    <input type="tel" name="mother_whatsapp" value="{{ old('mother_whatsapp', $member->mother_whatsapp) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'"
                           minlength="8" maxlength="15" />
                </div>
            </div>
        </div>

        {{-- Section: Institusi & Program --}}
        <div class="px-6 py-4" style="border-top: 1px solid var(--dash-card-border); border-bottom: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h3 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Institusi & Program</h3>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Institusi / Cabang <span class="text-red-500">*</span></label>
                <select name="institution_id" required
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Pilih Institusi</option>
                    @foreach(\App\Models\Institution::orderBy('institution_name')->get() as $inst)
                        <option value="{{ $inst->id }}" {{ old('institution_id', $member->institution_id) == $inst->id ? 'selected' : '' }}>{{ $inst->institution_name }}</option>
                    @endforeach
                </select>
                @error('institution_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold mb-1" style="color: var(--dash-text-muted);">Program</label>
                <select name="program_id"
                        class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                        style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                        onfocus="this.style.borderColor='var(--dash-primary)'"
                        onblur="this.style.borderColor='var(--dash-input-border)'">
                    <option value="">Pilih Program</option>
                    @foreach(\App\Models\Program::where('is_active', true)->orderBy('program_name')->get() as $prog)
                        <option value="{{ $prog->id }}" {{ old('program_id', $member->program_id) == $prog->id ? 'selected' : '' }}>{{ $prog->program_name }}</option>
                    @endforeach
                </select>
                @error('program_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="px-6 py-4 flex gap-3 justify-end" style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <a href="{{ route('members.show', $member) }}"
               class="px-5 py-2.5 text-sm font-semibold rounded-lg transition-colors inline-flex items-center gap-1.5"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-card-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'"><span class="material-symbols-outlined text-[16px]">close</span>Batal</a>
            <button type="submit"
                    class="px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity inline-flex items-center gap-1.5"
                    style="background-color: var(--dash-primary);"><span class="material-symbols-outlined text-[16px]">save</span>Simpan Perubahan</button>
        </div>
    </form>

</div>
@endsection
