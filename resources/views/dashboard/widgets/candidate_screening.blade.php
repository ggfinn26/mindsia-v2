@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-violet-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-violet-600 text-xl">filter_list</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Candidate Screening</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Dalam Screening</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['in_screening'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Total Discreening</p>
                <p class="text-sm font-semibold text-gray-700">{{ $data['total_screened'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Pass Rate</p>
                <p class="text-sm font-semibold {{ ($data['pass_rate'] ?? 0) >= 50 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $data['pass_rate'] ?? 0 }}%</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Psikotest Selesai</p>
                <p class="text-sm font-semibold text-gray-700">{{ $data['psikotest_completed'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Rata-rata Skor</p>
                <p class="text-sm font-semibold text-violet-600">{{ $data['avg_psikotest_score'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
