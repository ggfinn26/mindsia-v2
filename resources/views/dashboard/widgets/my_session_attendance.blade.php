@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-emerald-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-emerald-600 text-xl">fact_check</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">My Session Attendance</h3>
            <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-3 gap-2">
            <div class="text-center p-2 rounded-lg bg-emerald-50">
                <p class="text-xl font-bold text-emerald-600">{{ $data['hadir'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Hadir</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-yellow-50">
                <p class="text-xl font-bold text-yellow-600">{{ $data['izin'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Izin</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-red-50">
                <p class="text-xl font-bold text-red-500">{{ $data['tidak_hadir'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Absen</p>
            </div>
        </div>
        <div class="pt-2 border-t border-gray-50 flex justify-between items-center">
            <span class="text-xs text-gray-400">Attendance Rate</span>
            <span class="text-sm font-bold {{ ($data['attendance_rate'] ?? 0) >= 80 ? 'text-emerald-600' : 'text-red-500' }}">{{ $data['attendance_rate'] ?? 0 }}%</span>
        </div>
    </div>
</div>
