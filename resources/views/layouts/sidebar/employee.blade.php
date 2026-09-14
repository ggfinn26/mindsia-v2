<!-- 1.1 Umum -->
<x-dashboard.nav-item href="{{ route('dashboard') }}" icon="grid_view" :active="request()->routeIs('dashboard')">
    Dashboard
</x-dashboard.nav-item>

<x-dashboard.nav-group title="Pengaturan Akun" icon="settings" :active="request()->is('employee/profile*') || request()->is('employee/password*')">
    <x-dashboard.nav-item href="{{ route('employee.profile.show') }}" :isChild="true" :active="request()->routeIs('employee.profile.show')">Profile Saya</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('employee.password.edit') }}" :isChild="true" :active="request()->routeIs('employee.password.edit')">Ubah Password</x-dashboard.nav-item>
</x-dashboard.nav-group>

<!-- Strategic Alert Center -->
@can('strategic_alerts.view')
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-red-400 uppercase tracking-wider font-jakarta">Strategic Alert Center</p>
</div>
<x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" icon="warning" :active="request()->is('admin/strategic-alert*')" class="text-red-600 hover:bg-red-50 hover:text-red-700">
    Papan Krisis
</x-dashboard.nav-item>
@endcan

<!-- Personal Dashboard -->
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Personal Dashboard</p>
</div>
<x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" icon="receipt_long" :active="request()->is('employee/payslip*')">Slip Gaji Saya</x-dashboard.nav-item>
<x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" icon="track_changes" :active="request()->is('employee/my-kpi*')">KPI Saya</x-dashboard.nav-item>
<x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" icon="format_list_numbered" :active="request()->is('marketing/my-performance*')">Ranking MPI Saya</x-dashboard.nav-item>
<x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" icon="history" :active="request()->is('attendance/my-recap*')">Riwayat Absensi Saya</x-dashboard.nav-item>
<x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" icon="login" :active="request()->is('attendance/check-in-out*')">Absen Masuk/Keluar</x-dashboard.nav-item>
<x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" icon="co_present" :active="request()->is('class/my-classes*')">Absen Sesi Saya</x-dashboard.nav-item>
<x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" icon="event_busy" :active="request()->is('attendance/leave-request*')">Izin/Sakit/Cuti Saya</x-dashboard.nav-item>
<x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" icon="mail" :active="request()->is('letter/my-letters*')">Surat Saya</x-dashboard.nav-item>
<x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" icon="campaign" :active="request()->is('marketing/my-templates*')">Template Marketing Saya</x-dashboard.nav-item>
<x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" icon="confirmation_number" :active="request()->is('facility/my-tickets*')">Tiket Saya</x-dashboard.nav-item>

<!-- 1.2 Organization -->
@canany(['organization.province.create', 'organization.branch.create', 'organization.institution.create'])
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Organisasi</p>
</div>
<x-dashboard.nav-group title="Kelola Organisasi" icon="corporate_fare" :active="request()->is('provinces*') || request()->is('regions*') || request()->is('areas*') || request()->is('branches*') || request()->is('institutions*')">
    <x-dashboard.nav-item href="{{ route('provinces.index') }}" :isChild="true" :active="request()->routeIs('provinces.*')">Provinsi</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('regions.index') }}" :isChild="true" :active="request()->routeIs('regions.*')">Region</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('areas.index') }}" :isChild="true" :active="request()->routeIs('areas.*')">Area</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('branches.index') }}" :isChild="true" :active="request()->routeIs('branches.*')">Kelola Cabang</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('institutions.index') }}" :isChild="true" :active="request()->routeIs('institutions.*')">Kelola Institusi</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan

<!-- 1.3 Kelola Pengguna -->
@if(auth()->user()->can('employee.view') || auth()->user()->can('position.view'))
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">SDM & Akses</p>
</div>
<x-dashboard.nav-group title="Kelola Pengguna" icon="manage_accounts" :active="request()->is('admin/employee/list*') || request()->is('admin/position*') || request()->routeIs('employees.*') || request()->routeIs('positions.*')">
    <x-dashboard.nav-item href="{{ route('employees.index') }}" :active="request()->routeIs('employees.*')" :isChild="true">Daftar Pegawai</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('positions.index') }}" :active="request()->routeIs('positions.*')" :isChild="true">Kelola Divisi dan Posisi</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endif

<!-- 1.3A Sistem & Akses -->
@can('system.permission.manage')
<x-dashboard.nav-group title="Sistem & Akses" icon="shield_person" :active="request()->is('admin/permission*') || request()->is('admin/dashboard-config*') || request()->routeIs('roles.*') || request()->routeIs('system.dashboard-widgets.*')">
    <x-dashboard.nav-item href="{{ route('roles.index') }}" :active="request()->routeIs('roles.*')" :isChild="true">Manajemen Permission</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('system.dashboard-widgets.index') }}" :active="request()->routeIs('system.dashboard-widgets.*')" :isChild="true">Widget Dashboard</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan

