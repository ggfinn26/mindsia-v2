@props(['data'])
<div class="bg-[var(--dash-card-bg)] rounded-xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-blue-600 text-xl">payments</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Payment Status</h3>
            <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
        </div>
    </div>
    <div class="space-y-3">
        @foreach($data['by_status'] ?? [] as $status => $info)
            <div class="flex items-center justify-between">
                <span class="text-xs capitalize text-[color:var(--dash-text-body)]">{{ str_replace('_', ' ', $status) }}</span>
                <div class="text-right">
                    <span class="text-xs font-bold text-[color:var(--dash-text-heading)]">{{ $info['count'] }}</span>
                    <span class="text-xs text-[color:var(--dash-text-faint)] ml-1">/ Rp {{ number_format($info['total'], 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach
        <div class="pt-2 border-t border-gray-50 grid grid-cols-2 gap-2">
            <div class="text-center p-2 rounded-lg bg-yellow-50">
                <p class="text-lg font-bold text-yellow-600">{{ $data['due_soon_count'] ?? 0 }}</p>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Due 7 Hari</p>
            </div>
            <div class="text-center p-2 rounded-lg bg-red-50">
                <p class="text-lg font-bold text-red-500">{{ $data['overdue_count'] ?? 0 }}</p>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Overdue</p>
            </div>
        </div>
    </div>
</div>
