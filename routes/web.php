<?php

use App\Http\Controllers\Admin\AttendanceLogController;
use App\Http\Controllers\Admin\AttendanceRecapController;
use App\Http\Controllers\Admin\BonusRuleController;
use App\Http\Controllers\Admin\BranchTransferController;
use App\Http\Controllers\Admin\BudgetEstimateController;
use App\Http\Controllers\Admin\ClassRoomController;
use App\Http\Controllers\Admin\CurriculumController;
use App\Http\Controllers\Admin\EmployeeKpiEvaluationController;
use App\Http\Controllers\Admin\EmploymentStatusController;
use App\Http\Controllers\Admin\FacilityTicketController;
use App\Http\Controllers\Admin\JobRequisitionController;
use App\Http\Controllers\Admin\KpiTemplateController;
use App\Http\Controllers\Admin\LeaveRequestController;
use App\Http\Controllers\Admin\LetterTemplateController;
use App\Http\Controllers\Admin\MemberClassController;
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
use App\Http\Controllers\Auth\ApplicantForgotPasswordController;
use App\Http\Controllers\Auth\ApplicantLoginController;
use App\Http\Controllers\Auth\ApplicantPasswordController;
use App\Http\Controllers\Auth\ApplicantRegisterController;
use App\Http\Controllers\Auth\ApplicantResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\MemberForgotPasswordController;
use App\Http\Controllers\Auth\MemberLoginController;
use App\Http\Controllers\Auth\MemberPasswordController;
use App\Http\Controllers\Auth\MemberRegisterController;
use App\Http\Controllers\Auth\MemberResetPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BranchProgramQuotaController;
use App\Http\Controllers\ClassCurriculumController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EmployeeEducationHistoryController;
use App\Http\Controllers\FileProxyController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\OffBoardingController;
use App\Http\Controllers\PdfTestController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ResignRequestController;
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

// Auth Routes — Applicant Register (public)
Route::get('/karir/register', [ApplicantRegisterController::class, 'showRegister'])->name('applicant.register');
Route::post('/karir/register', [ApplicantRegisterController::class, 'register'])->name('applicant.register.post');

// Member authenticated routes
Route::middleware(['auth:member', EnsureEmailIsVerified::class])->group(function () {
    Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])->name('member.dashboard');
    Route::get('/member/change-password', [MemberPasswordController::class, 'showChangeForm'])->name('member.password.change');
    Route::post('/member/change-password', [MemberPasswordController::class, 'update'])->name('member.password.change.post');
});

// Auth Routes — Applicant (applicant guard)
Route::get('/karir/login', [ApplicantLoginController::class, 'showLogin'])->name('applicant.login');
Route::post('/karir/login', [ApplicantLoginController::class, 'login'])->name('applicant.login.post');
Route::post('/karir/logout', [ApplicantLoginController::class, 'logout'])->name('applicant.logout');

// Applicant authenticated routes
Route::middleware(['auth:applicant', EnsureEmailIsVerified::class])->group(function () {
    Route::get('/karir/dashboard', [ApplicantDashboardController::class, 'index'])->name('applicant.dashboard');
    Route::get('/karir/change-password', [ApplicantPasswordController::class, 'showChangeForm'])->name('applicant.password.change');
    Route::post('/karir/change-password', [ApplicantPasswordController::class, 'update'])->name('applicant.password.change.post');
});

// Auth Routes — Password Management (forgot + reset) — Employee (web guard)
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Auth Routes — Password Management (forgot + reset) — Member
Route::get('/member/forgot-password', [MemberForgotPasswordController::class, 'showForm'])->name('member.password.request');
Route::post('/member/forgot-password', [MemberForgotPasswordController::class, 'sendResetLink'])->name('member.password.email');
Route::get('/member/reset-password/{token}', [MemberResetPasswordController::class, 'showForm'])->name('member.password.reset');
Route::post('/member/reset-password', [MemberResetPasswordController::class, 'reset'])->name('member.password.update');

