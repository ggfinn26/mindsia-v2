@extends('layouts.dashboard')

@section('title', 'Tambah Aturan Sanksi Absen')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8"
    x-data="{
        actions: [{ action_type: '', action_order: 1 }],
        addAction() { this.actions.push({ action_type: '', action_order: this.actions.length + 1 }); },
        removeAction(i) { this.actions.splice(i, 1); this.actions.forEach((a, idx) => a.action_order = idx + 1); }
    }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Aturan Sanksi</h1>
        <a href="{{ route('attendance-rules.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium border border-gray-300">Batal</a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('attendance-rules.store') }}" method="POST">
        @csrf
        <div class="bg-white shadow sm:rounded-lg divide-y divide-gray-200">
            <!-- Info Aturan -->
            <div class="px-6 py-5">
                <h3 class="text-base font-medium text-gray-900 mb-4">Definisi Aturan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Nama Aturan <span class="text-red-500">*</span></label>
                        <input type="text" name="rule_name" value="{{ old('rule_name') }}" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipe Absensi <span class="text-red-500">*</span></label>
                        <select name="attendance_type" required class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                            <option value="work_schedule" {{ old('attendance_type') === 'work_schedule' ? 'selected' : '' }}>Kerja Harian</option>
                            <option value="session" {{ old('attendance_type') === 'session' ? 'selected' : '' }}>Sesi/Kelas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Periode <span class="text-red-500">*</span></label>
                        <select name="period_type" required class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                            <option value="daily" {{ old('period_type') === 'daily' ? 'selected' : '' }}>Harian</option>
                            <option value="monthly" {{ old('period_type') === 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Pemicu <span class="text-red-500">*</span></label>
                        <select name="trigger_type" required class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                            <option value="consecutive_absence" {{ old('trigger_type') === 'consecutive_absence' ? 'selected' : '' }}>Absen Beruntun</option>
                            <option value="monthly_absence" {{ old('trigger_type') === 'monthly_absence' ? 'selected' : '' }}>Absen Bulanan</option>
                            <option value="monthly_late_count" {{ old('trigger_type') === 'monthly_late_count' ? 'selected' : '' }}>Jumlah Keterlambatan</option>
                            <option value="monthly_late_minutes" {{ old('trigger_type') === 'monthly_late_minutes' ? 'selected' : '' }}>Total Menit Terlambat</option>
                            <option value="daily_late" {{ old('trigger_type') === 'daily_late' ? 'selected' : '' }}>Terlambat Harian</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Operator <span class="text-red-500">*</span></label>
                            <select name="trigger_operator" required class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                                @foreach(['=' => '=', '>' => '>', '>=' => '≥', '<' => '<', '<=' => '≤'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('trigger_operator') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nilai <span class="text-red-500">*</span></label>
                            <input type="number" name="trigger_value" value="{{ old('trigger_value', 0) }}" min="0" step="0.01" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="is_active" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                            <option value="1" selected>Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Aksi Sanksi (Dynamic) -->
            <div class="px-6 py-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-medium text-gray-900">Aksi Sanksi</h3>
                    <button type="button" @click="addAction()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">+ Tambah Aksi</button>
                </div>
                <div class="space-y-3">
                    <template x-for="(action, i) in actions" :key="i">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-gray-500 w-6" x-text="i + 1 + '.'"></span>
                            <select :name="`actions[${i}][action_type]`" x-model="action.action_type" required class="flex-1 bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 sm:text-sm">
                                <option value="">Pilih Jenis Aksi...</option>
                                <option value="notification">Notifikasi</option>
                                <option value="warning_letter">Surat Peringatan</option>
                                <option value="payroll_deduction">Potongan Gaji</option>
                                <option value="mark_anomaly">Tandai Anomali</option>
                                <option value="create_follow_up">Buat Tindak Lanjut</option>
                            </select>
                            <input type="hidden" :name="`actions[${i}][action_order]`" :value="i + 1">
                            <button type="button" @click="removeAction(i)" x-show="actions.length > 1" class="text-red-500 hover:text-red-700">
                                <span class="material-symbols-outlined text-xl">delete</span>
                            </button>
                        </div>
                    </template>
                </div>
                @error('actions')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="px-6 py-4 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">Simpan Aturan</button>
            </div>
        </div>
    </form>
</div>
@endsection
