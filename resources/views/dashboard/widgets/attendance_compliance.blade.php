@props(['data'])

@php
    $rate = $data['compliance_rate'] ?? 100;
    $barColor = $rate >= 90 ? 'bg-emerald-500' : ($rate >= 70 ? 'bg-amber-500' : 'bg-red-500');
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg {{ $rate < 90 ? 'bg-amber-50' : 'bg-green-50' }} flex items-center justify-center">
                <span class="material-symbols-outlined {{ $rate < 90 ? 'text-amber-600' : 'text-green-600' }} text-xl">rule</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Attendance Compliance</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-400">Compliance Rate</span>
                <span class="text-xs font-semibold {{ $rate >= 90 ? 'text-emerald-600' : ($rate >= 70 ? 'text-amber-600' : 'text-red-600') }}">{{ $rate }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ min($rate, 100) }}%"></div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Pelanggaran</p>
                <p class="text-sm font-semibold text-red-500">{{ $data['total_violations'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Karyawan Terkena</p>
                <p class="text-sm font-semibold text-amber-600">{{ $data['employees_with_violations'] ?? 0 }}</p>
            </div>
        </div>

        @if(($data['active_warning_letters'] ?? 0) > 0)
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Surat Peringatan Aktif</p>
            <div class="flex gap-3">
                @if(($data['sp1_count'] ?? 0) > 0)
                    <div class="text-center">
                        <span class="px-2 py-1 text-xs font-semibold rounded bg-amber-100 text-amber-700">SP1</span>
                        <p class="text-sm font-bold text-amber-700 mt-1">{{ $data['sp1_count'] }}</p>
                    </div>
                @endif
                @if(($data['sp2_count'] ?? 0) > 0)
                    <div class="text-center">
                        <span class="px-2 py-1 text-xs font-semibold rounded bg-orange-100 text-orange-700">SP2</span>
                        <p class="text-sm font-bold text-orange-700 mt-1">{{ $data['sp2_count'] }}</p>
                    </div>
                @endif
                @if(($data['sp3_count'] ?? 0) > 0)
                    <div class="text-center">
                        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">SP3</span>
                        <p class="text-sm font-bold text-red-700 mt-1">{{ $data['sp3_count'] }}</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
