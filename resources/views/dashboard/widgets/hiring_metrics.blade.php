@props(['data'])

<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-violet-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-violet-600 text-xl">analytics</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Hiring Metrics</h3>
                <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Overall Conversion</p>
            <p class="text-2xl font-bold text-[color:var(--dash-text-heading)] font-jakarta">{{ $data['overall_conversion'] ?? 0 }}%</p>
        </div>

        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-2">Funnel</p>
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-[color:var(--dash-text-body)]">Applied</span>
                    <span class="text-xs font-semibold text-[color:var(--dash-text-body)]">{{ $data['applied'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-[color:var(--dash-text-body)]">Screened</span>
                    <span class="text-xs font-semibold text-[color:var(--dash-text-body)]">{{ $data['screened'] ?? 0 }} <span class="text-[color:var(--dash-text-faint)]">({{ $data['screening_rate'] ?? 0 }}%)</span></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-[color:var(--dash-text-body)]">Interviewed</span>
                    <span class="text-xs font-semibold text-[color:var(--dash-text-body)]">{{ $data['interviewed'] ?? 0 }} <span class="text-[color:var(--dash-text-faint)]">({{ $data['interview_rate'] ?? 0 }}%)</span></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-[color:var(--dash-text-body)]">Offered</span>
                    <span class="text-xs font-semibold text-[color:var(--dash-text-body)]">{{ $data['offered'] ?? 0 }} <span class="text-[color:var(--dash-text-faint)]">({{ $data['offer_rate'] ?? 0 }}%)</span></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-[color:var(--dash-text-body)]">Hired</span>
                    <span class="text-xs font-semibold text-emerald-600">{{ $data['hired'] ?? 0 }} <span class="text-[color:var(--dash-text-faint)]">({{ $data['hire_rate'] ?? 0 }}%)</span></span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-[color:var(--dash-text-faint)]">Target Hiring</span>
            <span class="text-sm font-semibold {{ ($data['fulfillment_rate'] ?? 0) >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $data['fulfillment_rate'] ?? 0 }}%</span>
        </div>
    </div>
</div>
