<!-- Umum -->
<x-dashboard.nav-item href="{{ route('applicant.dashboard') }}" icon="grid_view" :active="request()->routeIs('applicant.dashboard')">
    Dashboard
</x-dashboard.nav-item>

<x-dashboard.nav-group title="Pengaturan Akun" icon="settings" :active="request()->routeIs('applicant.profile.*') || request()->routeIs('applicant.password.*')">
    <x-dashboard.nav-item href="{{ route('applicant.profile.show') }}" :isChild="true" :active="request()->routeIs('applicant.profile.*')">Profile Akun</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('applicant.password.change') }}" :isChild="true" :active="request()->routeIs('applicant.password.change')">Ubah Password</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- Karir -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Karir</p>
</div>
<x-dashboard.nav-group title="Lowongan Kerja" icon="work" :active="request()->routeIs('applicant.job-listings.*') || request()->routeIs('applicant.applications.*') || request()->routeIs('applicant.interviews.*')">
    <x-dashboard.nav-item href="{{ route('applicant.job-listings.index') }}" :isChild="true" :active="request()->routeIs('applicant.job-listings.*')">Cari Lowongan</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('applicant.applications.index') }}" :isChild="true" :active="request()->routeIs('applicant.applications.index')">Status Lamaran</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('applicant.interviews.index') }}" :isChild="true" :active="request()->routeIs('applicant.interviews.*')">Jadwal Interview</x-dashboard.nav-item>
</x-dashboard.nav-group>

