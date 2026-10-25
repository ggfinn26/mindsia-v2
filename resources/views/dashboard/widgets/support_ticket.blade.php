@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">support_agent</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Support Tickets</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">Assigned to me</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-3 gap-2">
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-status-warning-text);">{{ $data['open'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Open</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-primary-text);">{{ $data['in_progress'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">In Progress</p>
            </div>
            <div class="text-center p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xl font-bold font-jakarta" style="color: var(--dash-status-success-text);">{{ $data['resolved'] ?? 0 }}</p>
                <p class="text-xs mt-0.5" style="color: var(--dash-text-muted);">Resolved</p>
            </div>
        </div>
        <div class="space-y-1.5 flex-grow">
            @forelse($data['recent'] ?? [] as $ticket)
                @php
                    $status = $ticket['status'] ?? '-';
                    $badgeBg = $status === 'open' ? 'var(--dash-status-warning-bg)' : ($status === 'in_progress' ? 'var(--dash-primary-bg)' : 'var(--dash-status-success-bg)');
                    $badgeText = $status === 'open' ? 'var(--dash-status-warning-text)' : ($status === 'in_progress' ? 'var(--dash-primary-text)' : 'var(--dash-status-success-text)');
                @endphp
                <div class="flex items-center justify-between p-2 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold truncate" style="color: var(--dash-text-heading);">{{ $ticket['subject'] ?? '-' }}</p>
                        <p class="text-[11px]" style="color: var(--dash-text-muted);">{{ $ticket['member'] ?? '-' }}</p>
                    </div>
                    <span class="text-xs ml-2 px-2 py-0.5 rounded-full capitalize font-semibold shrink-0" style="background-color: {{ $badgeBg }}; color: {{ $badgeText }};">
                        {{ \App\Support\EnumLabels::get('support_ticket_status', $status) }}
                    </span>
                </div>
            @empty
                <div class="py-4 text-center">
                    <p class="text-xs" style="color: var(--dash-text-muted);">Tidak ada tiket</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
