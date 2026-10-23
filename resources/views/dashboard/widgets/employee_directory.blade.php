@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
                <span class="material-symbols-outlined text-xl">badge</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Employee Directory</h3>
                <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div>
            <p class="text-xs mb-1 font-medium" style="color: var(--dash-text-muted);">Karyawan Aktif</p>
            <p class="text-2xl sm:text-3xl font-bold font-jakarta tracking-tight" style="color: var(--dash-text-heading);">{{ $data['total_active'] ?? 0 }}</p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t" style="border-color: var(--dash-card-border);">
            <span class="text-xs font-medium" style="color: var(--dash-text-muted);">Join Bulan Ini</span>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background-color: var(--dash-status-success-bg); color: var(--dash-status-success-text);">+{{ $data['recent_joins'] ?? 0 }}</span>
        </div>

        @if(!empty($data['by_position']))
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">Top Posisi</p>
                <div class="space-y-1">
                    @foreach($data['by_position'] as $position => $count)
                        <div class="flex items-center justify-between text-xs">
                            <span class="truncate max-w-[70%]" style="color: var(--dash-text-body);">{{ $position }}</span>
                            <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(!empty($data['by_employment_type']))
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">Tipe Karyawan</p>
                <div class="space-y-1">
                    @foreach($data['by_employment_type'] as $type => $count)
                        <div class="flex items-center justify-between text-xs">
                            <span style="color: var(--dash-text-body);">{{ strtoupper($type) }}</span>
                            <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
