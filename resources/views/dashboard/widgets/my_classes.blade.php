@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-violet-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-violet-600 text-xl">class</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">My Classes</h3>
            <p class="text-xs text-gray-400">Kelas yang saya ampu</p>
        </div>
    </div>
    <div class="space-y-2">
        <div class="flex items-center gap-2 p-2 rounded-lg bg-violet-50">
            <span class="material-symbols-outlined text-violet-500 text-base">book</span>
            <span class="text-xs font-medium text-violet-700">{{ $data['total_classes'] ?? 0 }} kelas aktif · {{ $data['total_students'] ?? 0 }} siswa</span>
        </div>
        @forelse($data['classes'] ?? [] as $class)
            <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate">{{ $class['name'] }}</p>
                    <p class="text-xs text-gray-400">{{ $class['program'] ?? '-' }}</p>
                </div>
                <span class="text-xs font-bold text-violet-600 ml-2">{{ $class['member_count'] }} siswa</span>
            </div>
        @empty
            <p class="text-xs text-gray-400 text-center py-4">Tidak ada kelas</p>
        @endforelse
    </div>
</div>
