@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-600 text-xl">how_to_reg</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Onboarding Status</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Total Onboarding</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['total'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Selesai Bulan Ini</p>
                <p class="text-sm font-semibold text-emerald-600">{{ $data['completed_this_month'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Menunggu Review</p>
                <p class="text-sm font-semibold text-amber-600">{{ $data['pending_review'] ?? 0 }}</p>
            </div>
        </div>

        @if(!empty($data['by_status']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Status</p>
            @foreach($data['by_status'] as $status => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-600">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                    <span class="text-xs font-semibold {{ $status === 'completed' ? 'text-emerald-600' : ($status === 'rejected' ? 'text-red-500' : 'text-amber-600') }}">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
