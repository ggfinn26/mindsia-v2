@props(['data'])

<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-blue-600 text-xl">work</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Job Postings</h3>
                <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Total Lowongan</p>
            <p class="text-2xl font-bold text-[color:var(--dash-text-heading)] font-jakarta">{{ $data['total_postings'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Baru Bulan Ini</p>
                <p class="text-sm font-semibold text-blue-600">{{ $data['new_this_month'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Segera Tutup</p>
                <p class="text-sm font-semibold text-amber-600">{{ $data['closing_soon'] ?? 0 }}</p>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-gray-50">
            <span class="text-xs text-[color:var(--dash-text-faint)]">Total Lamaran</span>
            <span class="text-sm font-semibold text-[color:var(--dash-text-body)]">{{ $data['total_applications'] ?? 0 }}</span>
        </div>

        @if(!empty($data['by_status']))
        <div class="pt-2 border-t border-gray-50">
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-2">Status</p>
            @foreach($data['by_status'] as $status => $count)
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs text-[color:var(--dash-text-body)]">{{ ucfirst($status) }}</span>
                    <span class="text-xs font-semibold {{ $status === 'published' ? 'text-emerald-600' : ($status === 'closed' ? 'text-red-500' : 'text-amber-600') }}">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
