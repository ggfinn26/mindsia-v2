@extends('layouts.dashboard')

@section('title', 'Edit Cabang')
@section('header_title', 'Edit Cabang')

@section('content')
<div class="max-w-3xl mx-auto">

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

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[#5586DB] text-[20px]">store</span>
                <h2 class="font-bold text-gray-800 font-jakarta">Edit Cabang: {{ $branch->branch_name }}</h2>
            </div>
            <a href="{{ route('branches.index') }}" class="text-[13px] font-medium text-gray-500 hover:text-gray-800 flex items-center gap-1 transition-colors">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> Kembali
            </a>
        </div>

        <form action="{{ route('branches.update', $branch->id) }}" method="POST" class="px-6 py-5 space-y-5">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kode Cabang</label>
                    <input type="text" name="code_branches" value="{{ old('code_branches', $branch->code_branches) }}" required 
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Cabang</label>
                    <input type="text" name="branch_name" value="{{ old('branch_name', $branch->branch_name) }}" required 
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Area</label>
                    <select name="areas_id" required 
                            class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        <option value="">-- Pilih Area --</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ old('areas_id', $branch->areas_id) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Alamat</label>
                    <textarea name="address" rows="3" 
                              class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">{{ old('address', $branch->address) }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">URL Google Maps</label>
                    <input type="url" name="gmaps_url" value="{{ old('gmaps_url', $branch->gmaps_url) }}" 
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">No. WhatsApp</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 text-sm text-gray-500 bg-gray-100 border border-gray-200 border-r-0 rounded-l-xl font-semibold">+62</span>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $branch->whatsapp) }}" placeholder="8xxxxxxxxxx" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')" minlength="8" maxlength="15"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-r-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Instagram</label>
                    <input type="text" name="instagram" value="{{ old('instagram', $branch->instagram) }}" 
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Latitude</label>
                    <input type="number" step="any" name="latitude" value="{{ old('latitude', $branch->latitude) }}" 
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Longitude</label>
                    <input type="number" step="any" name="longitude" value="{{ old('longitude', $branch->longitude) }}" 
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Radius (Meter)</label>
                    <input type="number" name="radius_meters" value="{{ old('radius_meters', $branch->radius_meters) }}" 
                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>

                <div class="flex items-center pt-2">
                    <input type="hidden" name="is_active" value="0">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $branch->is_active) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#5586DB]/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#5586DB]"></div>
                        <span class="ml-3 text-[13px] font-medium text-gray-700">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 mt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-[#5586DB] hover:bg-[#4A78C4] text-white px-6 py-2.5 rounded-xl text-[13px] font-bold shadow-sm transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
