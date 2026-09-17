@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-violet-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-violet-600 text-xl">analytics</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Hiring Metrics</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Overall Conversion</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['overall_conversion'] ?? 0 }}%</p>
        </div>

        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Funnel</p>
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-600">Applied</span>
                    <span class="text-xs font-semibold text-gray-700">{{ $data['applied'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-600">Screened</span>
                    <span class="text-xs font-semibold text-gray-700">{{ $data['screened'] ?? 0 }} <span class="text-gray-400">({{ $data['screening_rate'] ?? 0 }}%)</span></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-600">Interviewed</span>
                    <span class="text-xs font-semibold text-gray-700">{{ $data['interviewed'] ?? 0 }} <span class="text-gray-400">({{ $data['interview_rate'] ?? 0 }}%)</span></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-600">Offered</span>
                    <span class="text-xs font-semibold text-gray-700">{{ $data['offered'] ?? 0 }} <span class="text-gray-400">({{ $data['offer_rate'] ?? 0 }}%)</span></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-600">Hired</span>
                    <span class="text-xs font-semibold text-emerald-600">{{ $data['hired'] ?? 0 }} <span class="text-gray-400">({{ $data['hire_rate'] ?? 0 }}%)</span></span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-gray-400">Target Hiring</span>
            <span class="text-sm font-semibold {{ ($data['fulfillment_rate'] ?? 0) >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $data['fulfillment_rate'] ?? 0 }}%</span>
        </div>
    </div>
</div>
