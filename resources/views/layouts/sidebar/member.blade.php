<!-- 2.1 Umum -->
<x-dashboard.nav-item href="{{ route('member.dashboard') }}" icon="grid_view" :active="request()->routeIs('member.dashboard')">
    Dashboard
</x-dashboard.nav-item>

<x-dashboard.nav-group title="Pengaturan Akun" icon="settings" :active="request()->is('member/profile*') || request()->is('member/change-password*')">
    <x-dashboard.nav-item href="{{ route('member.profile.show') }}" :isChild="true" :active="request()->routeIs('member.profile.show')">Profile Saya</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.password.change') }}" :isChild="true" :active="request()->routeIs('member.password.change')">Ubah Password</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.1a Program -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Program</p>
</div>
<x-dashboard.nav-group title="Program" icon="local_library" :active="request()->is('member/program*')">
    <x-dashboard.nav-item href="{{ route('member.program.my') }}" :isChild="true" :active="request()->routeIs('member.program.my')">Program Saya</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.program.browse') }}" :isChild="true" :active="request()->routeIs('member.program.browse')">Daftar Program</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.2 Kelas dan Pembelajaran -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Akademik</p>
</div>
<x-dashboard.nav-group title="Kelas Saya" icon="school" :active="request()->is('member/class*') || request()->is('member/curriculum*') || request()->is('member/attendance*')">
    <x-dashboard.nav-item href="{{ route('member.class.index') }}" :isChild="true" :active="request()->routeIs('member.class.index')">Jadwal Kelas</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.curriculum.index') }}" :isChild="true" :active="request()->routeIs('member.curriculum.index')">Materi Kurikulum</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.attendance.index') }}" :isChild="true" :active="request()->routeIs('member.attendance.index')">Kehadiran & Nilai</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.3 Pembayaran dan Registrasi -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Administrasi</p>
</div>
<x-dashboard.nav-group title="Pembayaran" icon="payments" :active="request()->is('member/payment*') || request()->is('member/registration*')">
    <x-dashboard.nav-item href="{{ route('member.registration.index') }}" :isChild="true" :active="request()->routeIs('member.registration.index')">Riwayat Registrasi</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.payment.installments') }}" :isChild="true" :active="request()->routeIs('member.payment.installments')">Status Cicilan</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.4 TOEFL -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Ujian</p>
</div>
<x-dashboard.nav-item href="{{ route('toefl.session.index') }}" icon="quiz" :active="request()->is('member/toefl*') || request()->routeIs('toefl.session.*')">TOEFL</x-dashboard.nav-item>

<!-- 2.5 Review dan Survey -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Lainnya</p>
</div>
<x-dashboard.nav-group title="Survei & Review" icon="reviews" :active="request()->is('member/survey*') || request()->is('member/review*') || request()->is('member/nps*')">
    <x-dashboard.nav-item href="{{ route('member.surveys.index') }}" :isChild="true" :active="request()->routeIs('member.surveys.index') || request()->routeIs('member.surveys.show')">Survey Saya</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.reviews.index') }}" :isChild="true" :active="request()->routeIs('member.reviews.index')">Review Program</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('member.nps.index') }}" :isChild="true" :active="request()->routeIs('member.nps.index')">NPS Response</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- 2.6 Support Ticket -->
<x-dashboard.nav-item href="{{ route('member.tickets.index') }}" icon="confirmation_number" :active="request()->is('member/my-tickets*') || request()->routeIs('member.tickets.*')">Tiket Bantuan</x-dashboard.nav-item>
<x-dashboard.nav-item href="{{ route('member.notifications.index') }}" icon="notifications" :active="request()->routeIs('member.notifications.index')">Notifikasi Saya</x-dashboard.nav-item>

