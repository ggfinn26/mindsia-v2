<?php

use App\Http\Controllers\Admin\AttendanceLogController;
use App\Http\Controllers\Admin\AttendanceRecapController;
use App\Http\Controllers\Admin\BonusRuleController;
use App\Http\Controllers\Admin\BudgetEstimateController;
use App\Http\Controllers\Admin\ClassRoomController;
use App\Http\Controllers\Admin\CurriculumController;
use App\Http\Controllers\Admin\EmployeeKpiEvaluationController;
use App\Http\Controllers\Admin\FacilityTicketController;
use App\Http\Controllers\Admin\JobRequisitionController;
use App\Http\Controllers\Admin\KpiTemplateController;
use App\Http\Controllers\Admin\LeaveRequestController;
use App\Http\Controllers\Admin\LetterTemplateController;
use App\Http\Controllers\Admin\MemberDataController;
use App\Http\Controllers\Admin\NotificationTemplateController;
use App\Http\Controllers\Admin\PayrollPeriodController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SocializationController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\ToeflTestController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\WorkScheduleController;
use App\Http\Controllers\Applicant\ApplicantDashboardController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\Auth\ApplicantLoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MemberLoginController;
use App\Http\Controllers\Auth\MemberRegisterController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FileProxyController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\PdfTestController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RegionController;
use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');
Route::get('/file/{fileId}', [FileProxyController::class, 'serve'])->name('file.serve');
Route::get('/pdf-test', [PdfTestController::class, 'test'])->name('pdf.test');
Route::get('/pdf-test/kop', [PdfTestController::class, 'testKopSurat'])->name('pdf.test.kop');
Route::get('/documents/surat-keterangan', [DocumentController::class, 'suratKeterangan'])->name('document.surat-keterangan');

// Auth Routes — Employee (web guard)
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Auth Routes — Register Employee (2 steps)
Route::get('/daftar/step1', [RegisterController::class, 'showStep1'])->name('register.step1');
Route::post('/daftar/verify', [RegisterController::class, 'verifyCode'])->name('register.verify');
Route::get('/daftar/step2', [RegisterController::class, 'showStep2'])->name('register.step2');
Route::post('/daftar', [RegisterController::class, 'register'])->name('register');

// Auth Routes — Member (member guard)
Route::get('/member/login', [MemberLoginController::class, 'showLogin'])->name('member.login');
Route::post('/member/login', [MemberLoginController::class, 'login'])->name('member.login.post');
Route::post('/member/logout', [MemberLoginController::class, 'logout'])->name('member.logout');

// Auth Routes — Member Register (public)
Route::get('/daftar-member', [MemberRegisterController::class, 'showRegister'])->name('member.register');
Route::post('/daftar-member', [MemberRegisterController::class, 'register'])->name('member.register.post');

// Member authenticated routes
Route::middleware(['auth:member', EnsureEmailIsVerified::class])->group(function () {
    Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])->name('member.dashboard');
});

// Auth Routes — Applicant (applicant guard)
Route::get('/karir/login', [ApplicantLoginController::class, 'showLogin'])->name('applicant.login');
Route::post('/karir/login', [ApplicantLoginController::class, 'login'])->name('applicant.login.post');
Route::post('/karir/logout', [ApplicantLoginController::class, 'logout'])->name('applicant.logout');

// Applicant authenticated routes
Route::middleware(['auth:applicant', EnsureEmailIsVerified::class])->group(function () {
    Route::get('/karir/dashboard', [ApplicantDashboardController::class, 'index'])->name('applicant.dashboard');
});

// Auth Routes — Password Management (forgot + reset)
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Register success notification page
Route::get('/daftar/sukses', function () {
    return view('auth.register-success');
})->name('register.success');

// Email Verification Routes
Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'send'])
    ->middleware('throttle:6,1')
    ->name('verification.send');

// Authenticated routes
Route::middleware(['auth:web', EnsureEmailIsVerified::class])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Password change (authenticated users)
    Route::get('/change-password', [PasswordController::class, 'showChangeForm'])->name('password.change');
    Route::post('/change-password', [PasswordController::class, 'update'])->name('password.change.post');
    Route::resource('provinces', ProvinceController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('regions', RegionController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('areas', AreaController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('branches', BranchController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::patch('branches/{branch}/toggle-active', [BranchController::class, 'toggleActive'])->name('branches.toggle-active');
    Route::resource('institutions', InstitutionController::class)->only(['index', 'store', 'update', 'destroy']);

    // Admin Routes — User Management (Auth Flow 8: employee-admin-auth)
    Route::resource('users', UserManagementController::class)->only(['index', 'show', 'edit', 'update']);
    Route::get('users/{user}/roles', [RolePermissionController::class, 'showUserRoles'])->name('users.roles');
    Route::post('users/{user}/assign-role', [RolePermissionController::class, 'assignRole'])->name('users.assign-role');
    Route::post('users/{user}/assign-permission', [RolePermissionController::class, 'assignPermission'])->name('users.assign-permission');

    // Admin Routes — Role Management (Spatie Permission CRUD)
    Route::get('roles', [RoleController::class, 'index'])->middleware('can:view roles')->name('roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])->middleware('can:create roles')->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->middleware('can:create roles')->name('roles.store');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->middleware('can:update roles')->name('roles.edit');
    Route::patch('roles/{role}', [RoleController::class, 'update'])->middleware('can:update roles')->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware('can:delete roles')->name('roles.destroy');

    // Admin Routes — Position Management (Employee Flow 1: position-management)
    Route::resource('positions', PositionController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Employee Management (Employee Flow 2)
    Route::resource('employees', EmployeeController::class)->except(['destroy']);
    Route::resource('employment-statuses', EmploymentStatusController::class)->except(['show', 'index', 'destroy']);

    // Attendance Domain
    Route::resource('work-schedules', WorkScheduleController::class);
    Route::resource('attendance-logs', AttendanceLogController::class);
    Route::resource('leave-requests', LeaveRequestController::class);
    Route::resource('attendance-recaps', AttendanceRecapController::class)->only(['index', 'show']);

    // KPI Domain
    Route::resource('kpi-templates', KpiTemplateController::class);
    Route::resource('kpi-evaluations', EmployeeKpiEvaluationController::class);

    // Marketing Domain
    Route::resource('socializations', SocializationController::class);

    // Bonus Domain
    Route::resource('bonus-rules', BonusRuleController::class);

    // Payroll Domain
    Route::resource('payroll-periods', PayrollPeriodController::class);

    // Member Domain
    Route::resource('members', MemberDataController::class);

    // Curriculum Domain
    Route::resource('curriculums', CurriculumController::class);

    // Class Domain
    Route::resource('classrooms', ClassRoomController::class);

    // Finance Domain
    Route::resource('budget-estimates', BudgetEstimateController::class);

    // Facility Domain
    Route::resource('facility-tickets', FacilityTicketController::class);

    // Letter Domain
    Route::resource('letter-templates', LetterTemplateController::class);

    // Recruitment Domain
    Route::resource('job-requisitions', JobRequisitionController::class);

    // Notification Domain
    Route::resource('notification-templates', NotificationTemplateController::class);

    // Survey Domain
    Route::resource('surveys', SurveyController::class);

    // TOEFL Domain
    Route::resource('toefl-tests', ToeflTestController::class);
});

require __DIR__.'/webhook.php';
