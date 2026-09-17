@props(['data'])
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
    <div class="flex items-center gap-3 mb-4">
        <div class="h-10 w-10 rounded-lg bg-green-50 flex items-center justify-center">
            <span class="material-symbols-outlined text-green-600 text-xl">online_prediction</span>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-gray-800">Active Users</h3>
            <p class="text-xs text-gray-400">Online sekarang</p>
        </div>
    </div>
    <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
            <div class="text-center p-3 rounded-lg bg-green-50">
                <div class="flex items-center justify-center gap-1.5 mb-1">
                    <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                    <p class="text-2xl font-bold text-green-600 font-jakarta">{{ $data['online_now'] ?? 0 }}</p>
                </div>
                <p class="text-xs text-gray-400">Online (15 menit)</p>
            </div>
            <div class="text-center p-3 rounded-lg bg-blue-50">
                <p class="text-2xl font-bold text-blue-600 font-jakarta">{{ $data['active_today'] ?? 0 }}</p>
                <p class="text-xs text-gray-400">Aktif Hari Ini</p>
            </div>
        </div>
        @if(!empty($data['recent_active']))
            <div class="pt-2 border-t border-gray-50">
                <p class="text-xs text-gray-400 mb-2">Terakhir Login</p>
                <div class="space-y-1.5">
                    @foreach(array_slice($data['recent_active'], 0, 5) as $user)
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full {{ isset($user['is_online']) && $user['is_online'] ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                            <span class="text-xs text-gray-700 flex-1 truncate">{{ $user['name'] }}</span>
                            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($user['last_login_at'])->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