<!-- 1.4 Attendance -->
@canany(['attendance.document_signature.manage', 'attendance.adjustment.create', 'attendance.leave.review'])
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Manajemen Absensi</p>
</div>
<x-dashboard.nav-group title="Absensi" icon="how_to_reg" :active="request()->is('attendance*') && !request()->is('attendance/check-in-out*') && !request()->is('attendance/leave-request*') && !request()->is('attendance/my-recap*')">
    <x-dashboard.nav-item href="{{ route('attendance-policies.index') }}" :active="request()->routeIs('attendance-policies.*')" :isChild="true">Jadwal Kerja & Libur</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('attendance-recaps.index') }}" :active="request()->routeIs('attendance-recaps.*')" :isChild="true">Rekap Absensi Pegawai</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('leave-requests.manage') }}" :active="request()->routeIs('leave-requests.manage')" :isChild="true">Review Izin/Sakit/Cuti</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('work-attendance.manage') }}" :active="request()->routeIs('work-attendance.manage')" :isChild="true">Verifikasi & Koreksi</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('attendance-rules.index') }}" :active="request()->routeIs('attendance-rules.*')" :isChild="true">Kelola Sanksi Absen</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('attendance.document-signature.index') }}" :active="request()->routeIs('attendance.document-signature.*')" :isChild="true">Pengaturan TTD</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan

<!-- 1.5 Bonus & Payroll -->
@if(auth()->user()->can('payroll.component.create') || auth()->user()->can('payroll.component.update') || auth()->user()->can('payroll.period.view') || auth()->user()->can('payroll.period.generate') || auth()->user()->can('payroll.period.pay') || auth()->user()->canAny(['bonus.marketing-rule.view','bonus.kpi-rule.view','bonus.special-rule.view']))
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Payroll</p>
</div>
<x-dashboard.nav-group title="Gaji & Bonus" icon="payments" :active="request()->is('payroll*') || request()->is('bonus*')">
    @canany(['payroll.component.create', 'payroll.component.update'])
    <x-dashboard.nav-item href="{{ route('payroll.components.index') }}" :active="request()->routeIs('payroll.components.*')" :isChild="true">Komponen Gaji</x-dashboard.nav-item>
    @endcanany
    @canany(['bonus.marketing-rule.view','bonus.marketing-rule.create'])
    <x-dashboard.nav-item href="{{ route('bonus.marketing-rules.index') }}" :active="request()->routeIs('bonus.marketing-rules.*')" :isChild="true">Marketing Bonus</x-dashboard.nav-item>
    @endcanany
    @canany(['bonus.kpi-rule.view','bonus.kpi-rule.create'])
    <x-dashboard.nav-item href="{{ route('bonus.kpi-rules.index') }}" :active="request()->routeIs('bonus.kpi-rules.*')" :isChild="true">KPI Bonus</x-dashboard.nav-item>
    @endcanany
    @canany(['bonus.special-rule.view','bonus.special-rule.create'])
    <x-dashboard.nav-item href="{{ route('bonus.special-rules.index') }}" :active="request()->routeIs('bonus.special-rules.*')" :isChild="true">Special Bonus</x-dashboard.nav-item>
    @endcanany
    <x-dashboard.nav-item href="{{ route('bonus.history.index') }}" :active="request()->routeIs('bonus.history.*')" :isChild="true">Riwayat Bonus</x-dashboard.nav-item>
    @canany(['payroll.period.view', 'payroll.period.generate', 'payroll.period.update'])
    <x-dashboard.nav-item href="{{ route('payroll.periods.index') }}" :active="request()->routeIs('payroll.periods.*')" :isChild="true">Periode Payroll</x-dashboard.nav-item>
    @endcanany
    @can('payroll.period.pay')
    <x-dashboard.nav-item href="{{ route('payroll.payments.index') }}" :active="request()->routeIs('payroll.payments.index')" :isChild="true">Proses Pembayaran</x-dashboard.nav-item>
    @endcan
    @canany(['payroll.period.view', 'payroll.period.generate'])
    <x-dashboard.nav-item href="{{ route('payroll.recap') }}" :active="request()->routeIs('payroll.recap')" :isChild="true">Rekap Payroll</x-dashboard.nav-item>
    @endcanany
</x-dashboard.nav-group>
@endif

