@php
    $guard = null;
    $name = 'Pengguna';
    $email = '';
    $logoutRoute = '';
    
    if (Auth::guard('web')->check()) {
        $guard = 'web';
        $user = Auth::guard('web')->user();
        $name = $user->name ?? 'Pengguna';
        $email = $user->email ?? '';
        $logoutRoute = route('logout');
    } elseif (Auth::guard('member')->check()) {
        $guard = 'member';
        $user = Auth::guard('member')->user();
        $name = $user->memberData->full_name ?? 'Member';
        $email = $user->email ?? '';
        $logoutRoute = route('member.logout');
    } elseif (Auth::guard('applicant')->check()) {
        $guard = 'applicant';
        $user = Auth::guard('applicant')->user();
        $name = $user->applicantData->full_name ?? 'Pelamar';
        $email = $user->email ?? '';
        $logoutRoute = route('applicant.logout');
    }
@endphp

@if($guard)
<div class="flex items-center mb-4">
    <div class="flex-shrink-0">
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#EEF4FF]">
            <span class="font-bold leading-none text-[#5586DB]">{{ substr($name, 0, 1) }}</span>
        </span>
    </div>
    <div class="ml-3 min-w-0 flex-1">
        <p class="truncate text-sm font-bold text-gray-900 font-jakarta">{{ $name }}</p>
        <p class="truncate text-xs text-gray-500">{{ $email }}</p>
    </div>
</div>

<form method="POST" action="{{ $logoutRoute }}">
    @csrf
    <button type="submit" class="w-full flex justify-center items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 min-h-[44px] text-sm font-semibold text-gray-700 hover:text-red-600 border border-gray-200 hover:border-red-200 hover:bg-red-50 active:bg-red-100 transition-colors">
        <span class="material-symbols-outlined text-[18px]">logout</span>
        Keluar
    </button>
</form>
@endif
