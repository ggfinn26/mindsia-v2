@extends('layouts.dashboard')

@section('title', 'Edit Profil - MINDSIA')
@section('header_title', 'Edit Data Diri')

@section('content')
<div class="max-w-2xl mx-auto">

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 mb-6 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#5586DB] text-[20px]">person</span>
            <h2 class="font-bold text-gray-800 font-jakarta">Data Diri</h2>
        </div>

        <form action="{{ route('applicant.profile.update') }}" method="POST" class="px-6 py-5 space-y-5">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $applicant->full_name) }}"
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. WhatsApp</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 text-sm text-gray-500 bg-gray-100 border border-gray-200 border-r-0 rounded-l-xl font-semibold">+62</span>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $applicant->whatsapp_number) }}"
                               minlength="8" maxlength="15" placeholder="8xxxxxxxxxx"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-r-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Jenis Kelamin</label>
                    <select name="gender"
                            class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        <option value="">Pilih...</option>
                        <option value="male" {{ old('gender', $applicant->gender) === 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $applicant->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date"
                           value="{{ old('birth_date', $applicant->birth_date?->format('Y-m-d')) }}"
                           max="{{ now()->subYears(17)->format('Y-m-d') }}"
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kota Domisili</label>
                    <input type="text" name="city" value="{{ old('city', $applicant->city) }}"
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Alamat Lengkap</label>
                    <textarea name="address" rows="3"
                              class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">{{ old('address', $applicant->address) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('applicant.profile.show') }}"
                   class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-[#5586DB] hover:bg-[#4470c4] rounded-xl transition-colors shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
