@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-amber-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-600 text-xl">school</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Employee Development</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Rata-rata Skor KPI</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['avg_kpi_score'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Evaluasi KPI</p>
                <p class="text-sm font-semibold text-gray-700">{{ $data['kpi_evaluations'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Sosialisasi</p>
                <p class="text-sm font-semibold text-gray-700">{{ $data['socialization_count'] ?? 0 }}</p>
            </div>
        </div>

        @if(!empty($data['by_grade']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Distribusi Grade</p>
            @foreach($data['by_grade'] as $grade => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-600">{{ $grade }}</span>
                    <span class="text-xs font-semibold text-gray-700">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
