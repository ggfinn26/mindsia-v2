@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text);">
            <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Expense Tracking</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div>
            <p class="text-xs mb-1 font-medium" style="color: var(--dash-text-muted);">Total Pengeluaran</p>
            <p class="text-2xl sm:text-3xl font-bold font-jakarta tracking-tight" style="color: var(--dash-text-heading);">Rp {{ number_format($data['total_expense'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="grid grid-cols-2 gap-2 pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="p-2.5 rounded-lg text-center" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Operasional</p>
                <p class="text-sm font-bold mt-0.5" style="color: var(--dash-status-warning-text);">Rp {{ number_format($data['opex'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="p-2.5 rounded-lg text-center" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Reimbursement</p>
                <p class="text-sm font-bold mt-0.5" style="color: var(--dash-primary-text);">Rp {{ number_format($data['reimbursement'] ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        @if(isset($data['pending_reimbursement_count']) && $data['pending_reimbursement_count'] > 0)
            <div class="flex items-center gap-2 p-2 rounded-lg" style="background-color: var(--dash-status-warning-bg); border: 1px solid var(--dash-card-border);">
                <span class="material-symbols-outlined text-base" style="color: var(--dash-status-warning-text);">pending</span>
                <span class="text-xs font-semibold" style="color: var(--dash-status-warning-text);">{{ $data['pending_reimbursement_count'] }} reimbursement pending approval</span>
            </div>
        @endif
    </div>
</div>
