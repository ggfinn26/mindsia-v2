<?php

// use App\Http\Controllers\Admin\AttendanceLogController;
use App\Http\Controllers\Admin\AttendancePolicyController;
use App\Http\Controllers\Admin\AttendanceRecapController;
use App\Http\Controllers\Admin\AttendanceRuleController;
use App\Http\Controllers\Admin\Bonus\BonusRuleChangeHistoryController;
use App\Http\Controllers\Admin\Bonus\KpiBonusRuleController;
use App\Http\Controllers\Admin\Bonus\MarketingBonusRuleController;
use App\Http\Controllers\Admin\Bonus\SpecialBonusRuleController;
use App\Http\Controllers\Admin\BranchTransferController;
use App\Http\Controllers\Admin\ClassRoomController;
use App\Http\Controllers\Admin\ClassScheduleController;
use App\Http\Controllers\Admin\ClassTestController;
use App\Http\Controllers\Admin\ContractTerminateController;
use App\Http\Controllers\Admin\CurriculumController;
use App\Http\Controllers\Admin\DocumentSignatureSettingController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EmployeeWaTemplateController;
use App\Http\Controllers\Admin\EmploymentStatusController;
use App\Http\Controllers\Admin\Facility\BranchRentContractController;
use App\Http\Controllers\Admin\Facility\BranchRentTerminController;
use App\Http\Controllers\Admin\Facility\FacilityTicketAttachmentController;
use App\Http\Controllers\Admin\Facility\FacilityTicketController;
use App\Http\Controllers\Admin\Facility\InventoryItemController;
use App\Http\Controllers\Admin\Finance\BranchMonthlyCostController;
use App\Http\Controllers\Admin\Finance\BranchPeriodLockController;
use App\Http\Controllers\Admin\Finance\BudgetEstimateController;
use App\Http\Controllers\Admin\Finance\ReimbursementController;
use App\Http\Controllers\Admin\GuestToeflLeadController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\Kpi\KpiDocumentController;
use App\Http\Controllers\Admin\Kpi\KpiEvaluationController;
use App\Http\Controllers\Admin\Kpi\KpiEvaluationItemController;
use App\Http\Controllers\Admin\Kpi\KpiEvaluatorAssignmentController;
use App\Http\Controllers\Admin\Kpi\KpiGradeRuleController;
use App\Http\Controllers\Admin\Kpi\KpiTemplateController;
use App\Http\Controllers\Admin\Kpi\KpiTemplateIndicatorController;
use App\Http\Controllers\Admin\Landing\LandingLeaderController;
use App\Http\Controllers\Admin\Landing\LandingPhotoController;
use App\Http\Controllers\Admin\Landing\LandingTestimonialController;
use App\Http\Controllers\Admin\Letter\InLetterController;
use App\Http\Controllers\Admin\Letter\LetterTemplateController;
use App\Http\Controllers\Admin\Letter\OutLetterViaGenerateController;
use App\Http\Controllers\Admin\Letter\OutLetterViaUploadController;
use App\Http\Controllers\Admin\Letter\SopDocumentController;
use App\Http\Controllers\Admin\MarketingKpiController;
use App\Http\Controllers\Admin\MarketingMyPerformanceController;
use App\Http\Controllers\Admin\MarketingTargetController;
use App\Http\Controllers\Admin\Member\DiscountController;
use App\Http\Controllers\Admin\Member\MemberDataController;
use App\Http\Controllers\Admin\Member\MemberNpsResponseController;
use App\Http\Controllers\Admin\Member\MemberPaymentController;
use App\Http\Controllers\Admin\Member\MemberRegistrationController;
use App\Http\Controllers\Admin\Member\MemberSupportTicketController;
use App\Http\Controllers\Admin\Member\MemberSupportTicketReplyController;
use App\Http\Controllers\Admin\Member\SupportTicketAssignmentRuleController;
use App\Http\Controllers\Admin\MemberAttendanceController;
use App\Http\Controllers\Admin\MemberCertificateController;
use App\Http\Controllers\Admin\MemberClassController;
use App\Http\Controllers\Admin\MemberCurriculumProgressController;
use App\Http\Controllers\Admin\MemberPaymentStatementController;
use App\Http\Controllers\Admin\MemberSessionAssessmentController;
use App\Http\Controllers\Admin\MemberTestResultController;
use App\Http\Controllers\Admin\MonthlyRevenueDataController;
use App\Http\Controllers\Admin\NotificationLogController;
use App\Http\Controllers\Admin\NotificationManualSendController;
use App\Http\Controllers\Admin\NotificationTemplateController;
use App\Http\Controllers\Admin\Payroll\EmployeeCompensationController;
use App\Http\Controllers\Admin\Payroll\EmployeePayrollController;
use App\Http\Controllers\Admin\Payroll\PayrollComponentController;
use App\Http\Controllers\Admin\Payroll\PayrollPaymentController;
use App\Http\Controllers\Admin\Payroll\PayrollPeriodController;
use App\Http\Controllers\Admin\Payroll\PayrollSlipController;
use App\Http\Controllers\Admin\Payroll\SessionCompensationRuleController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ProspectiveMemberController;
use App\Http\Controllers\Admin\Recruitment\ApplicantInterviewEvaluationController;
use App\Http\Controllers\Admin\Recruitment\ApplicantInterviewScheduleController;
use App\Http\Controllers\Admin\Recruitment\ApplicantPsikotestController;
use App\Http\Controllers\Admin\Recruitment\EmployeeOnboardingController;
use App\Http\Controllers\Admin\Recruitment\JobApplicationController;
use App\Http\Controllers\Admin\Recruitment\JobPermintaanController;
use App\Http\Controllers\Admin\Recruitment\JobPostingController;
use App\Http\Controllers\Admin\Recruitment\OfferingLetterController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SocializationController;
use App\Http\Controllers\Admin\SurveyAssignmentController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\Admin\SurveyResultController;
use App\Http\Controllers\Admin\SystemAccess\ApiKeyController;
use App\Http\Controllers\Admin\SystemAccess\BotManagementController;
use App\Http\Controllers\Admin\SystemAccess\DashboardWidgetConfigController;
use App\Http\Controllers\Admin\SystemAccess\ForceResetPasswordController;
use App\Http\Controllers\Admin\SystemAccess\NotificationRoutingController;
use App\Http\Controllers\Admin\SystemAccess\WebhookStatusController;
use App\Http\Controllers\Admin\TestQuestionController;
use App\Http\Controllers\Admin\ToeflMediaController;
use App\Http\Controllers\Admin\ToeflPassageController;
use App\Http\Controllers\Admin\ToeflQuestionController;
use App\Http\Controllers\Admin\ToeflTestController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\WorkScheduleAssignmentController;
use App\Http\Controllers\Admin\WorkScheduleRuleController;
use App\Http\Controllers\Applicant\ApplicantDashboardController;
use App\Http\Controllers\Applicant\ApplicantProfileController;
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
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ClassCurriculumController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Employee\EmployeeProfileController;
use App\Http\Controllers\Employee\EmployeeSurveyController;
use App\Http\Controllers\Employee\PayslipController;
use App\Http\Controllers\EmployeeEducationHistoryController;
use App\Http\Controllers\EmployeeNotificationController;
use App\Http\Controllers\FileProxyController;
use App\Http\Controllers\GuestToeflSessionController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\Landing\CompanyController;
use App\Http\Controllers\Landing\HomeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\Member\MemberDashboardController;
use App\Http\Controllers\Member\MemberNotificationController;
use App\Http\Controllers\Member\MemberProfileController;
use App\Http\Controllers\Member\MemberSurveyController;
use App\Http\Controllers\Member\ToeflSessionController;
use App\Http\Controllers\OffBoardingController;
use App\Http\Controllers\PdfTestController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\Public\OnlineTestController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ResignRequestController;
use App\Http\Controllers\SessionAttendanceController;
use App\Http\Controllers\WorkAttendanceController;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/company', CompanyController::class)->name('company.landing');

