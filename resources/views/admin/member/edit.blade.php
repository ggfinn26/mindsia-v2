@extends('layouts.dashboard')

@section('title', 'Edit Member - MINDSIA')
@section('header_title', 'Edit Member')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">
    {{-- Page Header Pola B3 --}}
    <div>
        <a href="{{ route('members.show', $member) }}" class="dash-back mb-3 inline-flex items-center gap-1.5 text-xs font-semibold">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Kembali ke Detail Member
        </a>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider mb-1" style="color: var(--dash-primary-text);">Member & Marketing</p>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight" style="color: var(--dash-text-heading);">Edit Data Member</h1>
            <p class="text-sm mt-1" style="color: var(--dash-text-muted);">Perbarui profil lengkap, informasi kontak, dan data institusi member.</p>
        </div>
    </div>

    {{-- Error validation banner --}}
    @if ($errors->any())
        <div class="px-4 py-3 rounded-xl text-sm font-medium"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
            <div class="flex items-center gap-2 mb-2 font-semibold">
                <span class="material-symbols-outlined text-[18px]">error</span>
                <span>Terdapat kesalahan pada input formulir:</span>
            </div>
            <ul class="list-disc list-inside space-y-1 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('members.update', $member) }}" method="POST"
          class="rounded-2xl shadow-sm"
          style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        @csrf
        @method('PUT')

        {{-- Section 1: Data Pribadi --}}
        <div class="px-6 py-4 rounded-t-2xl border-b"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Data Pribadi</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Informasi identitas lengkap member</p>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="full_name" value="{{ old('full_name', $member->full_name) }}" required
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                @error('full_name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select name="gender" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('gender', $member->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $member->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="birthdate" value="{{ old('birthdate', $member->birthdate?->format('Y-m-d')) }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('birthdate') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $member->email) }}" required
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                    @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                        Nomor WhatsApp <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center rounded-l-lg border border-r-0 px-3 text-sm font-bold"
                              style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-input-border); color: var(--dash-text-muted);">+62</span>
                        <input type="tel" name="whatsapp_number" value="{{ old('whatsapp_number', preg_replace('/^(\+?62|0)/', '', $member->whatsapp_number ?? '')) }}" required
                               placeholder="8xxxxxxxxxx" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')"
                               class="w-full min-w-0 flex-1 rounded-r-lg px-4 py-2.5 text-sm focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                               minlength="8" maxlength="15" />
                    </div>
                    @error('whatsapp_number') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Instagram <span style="color: var(--dash-text-faint);">(Opsional)</span>
                </label>
                <input type="text" name="instagram" value="{{ old('instagram', $member->instagram) }}"
                       class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                       style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                       onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                       onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                @error('instagram') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Alamat <span style="color: var(--dash-text-faint);">(Opsional)</span>
                </label>
                <textarea name="address" rows="2"
                          class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                          style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                          onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                          onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'">{{ old('address', $member->address) }}</textarea>
                @error('address') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Section 2: Data Orang Tua --}}
        <div class="px-6 py-4 border-b border-t"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Data Orang Tua / Wali</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Informasi pekerjaan dan kontak orang tua</p>
        </div>

        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">Nama Ayah</label>
                    <input type="text" name="father_name" value="{{ old('father_name', $member->father_name) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">Pekerjaan Ayah</label>
                    <input type="text" name="father_occupation" value="{{ old('father_occupation', $member->father_occupation) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">Nama Ibu</label>
                    <input type="text" name="mother_name" value="{{ old('mother_name', $member->mother_name) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">Pekerjaan Ibu</label>
                    <input type="text" name="mother_occupation" value="{{ old('mother_occupation', $member->mother_occupation) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">WA Ayah</label>
                    <input type="tel" name="father_whatsapp" value="{{ old('father_whatsapp', $member->father_whatsapp) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                           minlength="8" maxlength="15" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">WA Ibu</label>
                    <input type="tel" name="mother_whatsapp" value="{{ old('mother_whatsapp', $member->mother_whatsapp) }}"
                           class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                           style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                           onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                           onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'"
                           minlength="8" maxlength="15" />
                </div>
            </div>
        </div>

        {{-- Section 3: Institusi & Program --}}
        <div class="px-6 py-4 border-b border-t"
             style="border-color: var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
            <h2 class="font-jakarta text-base font-bold" style="color: var(--dash-text-heading);">Institusi & Program</h2>
            <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Pilihan sekolah/institusi dan program bimbingan</p>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Institusi / Cabang <span class="text-red-500">*</span>
                </label>
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
                @error('institution_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wide mb-1.5" style="color: var(--dash-text-body);">
                    Program
                </label>
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
                @error('program_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Actions Footer --}}
        <div class="px-6 py-4 rounded-b-2xl flex items-center justify-end gap-3 border-t"
             style="background-color: var(--dash-nav-hover-bg); border-color: var(--dash-card-border);">
            <a href="{{ route('members.show', $member) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
               style="color: var(--dash-text-muted); border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg);"
               onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
               onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-lg hover:opacity-90 transition-opacity"
                    style="background-color: var(--dash-primary);">
                <span class="material-symbols-outlined text-[18px]">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
