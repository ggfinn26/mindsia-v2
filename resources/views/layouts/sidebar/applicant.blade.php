<!-- 3.1 Umum -->
<x-dashboard.nav-item href="{{ route('applicant.dashboard') }}" icon="grid_view" :active="request()->routeIs('applicant.dashboard')">
    Dashboard
</x-dashboard.nav-item>

<x-dashboard.nav-group title="Pengaturan Akun" icon="settings" :active="request()->is('applicant/settings*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Profile Akun</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Ubah Password</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 3.2 Lowongan dan Aplikasi -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Karir</p>
</div>
<x-dashboard.nav-group title="Lowongan Kerja" icon="work" :active="request()->is('applicant/job*') || request()->is('applicant/application*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Cari Lowongan</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Status Lamaran</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Jadwal Interview</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 3.3 Profile -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Resume</p>
</div>
<x-dashboard.nav-group title="Curriculum Vitae" icon="description" :active="request()->is('applicant/profile*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Data Diri</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Pendidikan</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Pengalaman Kerja</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Upload CV / Berkas</x-dashboard.nav-item>
</x-dashboard.nav-group>
