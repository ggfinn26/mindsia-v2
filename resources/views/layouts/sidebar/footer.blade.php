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
{{-- Theme Switcher --}}
<div x-data="dashTheme()" class="mb-3 flex items-center justify-between">
    <div class="flex items-center gap-2">
        <button @click="setTheme('blue')"
                class="w-4 h-4 rounded-full transition-all"
                :style="theme === 'blue' ? 'background:#5586db; outline: 2px solid #5586db; outline-offset: 2px;' : 'background:#5586db; opacity:0.5;'"
                title="Tema Biru"></button>
        <button @click="setTheme('cyan')"
                class="w-4 h-4 rounded-full transition-all"
                :style="theme === 'cyan' ? 'background:#00aacc; outline: 2px solid #00aacc; outline-offset: 2px;' : 'background:#00aacc; opacity:0.5;'"
                title="Tema Cyan"></button>
        {{-- Gold hanya untuk Direksi & Super Admin — ponytail: guard di backend saat save ke DB --}}
        <button @click="setTheme('gold')"
                class="w-4 h-4 rounded-full transition-all"
                :style="theme === 'gold' ? 'background:#dbb940; outline: 2px solid #dbb940; outline-offset: 2px;' : 'background:#dbb940; opacity:0.5;'"
                title="Tema Gold"></button>
    </div>
    <button @click="toggleMode()"
            class="inline-flex items-center justify-center w-7 h-7 rounded-md transition-colors"
            style="color: var(--dash-text-faint);"
            onmouseover="this.style.backgroundColor='var(--dash-nav-hover-bg)'; this.style.color='var(--dash-text-muted)'"
            onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--dash-text-faint)'"
            :title="mode === 'dark' ? 'Mode Terang' : 'Mode Gelap'">
        <span class="material-symbols-outlined text-[16px]" x-text="mode === 'dark' ? 'light_mode' : 'dark_mode'"></span>
    </button>
</div>

{{-- User Info --}}
<div class="flex items-center mb-3">
    <div class="flex-shrink-0">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full" style="background-color: var(--dash-primary-bg);">
            <span class="font-bold leading-none text-sm" style="color: var(--dash-primary);">{{ substr($name, 0, 1) }}</span>
        </span>
    </div>
    <div class="ml-2.5 min-w-0 flex-1">
        <p class="truncate text-sm font-bold font-jakarta" style="color: var(--dash-text-heading);">{{ $name }}</p>
        <p class="truncate text-xs" style="color: var(--dash-text-muted);">{{ $email }}</p>
    </div>
</div>

<form method="POST" action="{{ $logoutRoute }}">
    @csrf
    <button type="submit"
            class="w-full flex justify-center items-center gap-2 rounded-lg px-3 py-2 min-h-[44px] text-sm font-semibold transition-colors"
            style="background-color: var(--dash-logout-bg); color: var(--dash-logout-text); border: 1px solid var(--dash-logout-border);"
            onmouseover="this.style.backgroundColor='#fee2e2'; this.style.color='#dc2626'; this.style.borderColor='#fecaca'"
            onmouseout="this.style.backgroundColor='var(--dash-logout-bg)'; this.style.color='var(--dash-logout-text)'; this.style.borderColor='var(--dash-logout-border)'">
        <span class="material-symbols-outlined text-[18px]">logout</span>
        Keluar
    </button>
</form>
@endif
