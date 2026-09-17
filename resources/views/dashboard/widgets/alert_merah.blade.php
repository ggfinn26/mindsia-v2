@props(['data'])

@php
    $alerts = $data['alerts'] ?? [];
    $dangerCount = $data['danger_count'] ?? 0;
    $warningCount = $data['warning_count'] ?? 0;
    $hasAlerts = count($alerts) > 0;
@endphp

<div class="bg-white rounded-xl shadow-sm border border-danger-100 border-2 {{ $hasAlerts ? 'border-red-200' : 'border-gray-100' }} p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg {{ $dangerCount > 0 ? 'bg-red-50' : ($warningCount > 0 ? 'bg-amber-50' : 'bg-gray-50') }} flex items-center justify-center">
                <span class="material-symbols-outlined {{ $dangerCount > 0 ? 'text-red-500' : ($warningCount > 0 ? 'text-amber-500' : 'text-gray-400') }} text-xl">warning</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Alert Merah</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
        @if($hasAlerts)
            <div class="flex gap-1">
                @if($dangerCount > 0)
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-700">{{ $dangerCount }} critical</span>
                @endif
                @if($warningCount > 0)
                    <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-amber-100 text-amber-700">{{ $warningCount }} warning</span>
                @endif
            </div>
        @endif
    </div>

    <div class="space-y-3">
        @if($hasAlerts)
            @foreach($alerts as $alert)
                <div class="flex items-center justify-between p-3 rounded-lg {{ $alert['severity'] === 'danger' ? 'bg-red-50' : 'bg-amber-50' }}">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm {{ $alert['severity'] === 'danger' ? 'text-red-500' : 'text-amber-500' }}">
                            {{ $alert['severity'] === 'danger' ? 'error' : 'warning' }}
                        </span>
                        <span class="text-xs font-medium {{ $alert['severity'] === 'danger' ? 'text-red-700' : 'text-amber-700' }}">{{ $alert['label'] }}</span>
                    </div>
                    <span class="text-sm font-bold {{ $alert['severity'] === 'danger' ? 'text-red-600' : 'text-amber-600' }}">{{ $alert['count'] }}</span>
                </div>
            @endforeach
        @else
            <div class="text-center py-4">
                <span class="material-symbols-outlined text-3xl text-emerald-300">check_circle</span>
                <p class="text-xs text-gray-400 mt-1">Tidak ada alert aktif</p>
            </div>
        @endif
    </div>
</div>
