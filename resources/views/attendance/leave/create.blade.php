@extends('layouts.dashboard')

@section('title', 'Ajukan Permohonan - MINDSIA')
@section('header_title', 'Ajukan Permohonan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div>
        <a href="{{ route('leave-requests.index') }}" class="dash-back mb-3">
            <span class="material-symbols-outlined">arrow_back</span>Kembali ke Daftar Permohonan
        </a>
        <p class="text-xs font-semibold uppercase tracking-wider mb-1"
           style="color: var(--dash-primary-text);">Personal Dashboard</p>
        <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
            style="color: var(--dash-text-heading);">Ajukan Permohonan</h1>
        <p class="text-sm mt-1" style="color: var(--dash-text-muted);">
            Isi formulir di bawah untuk mengajukan izin, sakit, atau cuti.
        </p>
    </div>

    {{-- Form Card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-6 sm:p-8 space-y-6">
                {{-- Jenis Permohonan --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);" for="leave_type">
                        Jenis Permohonan <span class="text-rose-500">*</span>
                    </label>
                    <select id="leave_type" name="leave_type" required
                            class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all cursor-pointer"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                            onfocus="this.style.borderColor='var(--dash-primary)'"
                            onblur="this.style.borderColor='var(--dash-input-border)'">
                        <option value="" disabled @if(!old('leave_type')) selected @endif>Pilih Jenis Permohonan</option>
                        <option value="permission" @if(old('leave_type') === 'permission') selected @endif>Izin</option>
                        <option value="sick" @if(old('leave_type') === 'sick') selected @endif>Sakit</option>
                        <option value="leave" @if(old('leave_type') === 'leave') selected @endif>Cuti</option>
                    </select>
                    @error('leave_type')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal Mulai & Sampai --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);" for="start_date">
                            Mulai Tanggal <span class="text-rose-500">*</span>
                        </label>
                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}" required
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                        @error('start_date')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);" for="end_date">
                            Sampai Tanggal <span class="text-rose-500">*</span>
                        </label>
                        <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" required
                               class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                               onfocus="this.style.borderColor='var(--dash-primary)'; this.style.boxShadow='0 0 0 2px color-mix(in srgb, var(--dash-primary) 20%, transparent)'"
                               onblur="this.style.borderColor='var(--dash-input-border)'; this.style.boxShadow='none'" />
                        @error('end_date')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Alasan / Keterangan --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);" for="reason">
                        Alasan / Keterangan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="reason" name="reason" rows="4" required
                              class="w-full px-4 py-2.5 text-sm rounded-lg focus:outline-none transition-all"
                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-heading);"
                              onfocus="this.style.borderColor='var(--dash-primary)'"
                              onblur="this.style.borderColor='var(--dash-input-border)'"
                              placeholder="Tuliskan keterangan detail...">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lampiran Pendukung --}}
                <div>
                    <label class="block text-sm font-medium mb-1" style="color: var(--dash-text-body);" for="attachment">
                        Lampiran Pendukung (opsional)
                    </label>
                    <input id="attachment" name="attachment" type="file" class="dash-file-input" accept=".png,.jpg,.jpeg,.pdf">
                    <p class="text-xs mt-1" style="color: var(--dash-text-faint);">Format berkas: PNG, JPG, PDF (maksimal 5MB)</p>
                    @error('attachment')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Action buttons footer --}}
            <div class="px-6 py-4 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('leave-requests.index') }}"
                   class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors"
                   style="border: 1px solid var(--dash-card-border); background-color: var(--dash-card-bg); color: var(--dash-text-body);"
                   onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
                   onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    Ajukan Permohonan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

