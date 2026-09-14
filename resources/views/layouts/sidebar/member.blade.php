<!-- 2.1 Umum -->
<x-dashboard.nav-item href="{{ route('member.dashboard') }}" icon="grid_view" :active="request()->routeIs('member.dashboard')">
    Dashboard
</x-dashboard.nav-item>

<x-dashboard.nav-group title="Pengaturan Akun" icon="settings" :active="request()->is('member/profile*') || request()->is('member/password*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true" :active="request()->is('member/profile*')">Profile Saya</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true" :active="request()->is('member/password*')">Ubah Password</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.2 Kelas dan Pembelajaran -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Akademik</p>
</div>
<x-dashboard.nav-group title="Kelas Saya" icon="school" :active="request()->is('member/class*') || request()->is('member/attendance*') || request()->is('member/certificate*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Daftar Kelas</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Materi Kurikulum</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Kehadiran & Nilai</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Sertifikat</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.3 Pembayaran dan Registrasi -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Administrasi</p>
</div>
<x-dashboard.nav-group title="Pembayaran" icon="payments" :active="request()->is('member/payment*') || request()->is('member/registration*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Riwayat Registrasi</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Status Cicilan</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Upload Bukti Bayar</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.4 Review dan Survey -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Lainnya</p>
</div>
<x-dashboard.nav-group title="Survei & Review" icon="reviews" :active="request()->is('member/survey*') || request()->is('member/review*') || request()->is('member/nps*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Survey Saya</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Review Program</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">NPS Response</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.5 Support dan Bantuan -->
<x-dashboard.nav-group title="Support" icon="support_agent" :active="request()->is('member/support*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Tiket Bantuan</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Chat & Diskusi</x-dashboard.nav-item>
</x-dashboard.nav-group>
