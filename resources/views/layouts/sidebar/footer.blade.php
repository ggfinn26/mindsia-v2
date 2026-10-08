@php
    $guard = null;
    $name = 'Pengguna';
    $email = '';
    $logoutRoute = '';
    
    $isMemberRoute = request()->is('member/*') || request()->routeIs('member.*');
    $isApplicantRoute = request()->is('applicant/*') || request()->routeIs('applicant.*');

    if ($isMemberRoute && Auth::guard('member')->check()) {
        $guard = 'member';
        $user = Auth::guard('member')->user();
        $name = $user->memberData->full_name ?? 'Member';
        $email = $user->email ?? '';
        $logoutRoute = route('member.logout');
    } elseif ($isApplicantRoute && Auth::guard('applicant')->check()) {
        $guard = 'applicant';
        $user = Auth::guard('applicant')->user();
        $name = $user->applicantData->full_name ?? 'Pelamar';
        $email = $user->email ?? '';
        $logoutRoute = route('applicant.logout');
    } elseif (Auth::guard('web')->check()) {
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
        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full"
              style="background-color: var(--dash-primary-bg);">
            <span class="font-bold leading-none" style="color: var(--dash-primary-text);">{{ substr($name, 0, 1) }}</span>
        </span>
    </div>
    <div class="ml-3 min-w-0 flex-1">
        <p class="truncate text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $name }}</p>
        <p class="truncate text-xs" style="color: var(--dash-text-muted);">{{ $email }}</p>
    </div>
</div>

<form method="POST" action="{{ $logoutRoute }}">
    @csrf
    <button type="submit"
            class="w-full flex justify-center items-center gap-2 rounded-lg px-3 py-2 min-h-[44px] text-sm font-semibold transition-colors hover:text-red-600 hover:border-red-200 hover:bg-red-50 active:bg-red-100"
            style="background-color: var(--dash-logout-bg); border: 1px solid var(--dash-logout-border); color: var(--dash-logout-text);">
        <span class="material-symbols-outlined text-[18px]">logout</span>
        Keluar
    </button>
</form>

{{-- Theme switcher --}}
<div class="mt-3 flex items-center justify-between gap-2" x-data="dashTheme()">
    {{-- Color dots --}}
    <div class="flex items-center gap-1.5">
        <button @click="setTheme('blue')"
                class="w-5 h-5 rounded-full transition-all"
                :style="'background:#5586DB;' + (theme==='blue' ? 'outline:2px solid #5586DB;outline-offset:2px;' : '')"
                title="Tema Biru"></button>
        <button @click="setTheme('cyan')"
                class="w-5 h-5 rounded-full transition-all"
                :style="'background:#00AACC;' + (theme==='cyan' ? 'outline:2px solid #00AACC;outline-offset:2px;' : '')"
                title="Tema Cyan"></button>
        {{-- ponytail: gold guard dinonaktifkan sementara untuk development --}}
        <button @click="setTheme('gold')"
                class="w-5 h-5 rounded-full transition-all"
                :style="'background:#DBB940;' + (theme==='gold' ? 'outline:2px solid #DBB940;outline-offset:2px;' : '')"
                title="Tema Gold"></button>
    </div>
    {{-- Dark/light toggle --}}
    <button @click="toggleMode()"
            class="flex items-center justify-center w-8 h-8 rounded-lg transition-colors"
            style="background-color: var(--dash-nav-hover-bg); color: var(--dash-text-muted);"
            :title="mode==='dark' ? 'Mode Terang' : 'Mode Gelap'">
        <span class="material-symbols-outlined text-[18px]" x-text="mode==='dark' ? 'light_mode' : 'dark_mode'"></span>
    </button>
</div>
@endif
