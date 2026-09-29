@extends('layouts.dashboard')

@section('title', 'Profil Saya - MINDSIA')
@section('header_title', 'Profil Saya')



@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-md text-green-700 text-sm font-medium">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-md text-red-700 text-sm">
            <span class="material-symbols-outlined text-[18px] mt-0.5">error</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- DATA DIRI --}}
    <div id="data-diri" class="bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <h2 class="font-bold text-gray-800 font-jakarta">Data Diri</h2>
            </div>
            <a href="{{ route('applicant.profile.edit') }}" class="text-xs font-semibold text-[#5586DB] hover:text-[#4470c4] flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">edit</span> Edit
            </a>
        </div>
        <div class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-0.5">Nama Lengkap</p>
                <p class="text-sm font-semibold text-gray-800">{{ $applicant->full_name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-0.5">Email</p>
                <p class="text-sm text-gray-700">{{ $applicant->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-0.5">No. WhatsApp</p>
                <p class="text-sm text-gray-700">{{ $applicant->whatsapp_number ? '+62'.$applicant->whatsapp_number : '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-0.5">Jenis Kelamin</p>
                <p class="text-sm text-gray-700">
                    @if($applicant->gender === 'male') Laki-laki
                    @elseif($applicant->gender === 'female') Perempuan
                    @else -
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-0.5">Tanggal Lahir</p>
                <p class="text-sm text-gray-700">{{ $applicant->birth_date?->translatedFormat('d F Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600 mb-0.5">Kota Domisili</p>
                <p class="text-sm text-gray-700">{{ $applicant->city ?? '-' }}</p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-sm font-medium text-gray-600 mb-0.5">Alamat</p>
                <p class="text-sm text-gray-700">{{ $applicant->address ?? '-' }}</p>
            </div>
        </div>
    </div>

    {{-- PENDIDIKAN --}}
    <div id="pendidikan" x-data="{ showForm: false }" class="bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <h2 class="font-bold text-gray-800 font-jakarta">Pendidikan</h2>
            </div>
            <button @click="showForm = !showForm" class="text-xs font-semibold text-[#5586DB] hover:text-[#4470c4] flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]" x-text="showForm ? 'close' : 'add'">add</span>
                <span x-text="showForm ? 'Tutup' : 'Tambah'">Tambah</span>
            </button>
        </div>

        <div x-show="showForm" x-collapse class="border-b border-gray-100 bg-[#F8FAFC]">
            <form action="{{ route('applicant.profile.education.store') }}" method="POST" class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Institusi *</label>
                    <input type="text" name="institution_name" value="{{ old('institution_name') }}" required
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Jenjang *</label>
                    <select name="education_level" required
                            class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        <option value="">Pilih...</option>
                        @foreach(['sd'=>'SD','smp'=>'SMP','sma'=>'SMA/SMK','d1'=>'D1','d2'=>'D2','d3'=>'D3','d4'=>'D4','s1'=>'S1','s2'=>'S2','s3'=>'S3'] as $val=>$label)
                            <option value="{{ $val }}" {{ old('education_level')==$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Jurusan *</label>
                    <input type="text" name="major" value="{{ old('major') }}" required
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">IPK *</label>
                    <input type="number" name="gpa" value="{{ old('gpa') }}" step="0.01" min="0" max="4" required
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Mulai *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Lulus <span class="font-normal text-gray-400">(kosong jika masih berjalan)</span></label>
                    <input type="date" name="graduation_date" value="{{ old('graduation_date') }}"
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div class="sm:col-span-2 flex justify-end">
                    <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-[#5586DB] hover:bg-[#4470c4] rounded-md transition-colors">
                        Simpan Pendidikan
                    </button>
                </div>
            </form>
        </div>

        <div class="divide-y divide-gray-50">
            @forelse($applicant->educations->sortByDesc('start_date') as $edu)
                <div x-data="{ editing: false }" class="px-6 py-4">
                    <div x-show="!editing" class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-800">{{ $edu->institution_name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ strtoupper($edu->education_level) }} · {{ $edu->major }}
                                @if($edu->gpa) · IPK {{ number_format($edu->gpa, 2) }} @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $edu->start_date->format('M Y') }} -
                                {{ $edu->graduation_date ? $edu->graduation_date->format('M Y') : 'Sekarang' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button @click="editing = true" class="p-1.5 text-gray-400 hover:text-[#5586DB] rounded-lg hover:bg-blue-50 transition-colors">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                            </button>
                            <form action="{{ route('applicant.profile.education.destroy', $edu) }}" method="POST"
                                  onsubmit="return confirm('Hapus data pendidikan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <form x-show="editing" action="{{ route('applicant.profile.education.update', $edu) }}" method="POST"
                          class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Nama Institusi *</label>
                            <input type="text" name="institution_name" value="{{ $edu->institution_name }}" required
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Jenjang *</label>
                            <select name="education_level" required
                                    class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                                @foreach(['sd'=>'SD','smp'=>'SMP','sma'=>'SMA/SMK','d1'=>'D1','d2'=>'D2','d3'=>'D3','d4'=>'D4','s1'=>'S1','s2'=>'S2','s3'=>'S3'] as $val=>$label)
                                    <option value="{{ $val }}" {{ $edu->education_level===$val?'selected':'' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Jurusan *</label>
                            <input type="text" name="major" value="{{ $edu->major }}" required
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">IPK *</label>
                            <input type="number" name="gpa" value="{{ $edu->gpa }}" step="0.01" min="0" max="4" required
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Mulai *</label>
                            <input type="date" name="start_date" value="{{ $edu->start_date->format('Y-m-d') }}" required
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Lulus</label>
                            <input type="date" name="graduation_date" value="{{ $edu->graduation_date?->format('Y-m-d') }}"
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div class="sm:col-span-2 flex items-center gap-2 justify-end">
                            <button type="button" @click="editing = false" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-[#5586DB] hover:bg-[#4470c4] rounded-md transition-colors">Simpan</button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="px-6 py-8 text-center">
                    
                    <p class="text-sm text-gray-400 mt-2">Belum ada data pendidikan.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- PENGALAMAN KERJA --}}
    <div id="pengalaman" x-data="{ showForm: false }" class="bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <h2 class="font-bold text-gray-800 font-jakarta">Pengalaman Kerja</h2>
            </div>
            <button @click="showForm = !showForm" class="text-xs font-semibold text-[#5586DB] hover:text-[#4470c4] flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]" x-text="showForm ? 'close' : 'add'">add</span>
                <span x-text="showForm ? 'Tutup' : 'Tambah'">Tambah</span>
            </button>
        </div>

        <div x-show="showForm" x-collapse class="border-b border-gray-100 bg-[#F8FAFC]">
            <form action="{{ route('applicant.profile.work-experience.store') }}" method="POST" class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Perusahaan *</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" required
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Jabatan *</label>
                    <input type="text" name="position" value="{{ old('position') }}" required
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Mulai *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Selesai <span class="font-normal text-gray-400">(kosong jika masih bekerja)</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Alasan Keluar</label>
                    <textarea name="reason_for_leaving" rows="2"
                              class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">{{ old('reason_for_leaving') }}</textarea>
                </div>
                <div class="sm:col-span-2 flex justify-end">
                    <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-[#5586DB] hover:bg-[#4470c4] rounded-md transition-colors">
                        Simpan Pengalaman
                    </button>
                </div>
            </form>
        </div>

        <div class="divide-y divide-gray-50">
            @forelse($applicant->workExperiences->sortByDesc('start_date') as $exp)
                <div x-data="{ editing: false }" class="px-6 py-4">
                    <div x-show="!editing" class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-800">{{ $exp->position }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $exp->company_name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $exp->start_date->format('M Y') }} -
                                {{ $exp->end_date ? $exp->end_date->format('M Y') : 'Sekarang' }}
                            </p>
                            @if($exp->reason_for_leaving)
                                <p class="text-xs text-gray-400 mt-1 italic">Alasan keluar: {{ $exp->reason_for_leaving }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button @click="editing = true" class="p-1.5 text-gray-400 hover:text-[#5586DB] rounded-lg hover:bg-blue-50 transition-colors">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                            </button>
                            <form action="{{ route('applicant.profile.work-experience.destroy', $exp) }}" method="POST"
                                  onsubmit="return confirm('Hapus pengalaman kerja ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <form x-show="editing" action="{{ route('applicant.profile.work-experience.update', $exp) }}" method="POST"
                          class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Nama Perusahaan *</label>
                            <input type="text" name="company_name" value="{{ $exp->company_name }}" required
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Jabatan *</label>
                            <input type="text" name="position" value="{{ $exp->position }}" required
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Mulai *</label>
                            <input type="date" name="start_date" value="{{ $exp->start_date->format('Y-m-d') }}" required
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Selesai</label>
                            <input type="date" name="end_date" value="{{ $exp->end_date?->format('Y-m-d') }}"
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-600 mb-1">Alasan Keluar</label>
                            <textarea name="reason_for_leaving" rows="2"
                                      class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">{{ $exp->reason_for_leaving }}</textarea>
                        </div>
                        <div class="sm:col-span-2 flex items-center gap-2 justify-end">
                            <button type="button" @click="editing = false" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-[#5586DB] hover:bg-[#4470c4] rounded-md transition-colors">Simpan</button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="px-6 py-8 text-center">
                    
                    <p class="text-sm text-gray-400 mt-2">Belum ada pengalaman kerja.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- SERTIFIKASI --}}
    <div id="sertifikasi" x-data="{ showForm: false }" class="bg-white rounded-lg shadow-sm ring-1 ring-gray-900/5 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <h2 class="font-bold text-gray-800 font-jakarta">Sertifikasi &amp; Kursus</h2>
            </div>
            <button @click="showForm = !showForm" class="text-xs font-semibold text-[#5586DB] hover:text-[#4470c4] flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]" x-text="showForm ? 'close' : 'add'">add</span>
                <span x-text="showForm ? 'Tutup' : 'Tambah'">Tambah</span>
            </button>
        </div>

        <div x-show="showForm" x-collapse class="border-b border-gray-100 bg-[#F8FAFC]">
            <form action="{{ route('applicant.profile.course.store') }}" method="POST" class="px-6 py-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Sertifikasi *</label>
                    <input type="text" name="course_name" value="{{ old('course_name') }}" required
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Penerbit *</label>
                    <input type="text" name="issuer_name" value="{{ old('issuer_name') }}" required
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Terbit</label>
                    <input type="date" name="issue_date" value="{{ old('issue_date') }}"
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Kadaluarsa <span class="font-normal text-gray-400">(kosong jika tidak)</span></label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}"
                           class="w-full px-3.5 py-2 bg-white border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                </div>
                <div class="sm:col-span-2 flex justify-end">
                    <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-[#5586DB] hover:bg-[#4470c4] rounded-md transition-colors">
                        Simpan Sertifikasi
                    </button>
                </div>
            </form>
        </div>

        <div class="divide-y divide-gray-50">
            @forelse($applicant->courses->sortByDesc('issue_date') as $course)
                <div x-data="{ editing: false }" class="px-6 py-4">
                    <div x-show="!editing" class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-800">{{ $course->course_name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $course->issuer_name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                @if($course->issue_date) Terbit {{ $course->issue_date->format('M Y') }} @endif
                                @if($course->expiry_date) · Kadaluarsa {{ $course->expiry_date->format('M Y') }}
                                @elseif($course->issue_date) · Tidak kadaluarsa
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button @click="editing = true" class="p-1.5 text-gray-400 hover:text-[#5586DB] rounded-lg hover:bg-blue-50 transition-colors">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                            </button>
                            <form action="{{ route('applicant.profile.course.destroy', $course) }}" method="POST"
                                  onsubmit="return confirm('Hapus sertifikasi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <form x-show="editing" action="{{ route('applicant.profile.course.update', $course) }}" method="POST"
                          class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Nama Sertifikasi *</label>
                            <input type="text" name="course_name" value="{{ $course->course_name }}" required
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Penerbit *</label>
                            <input type="text" name="issuer_name" value="{{ $course->issuer_name }}" required
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Tanggal Terbit</label>
                            <input type="date" name="issue_date" value="{{ $course->issue_date?->format('Y-m-d') }}"
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Kadaluarsa</label>
                            <input type="date" name="expiry_date" value="{{ $course->expiry_date?->format('Y-m-d') }}"
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-gray-200 rounded-md text-sm focus:outline-none focus:ring-2 focus:border-[#5586DB] focus:ring-[#5586DB]/20">
                        </div>
                        <div class="sm:col-span-2 flex items-center gap-2 justify-end">
                            <button type="button" @click="editing = false" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2 text-sm font-semibold text-white bg-[#5586DB] hover:bg-[#4470c4] rounded-md transition-colors">Simpan</button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="px-6 py-8 text-center">
                    
                    <p class="text-sm text-gray-400 mt-2">Belum ada sertifikasi atau kursus.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
