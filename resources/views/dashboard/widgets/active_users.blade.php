@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">
            <span class="material-symbols-outlined text-xl">online_prediction</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Active Users</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">Online sekarang</p>
        </div>
    </div>
    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div class="grid grid-cols-2 gap-3">
            <div class="text-center p-3 rounded-lg" style="background-color: var(--dash-status-success-bg); border: 1px solid var(--dash-card-border);">
                <div class="flex items-center justify-center gap-1.5 mb-1">
                    <span class="h-2 w-2 rounded-full animate-pulse" style="background-color: var(--dash-status-success-text);"></span>
                    <p class="text-2xl font-bold font-jakarta" style="color: var(--dash-status-success-text);">{{ $data['online_now'] ?? 0 }}</p>
                </div>
                <p class="text-xs" style="color: var(--dash-text-muted);">Online (15 menit)</p>
            </div>
            <div class="text-center p-3 rounded-lg" style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-2xl font-bold font-jakarta mb-1" style="color: var(--dash-primary-text);">{{ $data['active_today'] ?? 0 }}</p>
                <p class="text-xs" style="color: var(--dash-text-muted);">Aktif Hari Ini</p>
            </div>
        </div>
        @if(!empty($data['recent_active']))
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">Terakhir Login</p>
                <div class="space-y-1.5">
                    @foreach(array_slice($data['recent_active'], 0, 5) as $user)
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full" style="background-color: {{ isset($user['is_online']) && $user['is_online'] ? 'var(--dash-status-success-text)' : 'var(--dash-text-faint)' }};"></span>
                            <span class="text-xs flex-1 truncate" style="color: var(--dash-text-body);">{{ $user['name'] }}</span>
                            <span class="text-xs" style="color: var(--dash-text-muted);">{{ \Carbon\Carbon::parse($user['last_login_at'])->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
