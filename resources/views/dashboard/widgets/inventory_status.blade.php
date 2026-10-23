@props(['data'])

@php
    $conditionLabels = ['GOOD' => 'Baik', 'NEEDS_REPAIR' => 'Perlu Perbaikan', 'DAMAGED' => 'Rusak', 'UNUSABLE' => 'Tidak Pakai'];
    $typeLabels = ['FIXED_ASSET' => 'Aset Tetap', 'SUPPLIES' => 'Perlengkapan', 'OTHER' => 'Lainnya'];
@endphp

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: {{ ($data['needs_repair'] ?? 0) > 0 ? 'var(--dash-status-warning-bg)' : 'var(--dash-status-info-bg)' }}; color: {{ ($data['needs_repair'] ?? 0) > 0 ? 'var(--dash-status-warning-text)' : 'var(--dash-status-info-text)' }};">
                <span class="material-symbols-outlined text-xl">inventory_2</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Inventory Status</h3>
                <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if(($data['needs_repair'] ?? 0) > 0 || ($data['unusable'] ?? 0) > 0)
            <span class="px-2.5 py-1 text-xs font-bold rounded-full" style="background-color: var(--dash-status-warning-bg); color: var(--dash-status-warning-text);">
                {{ ($data['needs_repair'] ?? 0) + ($data['unusable'] ?? 0) }} perlu atensi
            </span>
        @endif
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div>
            <p class="text-xs mb-1 font-medium" style="color: var(--dash-text-muted);">Item Aktif</p>
            <p class="text-2xl sm:text-3xl font-bold font-jakarta tracking-tight" style="color: var(--dash-text-heading);">{{ $data['active_items'] ?? 0 }}</p>
        </div>

        <div class="flex items-center justify-between pt-2 border-t" style="border-color: var(--dash-card-border);">
            <span class="text-xs font-medium" style="color: var(--dash-text-muted);">Nilai Total</span>
            <span class="text-sm font-semibold" style="color: var(--dash-text-heading);">Rp {{ number_format($data['total_value'] ?? 0, 0, ',', '.') }}</span>
        </div>

        @if(!empty($data['by_condition']))
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">Kondisi</p>
                <div class="space-y-1">
                    @foreach($data['by_condition'] as $condition => $count)
                        <div class="flex items-center justify-between text-xs">
                            <span style="color: var(--dash-text-body);">{{ $conditionLabels[$condition] ?? ucfirst(str_replace('_', ' ', $condition)) }}</span>
                            <span class="font-semibold" style="color: {{ in_array($condition, ['DAMAGED', 'UNUSABLE']) ? 'var(--dash-status-danger-text)' : 'var(--dash-text-heading)' }};">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(!empty($data['by_type']))
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">Jenis</p>
                <div class="space-y-1">
                    @foreach($data['by_type'] as $type => $count)
                        <div class="flex items-center justify-between text-xs">
                            <span style="color: var(--dash-text-body);">{{ $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}</span>
                            <span class="font-semibold" style="color: var(--dash-text-heading);">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
