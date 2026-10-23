<!-- 2.1 Umum -->
<x-dashboard.nav-item href="{{ route('member.dashboard') }}" icon="grid_view" :active="request()->routeIs('member.dashboard')">
    Dashboard
</x-dashboard.nav-item>

<x-dashboard.nav-group title="Pengaturan Akun" icon="settings" :active="request()->is('member/profile*') || request()->is('member/change-password*') || request()->routeIs('member.profile.*') || request()->routeIs('member.password.*')">
    <x-dashboard.nav-item href="{{ route('member.profile.show') }}" :isChild="true" :active="request()->routeIs('member.profile.*')">Profile Saya</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.password.change') }}" :isChild="true" :active="request()->routeIs('member.password.*')">Ubah Password</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.1a Program -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold uppercase tracking-wider font-jakarta" style="color: var(--dash-nav-section);">Program</p>
</div>
<x-dashboard.nav-group title="Program" icon="local_library" :active="request()->is('member/program*') || request()->routeIs('member.program.*')">
    <x-dashboard.nav-item href="{{ route('member.program.my') }}" :isChild="true" :active="request()->routeIs('member.program.my*')">Program Saya</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.program.browse') }}" :isChild="true" :active="request()->routeIs('member.program.browse*') || request()->routeIs('member.program.register*')">Daftar Program</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.2 Kelas dan Pembelajaran -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold uppercase tracking-wider font-jakarta" style="color: var(--dash-nav-section);">Akademik</p>
</div>
<x-dashboard.nav-group title="Kelas Saya" icon="school" :active="request()->is('member/class*') || request()->is('member/curriculum*') || request()->is('member/attendance*') || request()->routeIs('member.class.*') || request()->routeIs('member.curriculum.*') || request()->routeIs('member.attendance.*')">
    <x-dashboard.nav-item href="{{ route('member.class.index') }}" :isChild="true" :active="request()->routeIs('member.class.*')">Jadwal Kelas</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.curriculum.index') }}" :isChild="true" :active="request()->routeIs('member.curriculum.*')">Materi Kurikulum</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.attendance.index') }}" :isChild="true" :active="request()->routeIs('member.attendance.*')">Kehadiran & Nilai</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.3 Pembayaran dan Registrasi -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold uppercase tracking-wider font-jakarta" style="color: var(--dash-nav-section);">Administrasi</p>
</div>
<x-dashboard.nav-group title="Pembayaran" icon="payments" :active="request()->is('member/payment*') || request()->is('member/registration*') || request()->routeIs('member.payment.*') || request()->routeIs('member.registration.*')">
    <x-dashboard.nav-item href="{{ route('member.registration.index') }}" :isChild="true" :active="request()->routeIs('member.registration.*')">Riwayat Registrasi</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.payment.installments') }}" :isChild="true" :active="request()->routeIs('member.payment.*')">Status Cicilan</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.4 TOEFL -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold uppercase tracking-wider font-jakarta" style="color: var(--dash-nav-section);">Ujian</p>
</div>
<x-dashboard.nav-item href="{{ route('toefl.session.index') }}" icon="quiz" :active="request()->is('member/toefl*') || request()->is('toefl*') || request()->routeIs('toefl.session.*')">TOEFL</x-dashboard.nav-item>

<!-- 2.5 Review dan Survey -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold uppercase tracking-wider font-jakarta" style="color: var(--dash-nav-section);">Lainnya</p>
</div>
<x-dashboard.nav-group title="Survei & Review" icon="reviews" :active="request()->is('member/survey*') || request()->is('member/review*') || request()->is('member/nps*') || request()->routeIs('member.surveys.*') || request()->routeIs('member.reviews.*') || request()->routeIs('member.nps.*')">
    <x-dashboard.nav-item href="{{ route('member.surveys.index') }}" :isChild="true" :active="request()->routeIs('member.surveys.*')">Survey Saya</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.reviews.index') }}" :isChild="true" :active="request()->routeIs('member.reviews.*')">Review Program</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.nps.index') }}" :isChild="true" :active="request()->routeIs('member.nps.*')">NPS Response</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.6 Support Ticket -->
<x-dashboard.nav-item href="{{ route('member.tickets.index') }}" icon="confirmation_number" :active="request()->is('member/my-tickets*') || request()->routeIs('member.tickets.*')">Tiket Bantuan</x-dashboard.nav-item>
<x-dashboard.nav-item href="{{ route('member.notifications.index') }}" icon="notifications" :active="request()->routeIs('member.notifications.*')">Notifikasi Saya</x-dashboard.nav-item>
