@props(['data'])

<div class="bg-[var(--dash-card-bg)] rounded-2xl shadow-sm border border-[color:var(--dash-card-border)] p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-violet-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-violet-600 text-xl">filter_list</span>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-[color:var(--dash-text-heading)]">Candidate Screening</h3>
                <p class="text-xs text-[color:var(--dash-text-faint)]">{{ $data['period'] ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <p class="text-xs text-[color:var(--dash-text-faint)] mb-1">Dalam Screening</p>
            <p class="text-2xl font-bold text-[color:var(--dash-text-heading)] font-jakarta">{{ $data['in_screening'] ?? 0 }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Total Discreening</p>
                <p class="text-sm font-semibold text-[color:var(--dash-text-body)]">{{ $data['total_screened'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Pass Rate</p>
                <p class="text-sm font-semibold {{ ($data['pass_rate'] ?? 0) >= 50 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $data['pass_rate'] ?? 0 }}%</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-50">
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Psikotest Selesai</p>
                <p class="text-sm font-semibold text-[color:var(--dash-text-body)]">{{ $data['psikotest_completed'] ?? 0 }}</p>
            </div>
            <div>
                <p class="text-xs text-[color:var(--dash-text-faint)]">Rata-rata Skor</p>
                <p class="text-sm font-semibold text-violet-600">{{ $data['avg_psikotest_score'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
