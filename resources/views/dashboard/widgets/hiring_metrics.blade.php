@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-xl">analytics</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Hiring Metrics</h3>
                <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div>
            <p class="text-xs mb-1 font-medium" style="color: var(--dash-text-muted);">Overall Conversion</p>
            <p class="text-2xl sm:text-3xl font-bold font-jakarta tracking-tight" style="color: var(--dash-text-heading);">{{ $data['overall_conversion'] ?? 0 }}%</p>
        </div>

        <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
            <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">Funnel</p>
            <div class="space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span style="color: var(--dash-text-body);">Applied</span>
                    <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $data['applied'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span style="color: var(--dash-text-body);">Screened</span>
                    <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $data['screened'] ?? 0 }} <span style="color: var(--dash-text-muted);">({{ $data['screening_rate'] ?? 0 }}%)</span></span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span style="color: var(--dash-text-body);">Interviewed</span>
                    <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $data['interviewed'] ?? 0 }} <span style="color: var(--dash-text-muted);">({{ $data['interview_rate'] ?? 0 }}%)</span></span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span style="color: var(--dash-text-body);">Offered</span>
                    <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $data['offered'] ?? 0 }} <span style="color: var(--dash-text-muted);">({{ $data['offer_rate'] ?? 0 }}%)</span></span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span style="color: var(--dash-text-body);">Hired</span>
                    <span class="font-semibold" style="color: var(--dash-status-success-text);">{{ $data['hired'] ?? 0 }} <span style="color: var(--dash-text-muted);">({{ $data['hire_rate'] ?? 0 }}%)</span></span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t" style="border-color: var(--dash-card-border);">
            <span class="text-xs font-medium" style="color: var(--dash-text-muted);">Target Hiring</span>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background-color: {{ ($data['fulfillment_rate'] ?? 0) >= 80 ? 'var(--dash-status-success-bg)' : 'var(--dash-status-warning-bg)' }}; color: {{ ($data['fulfillment_rate'] ?? 0) >= 80 ? 'var(--dash-status-success-text)' : 'var(--dash-status-warning-text)' }};">{{ $data['fulfillment_rate'] ?? 0 }}%</span>
        </div>
    </div>
</div>
