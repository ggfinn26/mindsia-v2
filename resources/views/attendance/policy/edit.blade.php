@extends('layouts.dashboard')

@section('title', 'Edit Policy Absensi')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="{ scope: '{{ old('attendance_scope', $policy->attendance_scope) }}', isExempt: {{ old('is_attendance_exempt', $policy->is_attendance_exempt) ? 'true' : 'false' }} }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Policy: {{ $policy->policy_name }}</h1>
        <a href="{{ route('attendance-policies.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium border border-gray-300">Batal</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('attendance-policies.update', $policy->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white shadow sm:rounded-lg divide-y divide-gray-200">
            <!-- Info Dasar -->
            <div class="px-6 py-5">
                <h3 class="text-base font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Nama Policy <span class="text-red-500">*</span></label>
                        <input type="text" name="policy_name" value="{{ old('policy_name', $policy->policy_name) }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cakupan Absensi <span class="text-red-500">*</span></label>
                        <select name="attendance_scope" x-model="scope" required class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                            <option value="branch">Cabang</option>
                            <option value="area">Area</option>
                            <option value="region">Region</option>
                        </select>
                    </div>

                    <div x-show="scope === 'branch'">
                        <label class="block text-sm font-medium text-gray-700">Cabang</label>
                        <select name="branch_id" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                            <option value="">Pilih Cabang...</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $policy->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="scope === 'area'">
                        <label class="block text-sm font-medium text-gray-700">Area</label>
                        <select name="area_id" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                            <option value="">Pilih Area...</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ old('area_id', $policy->area_id) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="scope === 'region'">
                        <label class="block text-sm font-medium text-gray-700">Region</label>
                        <select name="region_id" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                            <option value="">Pilih Region...</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" {{ old('region_id', $policy->region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_attendance_exempt" value="1" @change="isExempt = $event.target.checked" {{ old('is_attendance_exempt', $policy->is_attendance_exempt) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Bebas absensi (exempt)</span>
                        </label>
                    </div>
                    <div class="md:col-span-2" x-show="isExempt">
                        <label class="block text-sm font-medium text-gray-700">Alasan Pembebasan</label>
                        <input type="text" name="exemption_reason" value="{{ old('exemption_reason', $policy->exemption_reason) }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="is_active" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                            <option value="1" {{ old('is_active', $policy->is_active) ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !old('is_active', $policy->is_active) ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Work Schedule Config -->
            <div class="px-6 py-5">
                <h3 class="text-base font-medium text-gray-900 mb-4">Konfigurasi Absensi Kerja Harian</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="work_is_required" value="1" {{ old('work_is_required', $policy->workScheduleConfig?->is_required ?? true) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Absensi kerja harian wajib</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Toleransi Terlambat (menit)</label>
                        <input type="number" name="work_late_tolerance_minutes" value="{{ old('work_late_tolerance_minutes', $policy->workScheduleConfig?->late_tolerance_minutes ?? 0) }}" min="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Toleransi Pulang Cepat (menit)</label>
                        <input type="number" name="work_early_leave_tolerance_minutes" value="{{ old('work_early_leave_tolerance_minutes', $policy->workScheduleConfig?->early_leave_tolerance_minutes ?? 0) }}" min="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Session Config -->
            <div class="px-6 py-5">
                <h3 class="text-base font-medium text-gray-900 mb-4">Konfigurasi Absensi Sesi/Kelas</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="session_is_required" value="1" {{ old('session_is_required', $policy->sessionConfig?->is_required ?? true) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Absensi sesi wajib</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Toleransi Terlambat Sesi (menit)</label>
                        <input type="number" name="session_late_tolerance_minutes" value="{{ old('session_late_tolerance_minutes', $policy->sessionConfig?->late_tolerance_minutes ?? 0) }}" min="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">Simpan Perubahan</button>
            </div>
        </div>
    </form>
</div>
@endsection