// Auth Routes — Password Management (forgot + reset) — Applicant
Route::get('/karir/forgot-password', [ApplicantForgotPasswordController::class, 'showForm'])->name('applicant.password.request');
Route::post('/karir/forgot-password', [ApplicantForgotPasswordController::class, 'sendResetLink'])->name('applicant.password.email');
Route::get('/karir/reset-password/{token}', [ApplicantResetPasswordController::class, 'showForm'])->name('applicant.password.reset');
Route::post('/karir/reset-password', [ApplicantResetPasswordController::class, 'reset'])->name('applicant.password.update');

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
    Route::middleware('board-of-directors')->group(function () {
        Route::get('users/{user}/roles', [RolePermissionController::class, 'showUserRoles'])->name('users.roles');
        Route::post('users/{user}/assign-role', [RolePermissionController::class, 'assignRole'])->name('users.assign-role');
        Route::post('users/{user}/assign-permission', [RolePermissionController::class, 'assignPermission'])->name('users.assign-permission');
        Route::get('users/{user}/reset-password', [UserManagementController::class, 'showResetForm'])->name('users.reset-password.show');
        Route::post('users/{user}/reset-password', [UserManagementController::class, 'reset'])->name('users.reset-password.update');
    });

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
    Route::get('employment-statuses/{status}/extend', [EmploymentStatusController::class, 'extendCreate'])->name('employment-statuses.extend.create');
    Route::post('employment-statuses/{status}/extend', [EmploymentStatusController::class, 'extendStore'])->name('employment-statuses.extend.store');
    Route::get('employment-statuses/{status}/change-position', [EmploymentStatusController::class, 'changePositionCreate'])->name('employment-statuses.change-position.create');
    Route::post('employment-statuses/{status}/change-position', [EmploymentStatusController::class, 'changePositionStore'])->name('employment-statuses.change-position.store');

    // Employee Education History (inline in profile)
    Route::post('employees/{employee}/educations', [EmployeeEducationHistoryController::class, 'store'])->name('employees.educations.store');
    Route::patch('employees/{employee}/educations/{education}', [EmployeeEducationHistoryController::class, 'update'])->name('employees.educations.update');
    Route::delete('employees/{employee}/educations/{education}', [EmployeeEducationHistoryController::class, 'destroy'])->name('employees.educations.destroy');

    // Employee Resign Requests (self-service initiate + BOARD approval)
    Route::get('employees/{employee}/resign-requests/create', [ResignRequestController::class, 'create'])->name('resign-requests.create');
    Route::post('employees/{employee}/resign-requests', [ResignRequestController::class, 'store'])->name('resign-requests.store');
    Route::get('resign-requests', [ResignRequestController::class, 'index'])->middleware('board-of-directors')->name('resign-requests.index');
    Route::get('resign-requests/{resignRequest}', [ResignRequestController::class, 'show'])->name('resign-requests.show');
    Route::post('resign-requests/{resignRequest}/approve', [ResignRequestController::class, 'approve'])->name('resign-requests.approve');
    Route::post('resign-requests/{resignRequest}/reject', [ResignRequestController::class, 'reject'])->name('resign-requests.reject');

    // Employee Offboarding (contract termination)
    Route::get('employees/{employee}/off-boarding/create', [OffBoardingController::class, 'create'])->name('off-boarding.create');
    Route::post('employees/{employee}/off-boarding', [OffBoardingController::class, 'store'])->name('off-boarding.store');

    // Employee Branch Transfer (2 flows: self-service + direct)
    Route::get('employees/{employee}/branch-transfer/request', [BranchTransferController::class, 'requestCreate'])->name('branch-transfers.request.create');
    Route::post('employees/{employee}/branch-transfer/request', [BranchTransferController::class, 'requestStore'])->name('branch-transfers.request.store');
    Route::get('branch-transfers/review', [BranchTransferController::class, 'reviewIndex'])->name('branch-transfers.review.index');
    Route::get('branch-transfers/{transfer}', [BranchTransferController::class, 'reviewShow'])->name('branch-transfers.show');
    Route::put('branch-transfers/{transfer}', [BranchTransferController::class, 'reviewUpdate'])->name('branch-transfers.update');
    Route::get('employees/{employee}/branch-transfer/direct', [BranchTransferController::class, 'directEdit'])->name('branch-transfers.direct.edit');
    Route::put('employees/{employee}/branch-transfer/direct', [BranchTransferController::class, 'directUpdate'])->name('branch-transfers.direct.update');

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
    Route::resource('programs', ProgramController::class);
    Route::resource('programs.quotas', BranchProgramQuotaController::class)->shallow();
    Route::resource('curriculums', CurriculumController::class);
    Route::post('curriculums/{curriculum}/sessions', [CurriculumController::class, 'storeSession'])->name('curriculums.sessions.store');
    Route::put('curriculum-sessions/{session}', [CurriculumController::class, 'updateSession'])->name('curriculum-sessions.update');
    Route::delete('curriculum-sessions/{session}', [CurriculumController::class, 'destroySession'])->name('curriculum-sessions.destroy');
    Route::post('curriculum-sessions/{session}/items', [CurriculumController::class, 'storeItem'])->name('curriculum-sessions.items.store');
    Route::put('curriculum-items/{item}', [CurriculumController::class, 'updateItem'])->name('curriculum-items.update');
    Route::delete('curriculum-items/{item}', [CurriculumController::class, 'destroyItem'])->name('curriculum-items.destroy');

    // Class Domain
    Route::resource('classrooms', ClassRoomController::class);
    Route::post('classrooms/{classroom}/members', [MemberClassController::class, 'store'])->name('member-class.store');
    Route::delete('member-class/{memberClass}', [MemberClassController::class, 'destroy'])->name('member-class.destroy');
    Route::put('member-class/{memberClass}/transfer', [MemberClassController::class, 'transfer'])->name('member-class.transfer');

    // Curriculum Scoped Access (by classroom)
    Route::get('classrooms/{classroom}/curriculum', [ClassCurriculumController::class, 'index'])->name('class-curriculum.index');
    Route::get('classrooms/{classroom}/curriculum/items/{item}', [ClassCurriculumController::class, 'show'])->name('class-curriculum.show');
    Route::get('classrooms/{classroom}/curriculum/items/{item}/download', [ClassCurriculumController::class, 'downloadFile'])->name('class-curriculum.download');

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
