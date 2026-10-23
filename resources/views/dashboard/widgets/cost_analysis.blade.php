@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-status-danger-bg); color: var(--dash-status-danger-text);">
            <span class="material-symbols-outlined text-xl">pie_chart</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Cost Analysis</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div>
            <p class="text-xs mb-1 font-medium" style="color: var(--dash-text-muted);">Total Cost</p>
            <p class="text-2xl sm:text-3xl font-bold font-jakarta tracking-tight" style="color: var(--dash-text-heading);">Rp {{ number_format($data['total_cost'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="space-y-1.5 pt-2 border-t" style="border-color: var(--dash-card-border);">
            @foreach($data['breakdown'] ?? [] as $category => $amount)
                <div class="flex items-center justify-between text-xs">
                    <span class="truncate max-w-[55%]" style="color: var(--dash-text-body);">{{ $category }}</span>
                    <span class="font-bold" style="color: var(--dash-text-heading);">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
