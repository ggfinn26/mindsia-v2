@props(['data'])

@php
    $rate = $data['attendance_rate'] ?? 0;
    $barColor = $rate >= 95 ? 'bg-emerald-500' : ($rate >= 80 ? 'bg-amber-500' : 'bg-red-500');
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-teal-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-teal-600 text-xl">schedule</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Attendance Summary</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(isset($data['trend_percent']))
            <div class="flex items-center gap-1 {{ $data['trend_percent'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                <span class="material-symbols-outlined text-sm">{{ $data['trend_percent'] >= 0 ? 'trending_up' : 'trending_down' }}</span>
                <span class="text-xs font-semibold">{{ abs($data['trend_percent']) }}%</span>
            </div>
        @endif
    </div>

    <div class="space-y-3">
        <div>
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-400">Kehadiran</span>
                <span class="text-xs font-semibold {{ $rate >= 95 ? 'text-emerald-600' : ($rate >= 80 ? 'text-amber-600' : 'text-red-600') }}">{{ $rate }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ min($rate, 100) }}%"></div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-50">
            <div class="text-center">
                <p class="text-xs text-gray-400">Hadir</p>
                <p class="text-sm font-semibold text-emerald-600">{{ $data['total_present'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400">Absen</p>
                <p class="text-sm font-semibold text-red-500">{{ $data['total_absent'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400">Terlambat</p>
                <p class="text-sm font-semibold text-amber-600">{{ $data['total_late'] ?? 0 }}</p>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-50">
            <div class="text-center">
                <p class="text-xs text-gray-400">Sakit</p>
                <p class="text-sm font-semibold text-gray-600">{{ $data['total_sick'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400">Izin</p>
                <p class="text-sm font-semibold text-gray-600">{{ $data['total_permission'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400">Cuti</p>
                <p class="text-sm font-semibold text-gray-600">{{ $data['total_leave'] ?? 0 }}</p>
            </div>
        </div>

        @if(isset($data['anomaly_count']) && $data['anomaly_count'] > 0)
        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-gray-400">Anomali Lokasi</span>
            <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">{{ $data['anomaly_count'] }}</span>
        </div>
        @endif
    </div>
</div>
