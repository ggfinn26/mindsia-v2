@props(['data'])

@php
    $rate = $data['compliance_rate'] ?? 100;
    $barBg = $rate >= 90 ? 'var(--dash-status-success-text)' : ($rate >= 70 ? 'var(--dash-status-warning-text)' : 'var(--dash-status-danger-text)');
@endphp

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: {{ $rate < 90 ? 'var(--dash-status-warning-bg)' : 'var(--dash-status-success-bg)' }}; color: {{ $rate < 90 ? 'var(--dash-status-warning-text)' : 'var(--dash-status-success-text)' }};">
                <span class="material-symbols-outlined text-xl">rule</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">Attendance Compliance</h3>
                <p class="text-xs" style="color: var(--dash-text-muted);">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3 flex-grow flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs font-medium" style="color: var(--dash-text-muted);">Compliance Rate</span>
                <span class="text-xs font-bold" style="color: {{ $rate >= 90 ? 'var(--dash-status-success-text)' : ($rate >= 70 ? 'var(--dash-status-warning-text)' : 'var(--dash-status-danger-text)') }};">{{ $rate }}%</span>
            </div>
            <div class="w-full rounded-full h-2 overflow-hidden" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <div class="h-2 rounded-full transition-all" style="width: {{ min($rate, 100) }}%; background-color: {{ $barBg }};"></div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t" style="border-color: var(--dash-card-border);">
            <div class="p-2.5 rounded-lg text-center" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Pelanggaran</p>
                <p class="text-base font-bold mt-0.5" style="color: var(--dash-status-danger-text);">{{ $data['total_violations'] ?? 0 }}</p>
            </div>
            <div class="p-2.5 rounded-lg text-center" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <p class="text-xs font-medium" style="color: var(--dash-text-muted);">Karyawan Terkena</p>
                <p class="text-base font-bold mt-0.5" style="color: var(--dash-status-warning-text);">{{ $data['employees_with_violations'] ?? 0 }}</p>
            </div>
        </div>

        @if(($data['active_warning_letters'] ?? 0) > 0)
            <div class="pt-2 border-t" style="border-color: var(--dash-card-border);">
                <p class="text-xs mb-2 font-medium" style="color: var(--dash-text-muted);">Surat Peringatan Aktif</p>
                <div class="flex gap-2">
                    @if(($data['sp1_count'] ?? 0) > 0)
                        <div class="text-center flex-1 p-2 rounded-lg" style="background-color: var(--dash-status-warning-bg);">
                            <span class="text-xs font-bold" style="color: var(--dash-status-warning-text);">SP1</span>
                            <p class="text-sm font-bold mt-0.5" style="color: var(--dash-status-warning-text);">{{ $data['sp1_count'] }}</p>
                        </div>
                    @endif
                    @if(($data['sp2_count'] ?? 0) > 0)
                        <div class="text-center flex-1 p-2 rounded-lg" style="background-color: var(--dash-status-warning-bg);">
                            <span class="text-xs font-bold" style="color: var(--dash-status-warning-text);">SP2</span>
                            <p class="text-sm font-bold mt-0.5" style="color: var(--dash-status-warning-text);">{{ $data['sp2_count'] }}</p>
                        </div>
                    @endif
                    @if(($data['sp3_count'] ?? 0) > 0)
                        <div class="text-center flex-1 p-2 rounded-lg" style="background-color: var(--dash-status-danger-bg);">
                            <span class="text-xs font-bold" style="color: var(--dash-status-danger-text);">SP3</span>
                            <p class="text-sm font-bold mt-0.5" style="color: var(--dash-status-danger-text);">{{ $data['sp3_count'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
