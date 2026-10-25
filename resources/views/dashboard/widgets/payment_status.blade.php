@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">payments</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Payment Status</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="space-y-1.5">
            @foreach($data['by_status'] ?? [] as $status => $info)
                <div class="flex items-center justify-between text-xs">
                    <span style="color: var(--dash-text-body);">{{ \App\Support\EnumLabels::get('payment_status', $status) }}</span>
                    <div class="text-right">
                        <span class="font-bold" style="color: var(--dash-text-heading);">{{ $info['count'] }}</span>
                        <span class="ml-1" style="color: var(--dash-text-muted);">/ Rp {{ number_format($info['total'], 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="pt-2 border-t grid grid-cols-2 gap-2" style="border-color: var(--dash-card-border);">
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-lg font-bold font-jakarta" style="color: var(--dash-status-warning-text);">{{ $data['due_soon_count'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Due 7 Hari</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-lg font-bold font-jakarta" style="color: var(--dash-status-danger-text);">{{ $data['overdue_count'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Overdue</p>
            </div>
        </div>
    </div>
</div>
