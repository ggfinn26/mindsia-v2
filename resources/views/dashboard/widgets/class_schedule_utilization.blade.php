@props(['data'])

@php
    $utilization = $data['utilization_percent'] ?? 0;
    $barColor = $utilization >= 80 ? 'bg-emerald-500' : ($utilization >= 50 ? 'bg-blue-500' : 'bg-amber-500');
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-indigo-600 text-xl">school</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Class Schedule & Utilization</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Kelas Aktif</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['active_classes'] ?? 0 }}</p>
        </div>

        <div>
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-gray-400">Utilisasi Kuota</span>
                <span class="text-xs font-semibold text-gray-600">{{ $utilization }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="{{ $barColor }} h-2 rounded-full transition-all" style="width: {{ min($utilization, 100) }}%"></div>
            </div>
            <div class="flex items-center justify-between mt-1">
                <span class="text-xs text-gray-400">{{ $data['active_members'] ?? 0 }} siswa</span>
                <span class="text-xs text-gray-400">dari {{ $data['total_quota'] ?? 0 }} kuota</span>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-50">
            <div class="text-center">
                <p class="text-xs text-gray-400">Planned</p>
                <p class="text-sm font-semibold text-gray-600">{{ $data['planned_classes'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400">Aktif</p>
                <p class="text-sm font-semibold text-emerald-600">{{ $data['active_classes'] ?? 0 }}</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-400">Selesai</p>
                <p class="text-sm font-semibold text-blue-600">{{ $data['completed_classes'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
