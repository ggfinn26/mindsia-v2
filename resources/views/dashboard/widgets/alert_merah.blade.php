@if(isset($data['error']))
    <div class="p-4 bg-red-50 text-red-600 rounded-xl">Error: {{ $data['error'] }}</div>
@else
<div class="bg-white rounded-xl shadow-sm border border-red-100 p-5 h-full flex flex-col">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-lg">error</span>
            </div>
            <h3 class="text-sm font-semibold text-gray-800">Critical Alerts</h3>
        </div>
        <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded-md">{{ $data['period'] }}</span>
    </div>

    @if($data['total_alerts'] === 0)
        <div class="flex-1 flex flex-col items-center justify-center py-6 text-center">
            <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-2xl">check_circle</span>
            </div>
            <p class="text-sm font-medium text-gray-700">Tidak Ada Isu Kritis</p>
            <p class="text-xs text-gray-500 mt-1">Semua operasional berjalan normal.</p>
        </div>
    @else
        <div class="flex items-center gap-4 mb-4 pb-4 border-b border-gray-100">
            <div class="flex-1">
                <p class="text-xs text-gray-500 font-medium">Danger</p>
                <p class="text-xl font-bold text-red-600">{{ $data['danger_count'] }}</p>
            </div>
            <div class="w-px h-8 bg-gray-200"></div>
            <div class="flex-1">
                <p class="text-xs text-gray-500 font-medium">Warning</p>
                <p class="text-xl font-bold text-amber-500">{{ $data['warning_count'] }}</p>
            </div>
        </div>

        <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar pr-2 max-h-[250px]">
            @foreach($data['alerts'] as $alert)
                <div class="flex items-start justify-between p-3 rounded-lg border {{ $alert['severity'] === 'danger' ? 'bg-red-50 border-red-100' : 'bg-amber-50 border-amber-100' }}">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-base mt-0.5 {{ $alert['severity'] === 'danger' ? 'text-red-600' : 'text-amber-600' }}">
                            {{ $alert['severity'] === 'danger' ? 'warning' : 'info' }}
                        </span>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $alert['label'] }}</p>
                            <a href="#" class="text-xs font-medium hover:underline mt-1 inline-block {{ $alert['severity'] === 'danger' ? 'text-red-600' : 'text-amber-700' }}">
                                Tindak Lanjuti &rarr;
                            </a>
                        </div>
                    </div>
                    <span class="text-base font-bold {{ $alert['severity'] === 'danger' ? 'text-red-700' : 'text-amber-700' }}">
                        {{ $alert['count'] }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endif