<!-- 1.6 KPI -->
@canany(['kpi.template.create', 'kpi.evaluation.create', 'kpi.grade_rule.view'])
<x-dashboard.nav-group title="KPI" icon="target" :active="request()->is('kpi*')">
    @can('kpi.template.create')
    <x-dashboard.nav-item href="{{ route('kpi.templates.index') }}" :active="request()->routeIs('kpi.templates.*')" :isChild="true">Template KPI</x-dashboard.nav-item>
    @endcan
    @can('kpi.grade_rule.view')
    <x-dashboard.nav-item href="{{ route('kpi.grade-rules.index') }}" :active="request()->routeIs('kpi.grade-rules.*')" :isChild="true">Grade KPI</x-dashboard.nav-item>
    @endcan
    @can('kpi.evaluation.create')
    <x-dashboard.nav-item href="{{ route('kpi.evaluator-assignments.index') }}" :active="request()->routeIs('kpi.evaluator-assignments.*')" :isChild="true">Assignment Evaluator</x-dashboard.nav-item>
    <x-dashboard.nav-item href="{{ route('kpi.evaluations.index') }}" :active="request()->routeIs('kpi.evaluations.*')" :isChild="true">Evaluasi KPI</x-dashboard.nav-item>
    @endcan
</x-dashboard.nav-group>
@endcanany

<!-- 1.7 Attendance & Employee -->
@canany(['employee.create', 'employee.update'])
<x-dashboard.nav-group title="Kelola Karyawan" icon="badge" :active="request()->is('employee/manage*') || request()->is('employee/contract*') || request()->is('employee/education*') || request()->is('employee/offboarding*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Data Pegawai</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Kontrak Baru</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Perpanjang Kontrak</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Ubah Posisi / Cabang</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Resign / Offboarding</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan

<!-- 1.8 Curriculum -->
@canany(['curriculum.program.create', 'curriculum.curriculum.create', 'curriculum.view'])
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Akademik</p>
</div>
<x-dashboard.nav-group title="Program & Kurikulum" icon="menu_book" :active="request()->is('curriculum*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Program & Kuota</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Kelola Kurikulum</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan

<!-- 1.9 Class -->
@canany(['class.attendance.record', 'class.test.create', 'class.graduate'])
<x-dashboard.nav-group title="Kelas" icon="school" :active="request()->is('class*') && !request()->is('class/my-classes*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Buat Kelas Baru</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Daftarkan Member</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Sesi & Absensi</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Test & Sertifikat</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan

<!-- 1.10 Member & Marketing -->
@if(auth()->user()->can('marketing.socialization.create') || auth()->user()->can('marketing.prospective_member.create'))
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Sales & Service</p>
</div>
<x-dashboard.nav-group title="Member & Marketing" icon="campaign" :active="request()->is('member*') || request()->is('marketing*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Registrasi Member</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Profil Member</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Sosialisasi & Lead</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Template WA</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Target & Performance</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Financial Statement</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endif

<!-- 1.11 Recruitment -->
@canany(['recruitment.job_permintaan.create', 'recruitment.job_posting.create'])
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Rekrutmen</p>
</div>
<x-dashboard.nav-group title="Rekrutmen" icon="work" :active="request()->is('recruitment*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Job Permintaan</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Job Posting</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Aplikasi Lowongan</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Psikotest & Interview</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Offering & Onboarding</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan

<!-- 1.12 Survey -->
@canany(['survey.form.view', 'survey.form.create'])
<div class="pt-4 pb-2">
    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider font-jakarta">Lainnya</p>
</div>
<x-dashboard.nav-group title="Survey" icon="poll" :active="request()->is('survey*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Template Survey</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Assign Survey</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Hasil Survey</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan

<!-- 1.13 TOEFL -->
@canany(['toefl.test.create', 'toefl.test.update'])
<x-dashboard.nav-group title="TOEFL" icon="language" :active="request()->is('toefl*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Bank Test</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Session Member</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Lead Guest</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan

<!-- 1.14 Notification -->
@canany(['notification.template.create', 'notification.send_manual'])
<x-dashboard.nav-group title="Notifikasi" icon="notifications_active" :active="request()->is('notification*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Konfigurasi Routing</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Template Notifikasi</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Manual Send</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan

<!-- 1.15 Finance -->
@canany(['finance.budget_estimate.view', 'finance.reimbursement.view', 'finance.monthly_cost.view'])
<x-dashboard.nav-group title="Keuangan" icon="account_balance_wallet" :active="request()->is('finance*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Budget Estimate</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Reimbursement</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Biaya Operasional</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Lock Periode</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan

<!-- 1.16 Letter -->
@canany(['letter.template.create', 'letter.in.create', 'letter.generate.create'])
<x-dashboard.nav-group title="Surat" icon="drafts" :active="request()->is('letter*') && !request()->is('letter/my-letters*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Template Surat</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Surat Keluar</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Surat Masuk</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Dokumen SOP</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan

<!-- 1.17 Facility -->
@canany(['facility.ticket.create', 'facility.inventory.create', 'facility.rent_contract.create'])
<x-dashboard.nav-group title="Fasilitas" icon="build" :active="request()->is('facility*') && !request()->is('facility/my-tickets*')">
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Tiket Fasilitas</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Inventaris</x-dashboard.nav-item>
    <x-dashboard.nav-item href="javascript:void(0)" comingSoon="true" :isChild="true">Kontrak Sewa</x-dashboard.nav-item>
</x-dashboard.nav-group>
@endcan
