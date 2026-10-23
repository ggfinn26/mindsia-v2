@props(['data'])

<div class="rounded-2xl transition-all p-5 flex flex-col h-full" style="background-color: var(--dash-card-bg); border: 1px solid var(--dash-card-border);">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg flex items-center justify-center shrink-0" style="background-color: var(--dash-primary-bg); color: var(--dash-primary-text);">
            <span class="material-symbols-outlined text-xl">class</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold" style="color: var(--dash-text-heading);">My Classes</h3>
            <p class="text-xs" style="color: var(--dash-text-muted);">Kelas yang saya ampu</p>
        </div>
    </div>
    <div class="space-y-2 flex-grow">
        <div class="flex items-center gap-2 p-2 rounded-lg" style="background-color: var(--dash-primary-bg); border: 1px solid var(--dash-primary);">
            <span class="material-symbols-outlined text-base" style="color: var(--dash-primary-text);">book</span>
            <span class="text-xs font-semibold" style="color: var(--dash-primary-text);">{{ $data['total_classes'] ?? 0 }} kelas aktif · {{ $data['total_students'] ?? 0 }} siswa</span>
        </div>
        @forelse($data['classes'] ?? [] as $class)
            <div class="flex items-center justify-between p-2.5 rounded-lg" style="background-color: var(--dash-nav-hover-bg); border: 1px solid var(--dash-card-border);">
                <div class="min-w-0">
                    <p class="text-xs font-semibold truncate" style="color: var(--dash-text-heading);">{{ $class['name'] }}</p>
                    <p class="text-[11px]" style="color: var(--dash-text-muted);">{{ $class['program'] ?? '-' }}</p>
                </div>
                <span class="text-xs font-bold shrink-0 ml-2" style="color: var(--dash-primary-text);">{{ $class['member_count'] }} siswa</span>
            </div>
        @empty
            <div class="py-4 text-center">
                <p class="text-xs" style="color: var(--dash-text-muted);">Tidak ada kelas</p>
            </div>
        @endforelse
    </div>
</div>
