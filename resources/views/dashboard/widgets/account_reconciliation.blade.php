@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-status-info-bg); color: var(--dash-status-info-text);">
            <span class="material-symbols-outlined text-xl">account_balance</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Account Reconciliation</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-2 gap-3">
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Total Inflow</p>
                <p class="text-base sm:text-lg font-bold font-jakarta" style="color: var(--dash-status-success-text);">Rp {{ number_format($data['total_inflow'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium mb-1" style="color: var(--dash-text-muted);">Total Outflow</p>
                <p class="text-base sm:text-lg font-bold font-jakarta" style="color: var(--dash-status-danger-text);">Rp {{ number_format($data['total_outflow'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="pt-2 border-t flex items-center justify-between" style="border-color: var(--dash-card-border);">
            <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Net Balance</p>
            <p class="text-sm font-bold" style="color: {{ ($data['net_balance'] ?? 0) >= 0 ? 'var(--dash-status-success-text)' : 'var(--dash-status-danger-text)' }};">
                {{ ($data['net_balance'] ?? 0) >= 0 ? '+' : '' }}Rp {{ number_format($data['net_balance'] ?? 0, 0, ',', '.') }}
            </p>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-sm font-bold" style="color: var(--dash-status-warning-text);">{{ $data['unverified_payments'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Unverified</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-sm font-bold" style="color: var(--dash-text-heading);">{{ $data['pending_payroll'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Payroll Pending</p>
            </div>
        </div>
    </div>
</div>