Route::get('/api/branches-map', fn () => response()->json(
    Branch::select('provinces.name as province_name')
        ->selectRaw('COUNT(branches.id) as total')
        ->selectRaw('GROUP_CONCAT(branches.branch_name ORDER BY branches.branch_name SEPARATOR ",") as cities')
        ->join('areas', 'branches.areas_id', '=', 'areas.id')
        ->join('regions', 'areas.region_id', '=', 'regions.id')
        ->join('provinces', 'regions.province_id', '=', 'provinces.id')
        ->where('branches.is_active', true)
        ->groupBy('provinces.name')
        ->get()
        ->map(fn ($b) => [
            'province' => $b->province_name,
            'total' => $b->total,
            'cities' => explode(',', $b->cities),
        ])
));

Route::get('/', HomeController::class)->name('home');

Route::get('/karir', function () {
    $openings = collect([
        (object) [
            'id' => 1,
            'position' => 'English Tutor (Full-Time)',
            'branch' => (object) ['name' => 'MINDSIA Jakarta'],
            'education_requirement' => 'S1 Sastra Inggris / Pendidikan Bahasa Inggris',
            'experience_requirement' => 'Min 1 Tahun',
            'job_description' => 'Mengajar dan membimbing siswa di asrama 24 HESA.',
        ],
    ]);

    return view('careers.index', compact('openings'));
})->name('careers.index');

Route::get('/karir/{req}', function ($req) {
    // Dummy route for career details
    $job = (object) [
        'id' => $req,
        'position' => 'English Tutor (Full-Time)',
        'branch' => (object) ['name' => 'MINDSIA Jakarta'],
        'education_requirement' => 'S1 Sastra Inggris / Pendidikan Bahasa Inggris',
        'experience_requirement' => 'Min 1 Tahun',
        'job_description' => 'Kami mencari individu yang berdedikasi tinggi untuk bergabung sebagai English Tutor di asrama MINDSIA. Anda akan bertanggung jawab untuk memastikan setiap siswa tidak hanya mengerti tata bahasa, tetapi juga mampu mengaplikasikannya secara aktif dalam ekosistem asrama 24 jam.',
    ];

    return view('careers.show', compact('job'));
})->name('careers.show');

Route::get('/employee', function () {
    return view('employee');
})->name('employee.landing');

Route::get('/daftar', function (Request $request) {
    $branches = collect([
        (object) ['id' => 1, 'name' => 'MINDSIA Jakarta'],
    ]);
    $programs = collect([
        (object) ['id' => 1, 'code' => 'TOEFL PREP', 'promo_price' => 500000],
    ]);

    return view('members.create', [
        'branches' => $branches,
        'programs' => $programs,
        'referralCode' => '',
        'referralMo' => null,
    ]);
})->name('public.register');

Route::post('/daftar', function () {
    return 'Success';
})->name('public.store');

Route::get('/file/{fileId}', [FileProxyController::class, 'serve'])->name('file.serve');
Route::get('/pdf-test', [PdfTestController::class, 'test'])->name('pdf.test');
Route::get('/pdf-test/kop', [PdfTestController::class, 'testKopSurat'])->name('pdf.test.kop');

// Career routes (public — no auth)
// Route::prefix('karir')->name('career.')->group(function () {
//     Route::get('/', [CareerController::class, 'index'])->name('index');
//     Route::get('{jobPosting}', [CareerController::class, 'show'])->name('show');
// });

// TOEFL Guest Trial (public — no auth)
Route::prefix('toefl-trial')->name('toefl.guest.')->group(function () {
    Route::get('{toeflTest}', [GuestToeflSessionController::class, 'showEntry'])->name('entry');
    Route::post('{toeflTest}/start', [GuestToeflSessionController::class, 'start'])->name('start');
    Route::get('session/{session}/listening', [GuestToeflSessionController::class, 'showListening'])->name('listening');
    Route::get('session/{session}/structure', [GuestToeflSessionController::class, 'showStructure'])->name('structure');
    Route::get('session/{session}/reading', [GuestToeflSessionController::class, 'showReading'])->name('reading');
    Route::post('session/{session}/answer', [GuestToeflSessionController::class, 'saveAnswer'])->name('answer');
    Route::post('session/{session}/submit-section', [GuestToeflSessionController::class, 'submitSection'])->name('submit-section');
    Route::get('session/{session}/result', [GuestToeflSessionController::class, 'result'])->name('result');
});

// Auth Routes — Employee (web guard)
Route::get('/employee/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/employee/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/employee/logout', [LoginController::class, 'logout'])->name('logout');

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
Route::get('/member/register', [MemberRegisterController::class, 'showRegister'])->name('member.register');
Route::post('/member/register', [MemberRegisterController::class, 'register'])->name('member.register.post');

// Auth Routes — Applicant Register (public)
Route::get('/applicant/register', [ApplicantRegisterController::class, 'showRegister'])->name('applicant.register');
Route::post('/applicant/register', [ApplicantRegisterController::class, 'register'])->name('applicant.register.post');

// Member authenticated routes
Route::middleware(['auth:member', EnsureEmailIsVerified::class])->group(function () {
    Route::get('/member/dashboard', [MemberDashboardController::class, 'index'])->name('member.dashboard');
    Route::get('/member/profile', [MemberProfileController::class, 'show'])->name('member.profile.show');
    Route::put('/member/profile', [MemberProfileController::class, 'update'])->name('member.profile.update');
    Route::get('/member/change-password', [MemberPasswordController::class, 'showChangeForm'])->name('member.password.change');
    Route::post('/member/change-password', [MemberPasswordController::class, 'update'])->name('member.password.change.post');

    // Notifications
    Route::get('/member/notifications', [MemberNotificationController::class, 'index'])->name('member.notifications.index');
    Route::get('/member/notifications/unread-count', [MemberNotificationController::class, 'unreadCount'])->name('member.notifications.unread-count');

    // Survey
    Route::get('/member/surveys', [MemberSurveyController::class, 'index'])->name('member.surveys.index');

    // Online Test
    Route::get('/member/tests/{test}', [OnlineTestController::class, 'show'])->name('member.tests.show');
    Route::post('/member/tests/{test}/submit', [OnlineTestController::class, 'submit'])->name('member.tests.submit');
    Route::get('/member/surveys/{memberSurvey}', [MemberSurveyController::class, 'show'])->name('member.surveys.show');
    Route::post('/member/surveys/{memberSurvey}/submit', [MemberSurveyController::class, 'store'])->name('member.surveys.submit');

    // NPS & Review (member submit setelah lulus)
    Route::post('/member/nps-responses', [MemberNpsResponseController::class, 'store'])->name('member.nps-responses.store');
    Route::put('/member/reviews/{review}', [MemberNpsResponseController::class, 'updateReview'])->name('member.reviews.update');

    // TOEFL
    Route::get('/toefl', [ToeflSessionController::class, 'index'])->name('toefl.session.index');
    Route::post('/toefl/{toeflTest}/start', [ToeflSessionController::class, 'start'])->name('toefl.session.start');
    Route::get('/toefl/session/{session}/listening', [ToeflSessionController::class, 'showListening'])->name('toefl.session.listening');
    Route::get('/toefl/session/{session}/structure', [ToeflSessionController::class, 'showStructure'])->name('toefl.session.structure');
    Route::get('/toefl/session/{session}/reading', [ToeflSessionController::class, 'showReading'])->name('toefl.session.reading');
    Route::post('/toefl/session/{session}/answer', [ToeflSessionController::class, 'saveAnswer'])->name('toefl.session.answer');
    Route::post('/toefl/session/{session}/submit-section', [ToeflSessionController::class, 'submitSection'])->name('toefl.session.submit-section');
    Route::get('/toefl/session/{session}/result', [ToeflSessionController::class, 'result'])->name('toefl.session.result');
});

