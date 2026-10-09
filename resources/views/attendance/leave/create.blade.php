@extends('layouts.dashboard')

@section('title', 'Ajukan Permohonan - MINDSIA')
@section('header_title', 'Ajukan Permohonan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">

    {{-- Page header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('leave-requests.index') }}"
           class="flex-shrink-0 inline-flex items-center justify-center w-10 h-10 rounded-xl transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
           style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border); color: var(--dash-text-muted); --tw-ring-color: var(--dash-primary);"
           onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'"
           onmouseout="this.style.backgroundColor='var(--dash-card-bg)'">
            <span class="sr-only">Kembali</span>
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
        </a>
        <div>
            <h1 class="font-jakarta text-2xl md:text-3xl font-bold tracking-tight"
                style="color: var(--dash-text-heading);">Ajukan Permohonan</h1>
            <p class="mt-1 text-sm" style="color: var(--dash-text-muted);">
                Isi formulir untuk mengajukan izin, sakit, atau cuti.
            </p>
        </div>
    </div>

    @if($errors->any())
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl border text-sm font-medium mb-6"
             style="background-color: #fee2e2; border-color: #fca5a5; color: #dc2626;">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Form card --}}
    <div class="rounded-2xl overflow-hidden"
         style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
        <form action="{{ route('leave-requests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-6 sm:p-8 space-y-6">

                {{-- Jenis Permohonan --}}
                <div>
                    <label class="block text-sm font-medium mb-2" for="leave_type"
                           style="color: var(--dash-text-body);">
                        Jenis Permohonan <span style="color: #dc2626;" aria-hidden="true">*</span>
                    </label>
                    <select id="leave_type" name="leave_type" required
                            class="block w-full rounded-xl text-sm transition-colors focus:outline-none focus:ring-2 px-3 py-2.5 @error('leave_type') ring-2 ring-red-400 @enderror"
                            style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); --tw-ring-color: var(--dash-primary);">
                        <option value="" disabled @if(!old('leave_type')) selected @endif>Pilih Jenis</option>
                        <option value="permission" @if(old('leave_type') === 'permission') selected @endif>Izin</option>
                        <option value="sick"       @if(old('leave_type') === 'sick') selected @endif>Sakit</option>
                        <option value="leave"      @if(old('leave_type') === 'leave') selected @endif>Cuti</option>
                    </select>
                    @error('leave_type')
                        <p class="mt-1.5 text-xs" style="color: #dc2626;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tanggal --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-2" for="start_date"
                               style="color: var(--dash-text-body);">
                            Mulai Tanggal <span style="color: #dc2626;" aria-hidden="true">*</span>
                        </label>
                        <input id="start_date" name="start_date" type="date"
                               value="{{ old('start_date') }}" required
                               class="block w-full rounded-xl text-sm transition-colors focus:outline-none focus:ring-2 px-3 py-2.5 @error('start_date') ring-2 ring-red-400 @enderror"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); --tw-ring-color: var(--dash-primary);" />
                        @error('start_date')
                            <p class="mt-1.5 text-xs" style="color: #dc2626;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2" for="end_date"
                               style="color: var(--dash-text-body);">
                            Sampai Tanggal <span style="color: #dc2626;" aria-hidden="true">*</span>
                        </label>
                        <input id="end_date" name="end_date" type="date"
                               value="{{ old('end_date') }}" required
                               class="block w-full rounded-xl text-sm transition-colors focus:outline-none focus:ring-2 px-3 py-2.5 @error('end_date') ring-2 ring-red-400 @enderror"
                               style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); --tw-ring-color: var(--dash-primary);" />
                        @error('end_date')
                            <p class="mt-1.5 text-xs" style="color: #dc2626;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Alasan --}}
                <div>
                    <label class="block text-sm font-medium mb-2" for="reason"
                           style="color: var(--dash-text-body);">
                        Alasan / Keterangan <span style="color: #dc2626;" aria-hidden="true">*</span>
                    </label>
                    <textarea id="reason" name="reason" rows="4" required
                              placeholder="Tuliskan keterangan detail..."
                              class="block w-full rounded-xl text-sm transition-colors focus:outline-none focus:ring-2 px-3 py-2.5 @error('reason') ring-2 ring-red-400 @enderror"
                              style="background-color: var(--dash-input-bg); border: 1px solid var(--dash-input-border); color: var(--dash-text-body); --tw-ring-color: var(--dash-primary);">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1.5 text-xs" style="color: #dc2626;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lampiran --}}
                <div>
                    <label class="block text-sm font-medium mb-2" for="attachment"
                           style="color: var(--dash-text-body);">
                        Lampiran Pendukung
                        <span class="text-xs font-normal ml-1" style="color: var(--dash-text-muted);">(opsional)</span>
                    </label>
                    <div class="flex flex-col items-center justify-center px-6 py-8 rounded-xl border-2 border-dashed transition-colors"
                         style="border-color: var(--dash-input-border); background-color: var(--dash-nav-hover-bg);">
                        <span class="material-symbols-outlined text-[40px] mb-2" style="color: var(--dash-text-faint);">upload_file</span>
                        <div class="flex flex-wrap items-center justify-center text-sm gap-1">
                            <label for="attachment"
                                   class="cursor-pointer font-medium hover:opacity-75 transition-opacity"
                                   style="color: var(--dash-primary);">
                                Unggah file
                                <input id="attachment" name="attachment" type="file" class="sr-only">
                            </label>
                            <span style="color: var(--dash-text-muted);">atau seret ke sini</span>
                        </div>
                        <p class="text-xs mt-1" style="color: var(--dash-text-faint);">PNG, JPG, PDF maks. 5MB</p>
                    </div>
                    @error('attachment')
                        <p class="mt-1.5 text-xs" style="color: #dc2626;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 flex items-center justify-end gap-3"
                 style="border-top: 1px solid var(--dash-card-border); background-color: var(--dash-nav-hover-bg);">
                <a href="{{ route('leave-requests.index') }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                   style="border: 1px solid var(--dash-card-border); color: var(--dash-text-body); background-color: var(--dash-card-bg);">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white hover:opacity-90 transition-opacity"
                        style="background-color: var(--dash-primary);">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    Ajukan Permohonan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
