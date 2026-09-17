@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-rose-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-rose-500 text-xl">support_agent</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Support Tickets</h3>
            <p class="text-xs text-gray-400">Assigned to me</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-3 gap-2">
            <div class="text-center p-2 rounded-lg bg-yellow-50">
                <p class="text-xl font-bold text-yellow-600">{{ $data['open'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Open</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-blue-50">
                <p class="text-xl font-bold text-blue-600">{{ $data['in_progress'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-0.5">In Progress</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-emerald-50">
                <p class="text-xl font-bold text-emerald-600">{{ $data['resolved'] ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Resolved</p>
            </div>
        </div>
        @forelse($data['recent'] ?? [] as $ticket)
            <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate">{{ $ticket['subject'] ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $ticket['member'] ?? '-' }}</p>
                </div>
                <span class="text-xs ml-2 px-1.5 py-0.5 rounded capitalize
                    {{ $ticket['status'] === 'open' ? 'bg-yellow-100 text-yellow-700' : ($ticket['status'] === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') }}">
                    {{ str_replace('_', ' ', $ticket['status'] ?? '-') }}
                </span>
            </div>
        @empty
            <p class="text-xs text-gray-400 text-center py-2">Tidak ada tiket</p>
        @endforelse
    </div>
</div>
