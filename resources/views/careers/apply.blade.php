@extends('layouts.portal')
@section('title', 'Lamar — ' . $req->position)
@section('content')

<div class="mb-6">
    <a href="{{ route('careers.show', $req) }}" class="text-sm text-gray-500 hover:underline">← {{ $req->position }}</a>
    <h2 class="text-xl font-bold text-gray-800 mt-1">Form Lamaran</h2>
    <p class="text-sm text-gray-500">{{ $req->branch?->name ?? 'MINDSIA' }} · {{ $req->position }}</p>
</div>

<form method="POST" action="{{ route('careers.submit', $req) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- A. Data Pribadi --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
        <h3 class="font-semibold text-gray-700 border-b pb-2">A. Data Pribadi</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm uppercase">
                @error('full_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                <select name="gender" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Pilih --</option>
                    <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="birthdate" value="{{ old('birthdate') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Perkawinan</label>
                <select name="marital_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Pilih --</option>
                    @foreach(['Belum Menikah', 'Menikah', 'Cerai'] as $ms)
                    <option value="{{ $ms }}" {{ old('marital_status') === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                <select name="religion" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Pilih --</option>
                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $rel)
                    <option value="{{ $rel }}" {{ old('religion') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="08xxxxxxxxxx">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('address') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                <input type="text" name="city" value="{{ old('city') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                <input type="text" name="postal_code" value="{{ old('postal_code') }}" maxlength="10"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
        </div>
    </div>

    {{-- C. Pendidikan --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-3">
        <div class="flex items-center justify-between border-b pb-2">
            <h3 class="font-semibold text-gray-700">C. Pendidikan Formal</h3>
            <button type="button" onclick="addEduRow()" class="text-xs text-blue-600 hover:underline">+ Tambah</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-xs text-gray-400 uppercase">
                    <th class="text-left pr-2 py-1 w-28">Jenjang</th>
                    <th class="text-left pr-2 py-1">Nama Sekolah / Universitas</th>
                    <th class="text-left pr-2 py-1 w-20">Mulai</th>
                    <th class="text-left pr-2 py-1 w-20">Akhir</th>
                    <th class="w-6"></th>
                </tr></thead>
                <tbody id="edu-rows">
                    <tr class="edu-row">
                        <td class="pr-2 pb-1"><select name="education[0][level]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs"><option value="">--</option>@foreach(['SD','SMP','SMA/SMK','D3','S1','S2','S3','Training'] as $lv)<option value="{{ $lv }}">{{ $lv }}</option>@endforeach</select></td>
                        <td class="pr-2 pb-1"><input type="text" name="education[0][institution]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" placeholder="Nama sekolah"></td>
                        <td class="pr-2 pb-1"><input type="text" name="education[0][major]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" placeholder="YYYY"></td>
                        <td class="pr-2 pb-1"><input type="text" name="education[0][graduation_year]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" placeholder="YYYY"></td>
                        <td class="pb-1"><button type="button" onclick="this.closest('tr').remove()" class="text-red-400 hover:text-red-600 text-xs">✕</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- D. Riwayat Pekerjaan --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-3">
        <div class="flex items-center justify-between border-b pb-2">
            <h3 class="font-semibold text-gray-700">D. Riwayat Pekerjaan <span class="text-gray-400 text-xs font-normal">(kosongkan jika belum pernah bekerja)</span></h3>
            <button type="button" onclick="addWorkRow()" class="text-xs text-blue-600 hover:underline">+ Tambah</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-xs text-gray-400 uppercase">
                    <th class="text-left pr-2 py-1">Perusahaan</th>
                    <th class="text-left pr-2 py-1 w-36">Posisi</th>
                    <th class="text-left pr-2 py-1 w-16">Mulai</th>
                    <th class="text-left pr-2 py-1 w-16">Akhir</th>
                    <th class="w-6"></th>
                </tr></thead>
                <tbody id="work-rows">
                    <tr class="work-row">
                        <td class="pr-2 pb-1"><input type="text" name="work_history[0][company_name]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" placeholder="Nama perusahaan"></td>
                        <td class="pr-2 pb-1"><input type="text" name="work_history[0][position]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" placeholder="Jabatan"></td>
                        <td class="pr-2 pb-1"><input type="text" name="work_history[0][start_year]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" placeholder="YYYY"></td>
                        <td class="pr-2 pb-1"><input type="text" name="work_history[0][end_year]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" placeholder="YYYY"></td>
                        <td class="pb-1"><button type="button" onclick="this.closest('tr').remove()" class="text-red-400 text-xs">✕</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- CV --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-700 mb-3 border-b pb-2">Upload CV</h3>
        <input type="file" name="cv_file" accept="application/pdf"
               class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700">
        <p class="text-xs text-gray-400 mt-1">Format PDF, maksimal 5MB</p>
        @error('cv_file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    @if($errors->any())
    <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="flex gap-3">
        <a href="{{ route('careers.show', $req) }}"
           class="px-5 py-2.5 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50">Batal</a>
        <button type="submit"
                class="px-8 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 text-sm">
            Kirim Lamaran
        </button>
    </div>
</form>

<script>
let eduIdx = 1, workIdx = 1;
function addEduRow() {
    const levels = ['SD','SMP','SMA/SMK','D3','S1','S2','S3','Training'];
    const opts = levels.map(l => `<option value="${l}">${l}</option>`).join('');
    document.getElementById('edu-rows').insertAdjacentHTML('beforeend', `<tr class="edu-row">
        <td class="pr-2 pb-1"><select name="education[${eduIdx}][level]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs"><option value="">--</option>${opts}</select></td>
        <td class="pr-2 pb-1"><input type="text" name="education[${eduIdx}][institution]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs"></td>
        <td class="pr-2 pb-1"><input type="text" name="education[${eduIdx}][major]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" placeholder="YYYY"></td>
        <td class="pr-2 pb-1"><input type="text" name="education[${eduIdx}][graduation_year]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" placeholder="YYYY"></td>
        <td class="pb-1"><button type="button" onclick="this.closest('tr').remove()" class="text-red-400 text-xs">✕</button></td>
    </tr>`); eduIdx++;
}
function addWorkRow() {
    document.getElementById('work-rows').insertAdjacentHTML('beforeend', `<tr class="work-row">
        <td class="pr-2 pb-1"><input type="text" name="work_history[${workIdx}][company_name]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" placeholder="Nama perusahaan"></td>
        <td class="pr-2 pb-1"><input type="text" name="work_history[${workIdx}][position]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs"></td>
        <td class="pr-2 pb-1"><input type="text" name="work_history[${workIdx}][start_year]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" placeholder="YYYY"></td>
        <td class="pr-2 pb-1"><input type="text" name="work_history[${workIdx}][end_year]" class="w-full border border-gray-300 rounded px-2 py-1 text-xs" placeholder="YYYY"></td>
        <td class="pb-1"><button type="button" onclick="this.closest('tr').remove()" class="text-red-400 text-xs">✕</button></td>
    </tr>`); workIdx++;
}
</script>
@endsection