// Auth Routes — Applicant (applicant guard)
Route::get('/applicant/login', [ApplicantLoginController::class, 'showLogin'])->name('applicant.login');
Route::post('/applicant/login', [ApplicantLoginController::class, 'login'])->name('applicant.login.post');
Route::post('/applicant/logout', [ApplicantLoginController::class, 'logout'])->name('applicant.logout');

// Applicant authenticated routes
Route::middleware(['auth:applicant', EnsureEmailIsVerified::class])->group(function () {
    Route::get('/applicant/dashboard', [ApplicantDashboardController::class, 'index'])->name('applicant.dashboard');
    Route::get('/applicant/change-password', [ApplicantPasswordController::class, 'showChangeForm'])->name('applicant.password.change');
    Route::post('/applicant/change-password', [ApplicantPasswordController::class, 'update'])->name('applicant.password.change.post');

    // Applicant profile management
    Route::get('/applicant/profil', [ApplicantProfileController::class, 'show'])->name('applicant.profile.show');
    Route::get('/applicant/profil/edit', [ApplicantProfileController::class, 'edit'])->name('applicant.profile.edit');
    Route::patch('/applicant/profil', [ApplicantProfileController::class, 'update'])->name('applicant.profile.update');
    Route::post('/applicant/profil/education', [ApplicantProfileController::class, 'storeEducation'])->name('applicant.profile.education.store');
    Route::put('/applicant/profil/education/{education}', [ApplicantProfileController::class, 'updateEducation'])->name('applicant.profile.education.update');
    Route::delete('/applicant/profil/education/{education}', [ApplicantProfileController::class, 'destroyEducation'])->name('applicant.profile.education.destroy');
    Route::post('/applicant/profil/courses', [ApplicantProfileController::class, 'storeCourse'])->name('applicant.profile.course.store');
    Route::put('/applicant/profil/courses/{course}', [ApplicantProfileController::class, 'updateCourse'])->name('applicant.profile.course.update');
    Route::delete('/applicant/profil/courses/{course}', [ApplicantProfileController::class, 'destroyCourse'])->name('applicant.profile.course.destroy');
    Route::post('/applicant/profil/work-experiences', [ApplicantProfileController::class, 'storeWorkExperience'])->name('applicant.profile.work-experience.store');
    Route::put('/applicant/profil/work-experiences/{experience}', [ApplicantProfileController::class, 'updateWorkExperience'])->name('applicant.profile.work-experience.update');
    Route::delete('/applicant/profil/work-experiences/{experience}', [ApplicantProfileController::class, 'destroyWorkExperience'])->name('applicant.profile.work-experience.destroy');

    // Apply for jobs
    Route::post('/applicant/apply/{jobPosting}', [JobApplicationController::class, 'store'])->name('applicant.apply');
    Route::get('/applicant/applications/{jobApplication}', [JobApplicationController::class, 'show'])->name('applicant.applications.show');
});

