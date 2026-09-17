@props(['data'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-xl">work</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Job Postings</h3>
                <p class="text-xs text-gray-400">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-gray-400 mb-1">Total Lowongan</p>
            <p class="text-2xl font-bold text-gray-900 font-jakarta">{{ $data['total_postings'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-gray-400">Baru Bulan Ini</p>
                <p class="text-sm font-semibold text-blue-600">{{ $data['new_this_month'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400">Segera Tutup</p>
                <p class="text-sm font-semibold text-amber-600">{{ $data['closing_soon'] ?? 0 }}</p>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-gray-400">Total Lamaran</span>
            <span class="text-sm font-semibold text-gray-700">{{ $data['total_applications'] ?? 0 }}</span>
        </div>

        @if(!empty($data['by_status']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-gray-400 mb-2">Status</p>
            @foreach($data['by_status'] as $status => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-gray-600">{{ ucfirst($status) }}</span>
                    <span class="text-xs font-semibold {{ $status === 'published' ? 'text-emerald-600' : ($status === 'closed' ? 'text-red-500' : 'text-amber-600') }}">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