// Auth Routes — Password Management (forgot + reset) — Employee (web guard)
Route::get('/employee/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/employee/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/employee/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/employee/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Auth Routes — Password Management (forgot + reset) — Member
Route::get('/member/forgot-password', [MemberForgotPasswordController::class, 'showForm'])->name('member.password.request');
Route::post('/member/forgot-password', [MemberForgotPasswordController::class, 'sendResetLink'])->name('member.password.email');
Route::get('/member/reset-password/{token}', [MemberResetPasswordController::class, 'showForm'])->name('member.password.reset');
Route::post('/member/reset-password', [MemberResetPasswordController::class, 'reset'])->name('member.password.update');

// Auth Routes — Password Management (forgot + reset) — Applicant
Route::get('/applicant/forgot-password', [ApplicantForgotPasswordController::class, 'showForm'])->name('applicant.password.request');
Route::post('/applicant/forgot-password', [ApplicantForgotPasswordController::class, 'sendResetLink'])->name('applicant.password.email');
Route::get('/applicant/reset-password/{token}', [ApplicantResetPasswordController::class, 'showForm'])->name('applicant.password.reset');
Route::post('/applicant/reset-password', [ApplicantResetPasswordController::class, 'reset'])->name('applicant.password.update');

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
Route::middleware(['auth:web', EnsureEmailIsVerified::class, 'password.changed'])->group(function () {
    // Landing Page Management
    Route::prefix('admin/landing')->name('admin.landing.')->group(function () {
        Route::patch('photos/reorder', [LandingPhotoController::class, 'reorder'])->name('photos.reorder');
        Route::resource('photos', LandingPhotoController::class)->except(['show']);
        Route::resource('leaders', LandingLeaderController::class)->except(['show']);
        Route::get('testimonials/from-review/{memberReview}', [LandingTestimonialController::class, 'fromReview'])->name('testimonials.from-review');
        Route::resource('testimonials', LandingTestimonialController::class)->except(['show']);
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/employee/profile', [EmployeeProfileController::class, 'show'])->name('employee.profile.show');
    Route::get('/employee/profile/edit', [EmployeeProfileController::class, 'edit'])->name('employee.profile.edit');
    Route::patch('/employee/profile', [EmployeeProfileController::class, 'update'])->name('employee.profile.update');
    Route::get('/employee/password', [App\Http\Controllers\Employee\PasswordController::class, 'edit'])->name('employee.password.edit');
    Route::patch('/employee/password', [App\Http\Controllers\Employee\PasswordController::class, 'update'])->name('employee.password.update');

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
    Route::middleware('can:system.permission.manage')->group(function () {
        Route::get('users/{user}/roles', [RolePermissionController::class, 'showUserRoles'])->name('users.roles');
        Route::post('users/{user}/assign-role', [RolePermissionController::class, 'assignRole'])->name('users.assign-role');
        Route::post('users/{user}/assign-permission', [RolePermissionController::class, 'assignPermission'])->name('users.assign-permission');
    });
    Route::get('users/{user}/reset-password', [UserManagementController::class, 'showResetForm'])->middleware('can:auth.user.force_reset_password')->name('users.reset-password.show');
    Route::post('users/{user}/reset-password', [UserManagementController::class, 'reset'])->middleware('can:auth.user.force_reset_password')->name('users.reset-password.update');

    // Admin Routes — Role Management (Spatie Permission CRUD)
    Route::middleware('can:system.permission.manage')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::patch('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

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
    Route::get('resign-requests', [ResignRequestController::class, 'index'])->middleware('can:contract.manage')->name('resign-requests.index');
    Route::get('resign-requests/{resignRequest}', [ResignRequestController::class, 'show'])->name('resign-requests.show');
    Route::post('resign-requests/{resignRequest}/approve', [ResignRequestController::class, 'approve'])->name('resign-requests.approve');
    Route::post('resign-requests/{resignRequest}/reject', [ResignRequestController::class, 'reject'])->name('resign-requests.reject');

    // Employee Offboarding (contract termination)
    Route::get('employees/{employee}/off-boarding/create', [OffBoardingController::class, 'create'])->name('off-boarding.create');
    Route::post('employees/{employee}/off-boarding', [OffBoardingController::class, 'store'])->name('off-boarding.store');

    // Contract Termination — GAP-208
    Route::prefix('employment-statuses/{status}/terminate')->name('contract-terminate.')->group(function () {
        Route::get('/', [ContractTerminateController::class, 'initiate'])->name('initiate');
        Route::post('/', [ContractTerminateController::class, 'initiateStore'])->name('initiate.store');
        Route::get('checklist', [ContractTerminateController::class, 'checklistShow'])->name('checklist');
        Route::patch('checklist', [ContractTerminateController::class, 'checklistUpdate'])->name('checklist.update');
        Route::get('complete', [ContractTerminateController::class, 'completeConfirmation'])->name('complete.confirm');
        Route::post('complete', [ContractTerminateController::class, 'complete'])->name('complete');
    });

    // Employee Branch Transfer (2 flows: self-service + direct)
    Route::get('employees/{employee}/branch-transfer/request', [BranchTransferController::class, 'requestCreate'])->name('branch-transfers.request.create');
    Route::post('employees/{employee}/branch-transfer/request', [BranchTransferController::class, 'requestStore'])->name('branch-transfers.request.store');
    Route::get('branch-transfers/review', [BranchTransferController::class, 'reviewIndex'])->name('branch-transfers.review.index');
    Route::get('branch-transfers/{transfer}', [BranchTransferController::class, 'reviewShow'])->name('branch-transfers.show');
    Route::put('branch-transfers/{transfer}', [BranchTransferController::class, 'reviewUpdate'])->name('branch-transfers.update');
    Route::get('employees/{employee}/branch-transfer/direct', [BranchTransferController::class, 'directEdit'])->name('branch-transfers.direct.edit');
    Route::put('employees/{employee}/branch-transfer/direct', [BranchTransferController::class, 'directUpdate'])->name('branch-transfers.direct.update');

    // Attendance Domain
    Route::resource('work-schedule-rules', WorkScheduleRuleController::class);
    Route::post('work-schedule-assignments', [WorkScheduleAssignmentController::class, 'store'])->name('work-schedule-assignments.store');
    Route::delete('work-schedule-assignments/{workScheduleAssignment}', [WorkScheduleAssignmentController::class, 'destroy'])->name('work-schedule-assignments.destroy');
    Route::resource('holidays', HolidayController::class)->except(['index', 'show']);
    // Route::resource('attendance-logs', AttendanceLogController::class);
    Route::resource('attendance-policies', AttendancePolicyController::class);
    Route::resource('attendance-rules', AttendanceRuleController::class);
    Route::resource('leave-requests', LeaveRequestController::class)->only(['index', 'create', 'store']);
    Route::post('leave-requests/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');
    Route::post('leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');
    Route::get('leave-requests/manage', [LeaveRequestController::class, 'manage'])->name('leave-requests.manage');
    Route::resource('attendance-recaps', AttendanceRecapController::class)->only(['index', 'show']);
    Route::get('attendance/document-signature', [DocumentSignatureSettingController::class, 'index'])->name('attendance.document-signature.index');
    Route::put('attendance/document-signature', [DocumentSignatureSettingController::class, 'upsert'])->name('attendance.document-signature.upsert');

    // Work Attendance — check-in/out + manage
    Route::get('work-attendance', [WorkAttendanceController::class, 'index'])->name('work-attendance.index');
    Route::post('work-attendance/check-in', [WorkAttendanceController::class, 'checkIn'])->name('work-attendance.check-in');
    Route::post('work-attendance/check-out', [WorkAttendanceController::class, 'checkOut'])->name('work-attendance.check-out');
    Route::get('work-attendance/manage', [WorkAttendanceController::class, 'manage'])->name('work-attendance.manage');
    Route::post('work-attendance/{attendanceLog}/verify', [WorkAttendanceController::class, 'verify'])->name('work-attendance.verify');
    Route::post('work-attendance/{attendanceLog}/adjust', [WorkAttendanceController::class, 'adjust'])->name('work-attendance.adjust');

    // Session Attendance — tutor check-in/out per sesi
    Route::get('session-attendance', [SessionAttendanceController::class, 'index'])->name('session-attendance.index');
    Route::post('session-attendance/{sessionSchedule}/check-in', [SessionAttendanceController::class, 'checkIn'])->name('session-attendance.check-in');
    Route::post('session-attendance/{sessionSchedule}/check-out', [SessionAttendanceController::class, 'checkOut'])->name('session-attendance.check-out');
    Route::post('session-attendance/{sessionLog}/verify', [SessionAttendanceController::class, 'verify'])->name('session-attendance.verify');

    // KPI Domain
    Route::prefix('kpi')->name('kpi.')->group(function () {
        // Templates
        Route::resource('templates', KpiTemplateController::class)->except(['destroy']);
        Route::prefix('templates/{kpiTemplate}')->name('templates.')->group(function () {
            Route::get('indicators/create', [KpiTemplateIndicatorController::class, 'create'])->name('indicators.create');
            Route::post('indicators', [KpiTemplateIndicatorController::class, 'store'])->name('indicators.store');
            Route::get('indicators/{indicator}/edit', [KpiTemplateIndicatorController::class, 'edit'])->name('indicators.edit');
            Route::put('indicators/{indicator}', [KpiTemplateIndicatorController::class, 'update'])->name('indicators.update');
            Route::delete('indicators/{indicator}', [KpiTemplateIndicatorController::class, 'destroy'])->name('indicators.destroy');
        });

        // Grade Rules
        Route::resource('grade-rules', KpiGradeRuleController::class)->except(['show']);

        // Evaluator Assignments
        Route::resource('evaluator-assignments', KpiEvaluatorAssignmentController::class)->except(['show', 'destroy']);

        // Evaluations
        Route::resource('evaluations', KpiEvaluationController::class)->except(['edit', 'update', 'destroy']);
        Route::post('evaluations/{kpiEvaluation}/finalize', [KpiEvaluationController::class, 'finalize'])->name('evaluations.finalize');
        Route::put('evaluations/{kpiEvaluation}/items/{item}', [KpiEvaluationItemController::class, 'update'])->name('evaluations.items.update');

        // Documents
        Route::post('documents', [KpiDocumentController::class, 'store'])->name('documents.store');
        Route::delete('documents/{kpiDocument}', [KpiDocumentController::class, 'destroy'])->name('documents.destroy');
    });

    // Marketing Domain
    Route::resource('socializations', SocializationController::class);
    Route::post('socializations/{socialization}/schedule', [SocializationController::class, 'schedule'])->name('socializations.schedule');
    Route::post('socializations/{socialization}/cancel', [SocializationController::class, 'cancel'])->name('socializations.cancel');
    Route::post('socializations/{socialization}/assign-employee', [SocializationController::class, 'assignEmployee'])->name('socializations.assign-employee');
    Route::patch('socializations/{socialization}/partner-fee', [SocializationController::class, 'updatePartnerFee'])->name('socializations.partner-fee');
    Route::resource('prospective-members', ProspectiveMemberController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('prospective-members/{prospectiveMember}/status', [ProspectiveMemberController::class, 'updateStatus'])->name('prospective-members.update-status');
    Route::post('prospective-members/{prospectiveMember}/follow-ups', [ProspectiveMemberController::class, 'storeFollowUp'])->name('prospective-members.follow-ups.store');
    Route::resource('wa-templates', EmployeeWaTemplateController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('marketing-target', [MarketingTargetController::class, 'index'])->name('marketing-target.index');
    Route::post('marketing-target/employee', [MarketingTargetController::class, 'setEmployeeTarget'])->name('marketing-target.employee');
    Route::post('marketing-target/position-default', [MarketingTargetController::class, 'setPositionDefault'])->name('marketing-target.position-default');
    Route::get('marketing-kpi', [MarketingKpiController::class, 'index'])->name('marketing-kpi.index');
    Route::get('marketing-kpi/employees/{employee}', [MarketingKpiController::class, 'show'])->name('marketing-kpi.show');
    Route::get('marketing-kpi/rules', [MarketingKpiController::class, 'rules'])->name('marketing-kpi.rules');
    Route::post('marketing-kpi/rules', [MarketingKpiController::class, 'storeRule'])->name('marketing-kpi.rules.store');
    Route::post('marketing-kpi/rules/{rule}/activate', [MarketingKpiController::class, 'activateRule'])->name('marketing-kpi.rules.activate');
    Route::get('marketing/my-performance', [MarketingMyPerformanceController::class, 'index'])->name('marketing.my-performance');
    Route::get('marketing/member-payment-statement', [MemberPaymentStatementController::class, 'index'])->name('marketing.member-payment-statement');
    Route::get('marketing/monthly-revenue-data', [MonthlyRevenueDataController::class, 'index'])->name('marketing.monthly-revenue-data');

    // Bonus Domain
    Route::prefix('bonus')->name('bonus.')->group(function () {
        Route::resource('marketing-rules', MarketingBonusRuleController::class)->except(['create', 'edit']);
        Route::patch('marketing-rules/{marketingRule}/toggle-active', [MarketingBonusRuleController::class, 'toggleActive'])->name('marketing-rules.toggle-active');
        Route::post('marketing-rules/{marketingRule}/tiers', [MarketingBonusRuleController::class, 'storeTier'])->name('marketing-rules.tiers.store');
        Route::put('marketing-rules/{marketingRule}/tiers/{tier}', [MarketingBonusRuleController::class, 'updateTier'])->name('marketing-rules.tiers.update');
        Route::delete('marketing-rules/{marketingRule}/tiers/{tier}', [MarketingBonusRuleController::class, 'destroyTier'])->name('marketing-rules.tiers.destroy');

        Route::resource('kpi-rules', KpiBonusRuleController::class)->except(['create', 'edit']);
        Route::patch('kpi-rules/{kpiRule}/toggle-active', [KpiBonusRuleController::class, 'toggleActive'])->name('kpi-rules.toggle-active');
        Route::post('kpi-rules/{kpiRule}/tiers', [KpiBonusRuleController::class, 'storeTier'])->name('kpi-rules.tiers.store');
        Route::put('kpi-rules/{kpiRule}/tiers/{tier}', [KpiBonusRuleController::class, 'updateTier'])->name('kpi-rules.tiers.update');
        Route::delete('kpi-rules/{kpiRule}/tiers/{tier}', [KpiBonusRuleController::class, 'destroyTier'])->name('kpi-rules.tiers.destroy');

        Route::resource('special-rules', SpecialBonusRuleController::class)->except(['create', 'edit']);
        Route::patch('special-rules/{specialRule}/toggle-active', [SpecialBonusRuleController::class, 'toggleActive'])->name('special-rules.toggle-active');
        Route::post('special-rules/{specialRule}/conditions', [SpecialBonusRuleController::class, 'storeCondition'])->name('special-rules.conditions.store');
        Route::put('special-rules/{specialRule}/conditions/{condition}', [SpecialBonusRuleController::class, 'updateCondition'])->name('special-rules.conditions.update');
        Route::delete('special-rules/{specialRule}/conditions/{condition}', [SpecialBonusRuleController::class, 'destroyCondition'])->name('special-rules.conditions.destroy');

        Route::get('history', [BonusRuleChangeHistoryController::class, 'index'])->name('history.index');
    });

    // Payroll Domain — GAP-92/100/103/114/120/126
    Route::resource('payroll-periods', PayrollPeriodController::class)->names([
        'index' => 'payroll.periods.index',
        'create' => 'payroll.periods.create',
        'store' => 'payroll.periods.store',
        'show' => 'payroll.periods.show',
        'edit' => 'payroll.periods.edit',
        'update' => 'payroll.periods.update',
        'destroy' => 'payroll.periods.destroy',
    ]);
    Route::get('payroll-periods/{payrollPeriod}/preview-generate', [PayrollPeriodController::class, 'previewGenerate'])->name('payroll.periods.preview-generate');
    Route::post('payroll-periods/{payrollPeriod}/generate', [PayrollPeriodController::class, 'generate'])->name('payroll.periods.generate');
    Route::patch('payroll-periods/{payrollPeriod}/advance-status', [PayrollPeriodController::class, 'advanceStatus'])->name('payroll.periods.advance-status');
    Route::patch('payroll-periods/{payrollPeriod}/revert', [PayrollPeriodController::class, 'revert'])->name('payroll.periods.revert');
    Route::post('payroll-periods/auto-create', [PayrollPeriodController::class, 'autoCreate'])->name('payroll.periods.auto-create');

    Route::resource('payroll/component', PayrollComponentController::class)
        ->except(['create', 'edit', 'show'])
        ->parameters(['component' => 'component'])
        ->names('payroll.components');
    Route::resource('session-compensation-rules', SessionCompensationRuleController::class)->except(['create', 'edit', 'show'])->names([
        'index' => 'payroll.session-rules.index',
        'store' => 'payroll.session-rules.store',
        'update' => 'payroll.session-rules.update',
        'destroy' => 'payroll.session-rules.destroy',
    ]);
    Route::get('payroll/payment', [PayrollPaymentController::class, 'index'])->name('payroll.payments.index');
    Route::get('payroll/recap', [PayrollPeriodController::class, 'recap'])->name('payroll.recap');

    Route::prefix('payroll-periods/{period}')->name('payroll.')->group(function () {
        Route::get('payrolls/{payroll}', [EmployeePayrollController::class, 'show'])->name('payrolls.show');
        Route::post('payrolls/{payroll}/adjust', [EmployeePayrollController::class, 'adjust'])->name('payrolls.adjust');
        Route::post('payrolls/{payroll}/slip', [PayrollSlipController::class, 'generate'])->name('payrolls.slip.generate');
        Route::get('payrolls/{payroll}/slip/download', [PayrollSlipController::class, 'download'])->name('payrolls.slip.download');
        Route::post('payrolls/{payroll}/payment', [PayrollPaymentController::class, 'store'])->name('payrolls.payment.store');
        Route::patch('payrolls/{payroll}/payment/failed', [PayrollPaymentController::class, 'markFailed'])->name('payrolls.payment.failed');
    });

    Route::prefix('employees/{employee}')->name('employees.')->group(function () {
        Route::resource('compensations', EmployeeCompensationController::class)->except(['create', 'edit', 'show'])->names([
            'index' => 'payroll.compensations.index',
            'store' => 'payroll.compensations.store',
            'update' => 'payroll.compensations.update',
            'destroy' => 'payroll.compensations.destroy',
        ]);
    });

    // Employee self-service payslip portal — GAP-132
    Route::get('my-payslips', [PayslipController::class, 'index'])->name('employee.payslips.index');
    Route::get('my-payslips/{payroll}/download', [PayslipController::class, 'download'])->name('employee.payslips.download');

    // Member Domain
    Route::post('members/import', [MemberDataController::class, 'import'])->name('members.import');
    Route::post('members/{member}/activate', [MemberDataController::class, 'activate'])->name('members.activate');
    Route::resource('members', MemberDataController::class);
    Route::prefix('members/{member}')->name('members.')->group(function () {
        Route::get('registrations/create', [MemberRegistrationController::class, 'create'])->name('registrations.create');
        Route::post('registrations', [MemberRegistrationController::class, 'store'])->name('registrations.store');
    });
    Route::prefix('registrations')->name('registrations.')->group(function () {
        Route::get('{registration}', [MemberRegistrationController::class, 'show'])->name('show');
        Route::post('{registration}/certificates', [MemberCertificateController::class, 'store'])->name('certificates.store');
        Route::post('{registration}/payments', [MemberPaymentController::class, 'store'])->name('payments.store');
        Route::patch('{registration}/graduation', [MemberRegistrationController::class, 'updateGraduation'])->name('graduation.update');
        Route::patch('{registration}/payment-status', [MemberRegistrationController::class, 'updatePaymentStatus'])->name('payment-status.update');
    });
    Route::patch('member-payments/{memberPayment}', [MemberPaymentController::class, 'update'])->name('member-payments.update');
    Route::resource('discounts', DiscountController::class)->except(['create', 'edit']);
    Route::patch('discounts/{discount}/toggle-active', [DiscountController::class, 'toggleActive'])->name('discounts.toggle-active');
    Route::put('discounts/{discount}/programs', [DiscountController::class, 'syncPrograms'])->name('discounts.programs.sync');
    Route::prefix('support-tickets')->name('support-tickets.')->group(function () {
        Route::get('/', [MemberSupportTicketController::class, 'index'])->name('index');
        Route::post('/', [MemberSupportTicketController::class, 'store'])->name('store');
        Route::get('{ticket}', [MemberSupportTicketController::class, 'show'])->name('show');
        Route::post('{ticket}/status', [MemberSupportTicketController::class, 'changeStatus'])->name('status');
        Route::post('{ticket}/assign', [MemberSupportTicketController::class, 'assign'])->name('assign');
        Route::post('{ticket}/rate', [MemberSupportTicketController::class, 'rate'])->name('rate');
        Route::post('{ticket}/replies', [MemberSupportTicketReplyController::class, 'store'])->name('replies.store');
    });
    Route::get('nps-responses', [MemberNpsResponseController::class, 'index'])->name('nps-responses.index');
    Route::get('ticket-assignment-rules', [SupportTicketAssignmentRuleController::class, 'index'])->name('ticket-assignment-rules.index');
    Route::post('ticket-assignment-rules', [SupportTicketAssignmentRuleController::class, 'store'])->name('ticket-assignment-rules.store');
    Route::delete('ticket-assignment-rules/{supportTicketAssignmentRule}', [SupportTicketAssignmentRuleController::class, 'destroy'])->name('ticket-assignment-rules.destroy');
    Route::post('member-reviews/{review}/approve', [MemberNpsResponseController::class, 'approveReview'])->name('member-reviews.approve');
    Route::post('member-reviews/{review}/reject', [MemberNpsResponseController::class, 'rejectReview'])->name('member-reviews.reject');

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
    Route::post('classrooms/{classroom}/graduate', [ClassRoomController::class, 'graduate'])->name('classrooms.graduate');
    Route::post('classrooms/{classroom}/members', [MemberClassController::class, 'store'])->name('member-class.store');
    Route::delete('member-class/{memberClass}', [MemberClassController::class, 'destroy'])->name('member-class.destroy');
    Route::put('member-class/{memberClass}/transfer', [MemberClassController::class, 'transfer'])->name('member-class.transfer');

    // Class Session — material + attendance + assessment + progress
    Route::patch('class-schedules/{schedule}/material', [ClassScheduleController::class, 'update'])->name('class-schedules.material');
    Route::post('class-schedules/{schedule}/attendance', [MemberAttendanceController::class, 'store'])->name('member-attendance.store');
    Route::patch('member-attendance/{memberAttendance}', [MemberAttendanceController::class, 'update'])->name('member-attendance.update');
    Route::post('class-schedules/{schedule}/assessments', [MemberSessionAssessmentController::class, 'store'])->name('member-assessments.store');
    Route::patch('member-assessments/{assessment}', [MemberSessionAssessmentController::class, 'update'])->name('member-assessments.update');
    Route::patch('curriculum-progress/{progress}', [MemberCurriculumProgressController::class, 'update'])->name('curriculum-progress.update');

    // Class Test
    Route::get('classrooms/{classroom}/tests/create', [ClassTestController::class, 'create'])->name('class-tests.create');
    Route::post('classrooms/{classroom}/tests', [ClassTestController::class, 'store'])->name('class-tests.store');
    Route::get('classrooms/{classroom}/tests/{classTest}/edit', [ClassTestController::class, 'edit'])->name('class-tests.edit');
    Route::put('classrooms/{classroom}/tests/{classTest}', [ClassTestController::class, 'update'])->name('class-tests.update');
    Route::delete('classrooms/{classroom}/tests/{classTest}', [ClassTestController::class, 'destroy'])->name('class-tests.destroy');

    Route::post('tests/{test}/questions', [TestQuestionController::class, 'store'])->name('tests.questions.store');
    Route::delete('test-questions/{question}', [TestQuestionController::class, 'destroy'])->name('test-questions.destroy');
    Route::post('member-class/{memberClass}/tests/{classTest}/results', [MemberTestResultController::class, 'store'])->name('member-test-results.store');
    Route::post('member-test-results/{result}/review-essay', [MemberTestResultController::class, 'reviewEssay'])->name('member-test-results.review-essay');

    // Certificate
    Route::patch('member-certificates/{memberCertificate}', [MemberCertificateController::class, 'update'])->name('member-certificates.update');

    // Curriculum Scoped Access (by classroom)
    Route::get('classrooms/{classroom}/curriculum', [ClassCurriculumController::class, 'index'])->name('class-curriculum.index');
    Route::get('classrooms/{classroom}/curriculum/items/{item}', [ClassCurriculumController::class, 'show'])->name('class-curriculum.show');
    Route::get('classrooms/{classroom}/curriculum/items/{item}/download', [ClassCurriculumController::class, 'downloadFile'])->name('class-curriculum.download');

    // Finance Domain
    Route::resource('budget-estimates', BudgetEstimateController::class)->except(['destroy']);
    Route::post('budget-estimates/{budgetEstimate}/submit-ops', [BudgetEstimateController::class, 'submitOps'])->name('budget-estimates.submit-ops');
    Route::post('budget-estimates/{budgetEstimate}/submit-finance', [BudgetEstimateController::class, 'submitFinance'])->name('budget-estimates.submit-finance');
    Route::post('budget-estimates/{budgetEstimate}/review-finance', [BudgetEstimateController::class, 'reviewFinance'])->name('budget-estimates.review-finance');
    Route::post('budget-estimates/{budgetEstimate}/send', [BudgetEstimateController::class, 'send'])->name('budget-estimates.send');

    Route::resource('reimbursements', ReimbursementController::class)->except(['destroy']);
    Route::post('reimbursements/{reimbursement}/submit', [ReimbursementController::class, 'submit'])->name('reimbursements.submit');
    Route::post('reimbursements/{reimbursement}/review', [ReimbursementController::class, 'review'])->name('reimbursements.review');
    Route::post('reimbursements/{reimbursement}/revert', [ReimbursementController::class, 'revert'])->name('reimbursements.revert');
    Route::post('reimbursements/{reimbursement}/mark-paid', [ReimbursementController::class, 'markPaid'])->name('reimbursements.mark-paid');
    Route::get('reimbursements-review', [ReimbursementController::class, 'reviewList'])->name('reimbursements.review-list');

    Route::get('branch-monthly-costs', [BranchMonthlyCostController::class, 'index'])->name('branch-monthly-costs.index');
    Route::post('branch-monthly-costs', [BranchMonthlyCostController::class, 'store'])->name('branch-monthly-costs.store');
    Route::patch('branch-monthly-costs/{branchMonthlyCost}', [BranchMonthlyCostController::class, 'update'])->name('branch-monthly-costs.update');
    Route::delete('branch-monthly-costs/{branchMonthlyCost}', [BranchMonthlyCostController::class, 'destroy'])->name('branch-monthly-costs.destroy');

    Route::get('period-locks', [BranchPeriodLockController::class, 'index'])->name('period-locks.index');
    Route::post('period-locks/lock', [BranchPeriodLockController::class, 'lock'])->name('period-locks.lock');
    Route::post('period-locks/unlock', [BranchPeriodLockController::class, 'unlock'])->name('period-locks.unlock');

    // Facility Domain
    Route::prefix('facility')->name('facility.')->group(function () {
        // Tickets
        Route::resource('tickets', FacilityTicketController::class)->except(['destroy']);
        Route::post('tickets/{facilityTicket}/review', [FacilityTicketController::class, 'review'])->name('tickets.review');
        Route::post('tickets/{facilityTicket}/resolve', [FacilityTicketController::class, 'resolve'])->name('tickets.resolve');
        Route::post('tickets/{facilityTicket}/assign', [FacilityTicketController::class, 'assign'])->name('tickets.assign');
        Route::post('tickets/{facilityTicket}/attachments', [FacilityTicketAttachmentController::class, 'store'])->name('tickets.attachments.store');
        Route::delete('tickets/{facilityTicket}/attachments/{attachment}', [FacilityTicketAttachmentController::class, 'destroy'])->name('tickets.attachments.destroy');

        // Inventory
        Route::resource('inventory', InventoryItemController::class)->except(['destroy']);
        Route::post('inventory/{inventoryItem}/adjust', [InventoryItemController::class, 'adjustQuantity'])->name('inventory.adjust');
        Route::post('inventory/{inventoryItem}/dispose', [InventoryItemController::class, 'dispose'])->name('inventory.dispose');

        // Rent Contracts
        Route::resource('rent-contracts', BranchRentContractController::class);
        Route::post('rent-contracts/{branchRentContract}/termins/{termin}/mark-paid', [BranchRentTerminController::class, 'markPaid'])->name('rent-contracts.termins.mark-paid');
    });

    // System & Access Domain (superadmin)
    Route::prefix('system')->name('system.')->group(function () {
        Route::resource('notification-routings', NotificationRoutingController::class)->except(['show']);
        Route::resource('dashboard-widgets', DashboardWidgetConfigController::class)->except(['show', 'edit', 'create']);
        Route::resource('bots', BotManagementController::class)->except(['show', 'destroy']);
        Route::post('bots/{bot}/toggle', [BotManagementController::class, 'toggleActive'])->name('bots.toggle');
        Route::resource('webhooks', WebhookStatusController::class)->except(['show']);
        Route::post('webhooks/{webhook}/ping', [WebhookStatusController::class, 'ping'])->name('webhooks.ping');
        // GAP-46: rute force-reset lama ini duplikat — fungsinya sudah ada di users/{user}/reset-password
        // Route::get('force-reset', [ForceResetPasswordController::class, 'index'])->name('force-reset.index');
        // Route::post('force-reset', [ForceResetPasswordController::class, 'reset'])->name('force-reset.reset');
        Route::resource('api-keys', ApiKeyController::class)->except(['show']);
        Route::post('api-keys/{apiKey}/toggle', [ApiKeyController::class, 'toggleActive'])->name('api-keys.toggle');
    });

    // Letter Domain
    Route::resource('letter-templates', LetterTemplateController::class);
    Route::post('letter-templates/{letterTemplate}/toggle-active', [LetterTemplateController::class, 'toggleActive'])->name('letter-templates.toggle-active');
    Route::get('letter-templates/manual-vars', [LetterTemplateController::class, 'manualVars'])->name('letter-templates.manual-vars');

    Route::resource('out-letters-generate', OutLetterViaGenerateController::class)->except(['destroy']);
    Route::post('out-letters-generate/{outLetterViaGenerate}/publish', [OutLetterViaGenerateController::class, 'publish'])->name('out-letters-generate.publish');
    Route::get('out-letters-generate/{outLetterViaGenerate}/download', [OutLetterViaGenerateController::class, 'download'])->name('out-letters-generate.download');

    Route::resource('out-letters-upload', OutLetterViaUploadController::class);
    Route::get('out-letters-upload/{outLetterViaUpload}/download', [OutLetterViaUploadController::class, 'download'])->name('out-letters-upload.download');

    Route::resource('in-letters', InLetterController::class);
    Route::get('in-letters/{inLetter}/download', [InLetterController::class, 'download'])->name('in-letters.download');

    Route::resource('sop-documents', SopDocumentController::class)->except(['destroy']);
    Route::post('sop-documents/{sopDocument}/deactivate', [SopDocumentController::class, 'deactivate'])->name('sop-documents.deactivate');
    Route::get('sop-documents/{sopDocument}/download', [SopDocumentController::class, 'download'])->name('sop-documents.download');

    // Recruitment Domain
    Route::prefix('recruitment')->name('recruitment.')->group(function () {
        // Job Permintaan
        Route::resource('permintaan', JobPermintaanController::class);
        Route::post('permintaan/{jobPermintaan}/submit', [JobPermintaanController::class, 'submit'])->name('permintaan.submit');
        Route::post('permintaan/{jobPermintaan}/hr-review', [JobPermintaanController::class, 'hrReview'])->name('permintaan.hr-review');
        Route::post('permintaan/{jobPermintaan}/ops-approve', [JobPermintaanController::class, 'opsApprove'])->name('permintaan.ops-approve');
        Route::post('permintaan/{jobPermintaan}/fulfill', [JobPermintaanController::class, 'markFulfilled'])->name('permintaan.fulfill');

        // Job Posting
        Route::resource('postings', JobPostingController::class);
        Route::post('postings/{jobPosting}/publish', [JobPostingController::class, 'publish'])->name('postings.publish');
        Route::post('postings/{jobPosting}/close', [JobPostingController::class, 'close'])->name('postings.close');

        // Job Applications (HR view)
        Route::get('applications', [JobApplicationController::class, 'index'])->name('application.index');
        Route::get('applications/{jobApplication}', [JobApplicationController::class, 'show'])->name('application.show');
        Route::post('applications/manual', [JobApplicationController::class, 'storeManual'])->name('application.manual');
        Route::post('applications/{jobApplication}/reject', [JobApplicationController::class, 'reject'])->name('application.reject');
        Route::post('applications/{jobApplication}/advance-reserve', [JobApplicationController::class, 'advanceReserve'])->name('application.advance-reserve');

        // Psikotest
        Route::post('applications/{jobApplication}/psikotest', [ApplicantPsikotestController::class, 'store'])->name('application.psikotest.store');
        Route::patch('applications/{jobApplication}/psikotest', [ApplicantPsikotestController::class, 'update'])->name('application.psikotest.update');

        // Interview Schedule
        Route::post('applications/{jobApplication}/interviews', [ApplicantInterviewScheduleController::class, 'store'])->name('application.interviews.store');
        Route::patch('interviews/{schedule}', [ApplicantInterviewScheduleController::class, 'update'])->name('interviews.update');
        Route::delete('interviews/{schedule}', [ApplicantInterviewScheduleController::class, 'destroy'])->name('interviews.destroy');

        // Interview Evaluation
        Route::post('interviews/{schedule}/evaluation', [ApplicantInterviewEvaluationController::class, 'store'])->name('interviews.evaluation.store');

        // Offering Letter
        Route::post('applications/{jobApplication}/offering-letter', [OfferingLetterController::class, 'store'])->name('application.offering-letter.store');
        Route::patch('offering-letters/{offeringLetter}', [OfferingLetterController::class, 'update'])->name('offering-letters.update');
        Route::post('offering-letters/{offeringLetter}/negotiate', [OfferingLetterController::class, 'negotiate'])->name('offering-letters.negotiate');
        Route::post('offering-letters/{offeringLetter}/accept', [OfferingLetterController::class, 'accept'])->name('offering-letters.accept');
        Route::post('offering-letters/{offeringLetter}/decline', [OfferingLetterController::class, 'decline'])->name('offering-letters.decline');
        Route::get('offering-letters/{offeringLetter}/download', [OfferingLetterController::class, 'downloadPdf'])->name('offering-letters.download');

        // Employee Onboarding
        Route::get('onboarding', [EmployeeOnboardingController::class, 'index'])->name('onboarding.index');
        Route::get('onboarding/{employeeOnboarding}', [EmployeeOnboardingController::class, 'show'])->name('onboarding.show');
        Route::post('applications/{jobApplication}/onboarding', [EmployeeOnboardingController::class, 'store'])->name('application.onboarding.store');
        Route::post('onboarding/{employeeOnboarding}/review', [EmployeeOnboardingController::class, 'review'])->name('onboarding.review');
        Route::post('onboarding/{employeeOnboarding}/complete', [EmployeeOnboardingController::class, 'complete'])->name('onboarding.complete');
    });

    // Notification Domain
    Route::resource('notification-templates', NotificationTemplateController::class);
    Route::post('notification-templates/{notificationTemplate}/variables', [NotificationTemplateController::class, 'storeVariable'])->name('notification-templates.variables.store');
    Route::patch('notification-templates/{notificationTemplate}/variables/{variable}', [NotificationTemplateController::class, 'updateVariable'])->name('notification-templates.variables.update');
    Route::delete('notification-templates/{notificationTemplate}/variables/{variable}', [NotificationTemplateController::class, 'destroyVariable'])->name('notification-templates.variables.destroy');
    Route::get('notification-logs', [NotificationLogController::class, 'index'])->name('notification-logs.index');
    Route::post('notification-logs/{id}/resend', [NotificationLogController::class, 'resend'])->name('notification-logs.resend');
    Route::get('notification-manual-send', [NotificationManualSendController::class, 'create'])->name('notification-manual-send.create');
    Route::post('notification-manual-send', [NotificationManualSendController::class, 'store'])->name('notification-manual-send.store');
    // Employee notification inbox
    Route::get('notifications', [EmployeeNotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread-count', [EmployeeNotificationController::class, 'unreadCount'])->name('notifications.unread-count');

    // Employee Survey (self-service)
    Route::get('my-surveys', [EmployeeSurveyController::class, 'index'])->name('employee.surveys.index');
    Route::get('my-surveys/{employeeSurvey}', [EmployeeSurveyController::class, 'show'])->name('employee.surveys.show');
    Route::post('my-surveys/{employeeSurvey}/submit', [EmployeeSurveyController::class, 'store'])->name('employee.surveys.submit');

    // Survey Domain
    Route::resource('surveys', SurveyController::class);
    Route::post('surveys/{survey}/assign-members', [SurveyAssignmentController::class, 'assignMembers'])->name('surveys.assign-members');
    Route::post('surveys/{survey}/assign-employees', [SurveyAssignmentController::class, 'assignEmployees'])->name('surveys.assign-employees');
    Route::get('surveys/{survey}/results', [SurveyResultController::class, 'show'])->name('surveys.results');

    // TOEFL Domain — Admin
    Route::resource('toefl-tests', ToeflTestController::class);
    Route::post('toefl-tests/{toeflTest}/publish', [ToeflTestController::class, 'publish'])->name('toefl-tests.publish');
    Route::post('toefl-tests/{toeflTest}/passages', [ToeflPassageController::class, 'store'])->name('toefl.passages.store');
    Route::patch('toefl-passages/{passage}', [ToeflPassageController::class, 'update'])->name('toefl.passages.update');
    Route::delete('toefl-passages/{passage}', [ToeflPassageController::class, 'destroy'])->name('toefl.passages.destroy');
    Route::post('toefl-tests/{toeflTest}/questions', [ToeflQuestionController::class, 'store'])->name('toefl.questions.store');
    Route::patch('toefl-questions/{question}', [ToeflQuestionController::class, 'update'])->name('toefl.questions.update');
    Route::patch('toefl-questions/{question}/toggle-active', [ToeflQuestionController::class, 'toggleActive'])->name('toefl.questions.toggle-active');
    Route::delete('toefl-questions/{question}', [ToeflQuestionController::class, 'destroy'])->name('toefl.questions.destroy');
    Route::get('toefl-media', [ToeflMediaController::class, 'index'])->name('toefl.media.index');
    Route::post('toefl-media', [ToeflMediaController::class, 'store'])->name('toefl.media.store');
    Route::delete('toefl-media/{toeflMedia}', [ToeflMediaController::class, 'destroy'])->name('toefl.media.destroy');
    Route::get('toefl-guest-leads', [GuestToeflLeadController::class, 'index'])->name('toefl.guest-lead.index');
});

require __DIR__.'/webhook.php';

// Public Data API for Registration
Route::get('/api/public/provinces', [App\Http\Controllers\Public\RegionController::class, 'getActiveProvinces'])->name('api.public.provinces');
Route::get('/api/public/regions', [App\Http\Controllers\Public\RegionController::class, 'getActiveRegions'])->name('api.public.regions');
Route::get('/api/public/institutions', [App\Http\Controllers\Public\RegionController::class, 'getInstitutions'])->name('api.public.institutions');
