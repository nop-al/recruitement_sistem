<?php

use App\Http\Controllers\AdminApplicationController;
use App\Http\Controllers\AdminConfigController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\OfferingController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PositionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Recruitment Management System:
| - Authentication: Login, Candidate Registration, Logout
| - Protected Dashboards with Role-Based Access Control (RBAC)
|
*/

// Home / Root: Landing page for guests, redirects to dashboard for authenticated users
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/home', [DashboardController::class, 'home'])->name('public.home');

// Public Lowongan (Vacancy) Pages - encrypted slug, no auth required
Route::get('/lowongan', [DashboardController::class, 'lowonganIndex'])->name('public.lowongan.index');
Route::get('/lowongan/{encryptedId}', [DashboardController::class, 'lowonganShow'])->name('public.lowongan.show');

// Quick NIK Status Check (Tanpa harus login)
Route::post('/check-status-nik', [DashboardController::class, 'checkStatusByNik'])
    ->name('check-status-nik')
    ->middleware('throttle:20,1');

// Guest Authentication Routes with Brute-Force Rate Limiting
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post')->middleware('throttle:3,1');

    // Forgot & Reset Password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update')->middleware('throttle:5,1');
});

// Logout (Authenticated users)
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Secure Authorized Document Access (Resume & Onboarding Documents)
Route::middleware(['auth', 'throttle:45,1'])->prefix('documents')->name('documents.')->group(function () {
    Route::get('/resume/{candidate}', [DocumentController::class, 'downloadResume'])->name('resume');
    Route::get('/onboarding/{onboarding}/{type}', [DocumentController::class, 'downloadOnboarding'])->name('onboarding');
});

// Protected Role Dashboards
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // Admin User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/blacklist', [AdminUserController::class, 'toggleBlacklist'])->name('users.blacklist');
    Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');

    // Admin Position / Vacancy Management
    Route::get('/positions', [PositionController::class, 'index'])->name('positions.index');
    Route::get('/positions/create', [PositionController::class, 'create'])->name('positions.create');
    Route::post('/positions', [PositionController::class, 'store'])->name('positions.store');
    Route::get('/positions/{position}', [PositionController::class, 'show'])->name('positions.show');
    Route::get('/positions/{position}/edit', [PositionController::class, 'edit'])->name('positions.edit');
    Route::put('/positions/{position}', [PositionController::class, 'update'])->name('positions.update');
    Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');

    // Admin Candidate Applications Management (Halaman Terpisah)
    Route::get('/applications/export/excel', [AdminApplicationController::class, 'exportExcel'])->name('applications.export');
    Route::get('/applications/template/mass-status', [AdminApplicationController::class, 'downloadMassTemplate'])->name('applications.template');
    Route::post('/applications/upload-mass', [AdminApplicationController::class, 'uploadMassStatus'])->name('applications.upload-mass');
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/update-stage', [AdminApplicationController::class, 'updateStage'])->name('applications.update-stage');

    // Admin Online Interview Sessions Management
    Route::get('/interviews', [InterviewController::class, 'index'])->name('interviews.index');
    Route::get('/interviews/create', [InterviewController::class, 'create'])->name('interviews.create');
    Route::post('/interviews', [InterviewController::class, 'store'])->name('interviews.store');
    Route::get('/interviews/{interview}', [InterviewController::class, 'show'])->name('interviews.show');
    Route::post('/interviews/{interview}/evaluate', [InterviewController::class, 'evaluate'])->name('interviews.evaluate');
    Route::delete('/interviews/{interview}', [InterviewController::class, 'destroy'])->name('interviews.destroy');

    // Admin System Configuration Management
    Route::get('/config', [AdminConfigController::class, 'index'])->name('config.index');
    Route::put('/config/identity', [AdminConfigController::class, 'updateIdentity'])->name('config.identity.update');
    Route::post('/config/logo', [AdminConfigController::class, 'updateLogo'])->name('config.logo.update');
    Route::delete('/config/logo', [AdminConfigController::class, 'removeLogo'])->name('config.logo.destroy');
    Route::post('/config/departments', [AdminConfigController::class, 'storeDepartment'])->name('config.departments.store');
    Route::delete('/config/departments/{department}', [AdminConfigController::class, 'destroyDepartment'])->name('config.departments.destroy');
    Route::post('/config/backup', [AdminConfigController::class, 'createBackup'])->name('config.backup.create')->middleware('throttle:5,1');
    Route::get('/config/backup/{filename}', [AdminConfigController::class, 'downloadBackup'])->name('config.backup.download');
    Route::post('/config/restore', [AdminConfigController::class, 'restoreBackup'])->name('config.backup.restore')->middleware('throttle:5,1');
    Route::delete('/config/backup/{filename}', [AdminConfigController::class, 'deleteBackup'])->name('config.backup.destroy');
    Route::post('/config/clear-cache', [AdminConfigController::class, 'clearCache'])->name('config.cache.clear')->middleware('throttle:5,1');

    // Admin Assessments & Question Bank
    Route::resource('assessments', AssessmentController::class);
    Route::post('/assessments/{assessment}/questions', [AssessmentController::class, 'storeQuestion'])->name('assessments.questions.store');
    Route::delete('/assessments/{assessment}/questions/{question}', [AssessmentController::class, 'destroyQuestion'])->name('assessments.questions.destroy');

    // Admin Offerings Management
    Route::resource('offerings', OfferingController::class);
    Route::get('/offerings/{offering}/print', [OfferingController::class, 'printLetter'])->name('offerings.print');
    Route::post('/offerings/{offering}/send', [OfferingController::class, 'send'])->name('offerings.send');

    // Admin Onboardings Management
    Route::get('/onboardings', [OnboardingController::class, 'index'])->name('onboardings.index');
    Route::get('/onboardings/{onboarding}', [OnboardingController::class, 'show'])->name('onboardings.show');
    Route::post('/onboardings/{onboarding}/verify', [OnboardingController::class, 'verifyItem'])->name('onboardings.verify');
    Route::put('/onboardings/{onboarding}/status', [OnboardingController::class, 'updateStatus'])->name('onboardings.status.update');
});

Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'hr'])->name('dashboard');

    // HR Position / Vacancy Management
    Route::get('/positions', [PositionController::class, 'index'])->name('positions.index');
    Route::get('/positions/create', [PositionController::class, 'create'])->name('positions.create');
    Route::post('/positions', [PositionController::class, 'store'])->name('positions.store');
    Route::get('/positions/{position}', [PositionController::class, 'show'])->name('positions.show');
    Route::get('/positions/{position}/edit', [PositionController::class, 'edit'])->name('positions.edit');
    Route::put('/positions/{position}', [PositionController::class, 'update'])->name('positions.update');
    Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');

    // HR Candidate Applications Management (Halaman Terpisah)
    Route::get('/applications/export/excel', [AdminApplicationController::class, 'exportExcel'])->name('applications.export');
    Route::get('/applications/template/mass-status', [AdminApplicationController::class, 'downloadMassTemplate'])->name('applications.template');
    Route::post('/applications/upload-mass', [AdminApplicationController::class, 'uploadMassStatus'])->name('applications.upload-mass');
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/update-stage', [AdminApplicationController::class, 'updateStage'])->name('applications.update-stage');

    // HR Online Interview Sessions Management
    Route::get('/interviews', [InterviewController::class, 'index'])->name('interviews.index');
    Route::get('/interviews/create', [InterviewController::class, 'create'])->name('interviews.create');
    Route::post('/interviews', [InterviewController::class, 'store'])->name('interviews.store');
    Route::get('/interviews/{interview}', [InterviewController::class, 'show'])->name('interviews.show');
    Route::post('/interviews/{interview}/evaluate', [InterviewController::class, 'evaluate'])->name('interviews.evaluate');
    Route::delete('/interviews/{interview}', [InterviewController::class, 'destroy'])->name('interviews.destroy');

    // HR Assessments & Question Bank
    Route::resource('assessments', AssessmentController::class);
    Route::post('/assessments/{assessment}/questions', [AssessmentController::class, 'storeQuestion'])->name('assessments.questions.store');
    Route::delete('/assessments/{assessment}/questions/{question}', [AssessmentController::class, 'destroyQuestion'])->name('assessments.questions.destroy');

    // HR Offerings Management
    Route::resource('offerings', OfferingController::class);
    Route::get('/offerings/{offering}/print', [OfferingController::class, 'printLetter'])->name('offerings.print');
    Route::post('/offerings/{offering}/send', [OfferingController::class, 'send'])->name('offerings.send');

    // HR Onboardings Management
    Route::get('/onboardings', [OnboardingController::class, 'index'])->name('onboardings.index');
    Route::get('/onboardings/{onboarding}', [OnboardingController::class, 'show'])->name('onboardings.show');
    Route::post('/onboardings/{onboarding}/verify', [OnboardingController::class, 'verifyItem'])->name('onboardings.verify');
    Route::put('/onboardings/{onboarding}/status', [OnboardingController::class, 'updateStatus'])->name('onboardings.status.update');
});

Route::middleware(['auth', 'role:interviewer'])->prefix('interview')->name('interview.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'interview'])->name('dashboard');

    // Interviewer Candidate Applications Management (Halaman Terpisah)
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/export/excel', [AdminApplicationController::class, 'exportExcel'])->name('applications.export');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');

    // Interviewer Online Interview Sessions Management
    Route::get('/interviews', [InterviewController::class, 'index'])->name('interviews.index');
    Route::get('/interviews/create', [InterviewController::class, 'create'])->name('interviews.create');
    Route::post('/interviews', [InterviewController::class, 'store'])->name('interviews.store');
    Route::get('/interviews/{interview}', [InterviewController::class, 'show'])->name('interviews.show');
    Route::post('/interviews/{interview}/evaluate', [InterviewController::class, 'evaluate'])->name('interviews.evaluate');
    Route::delete('/interviews/{interview}', [InterviewController::class, 'destroy'])->name('interviews.destroy');
});

Route::middleware(['auth', 'role:kandidat'])->prefix('kadidat')->name('kadidat.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kadidat'])->name('dashboard');

    // Job Vacancies (Portal Karir & Melamar Pekerjaan)
    Route::get('/positions', [PositionController::class, 'index'])->name('positions.index');
    Route::get('/positions/{position}', [PositionController::class, 'show'])->name('positions.show');
    Route::post('/positions/{position}/apply', [PositionController::class, 'apply'])->name('positions.apply');

    // Candidate Profile & CV Management
    Route::get('/profile', [CandidateProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [CandidateProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/education', [CandidateProfileController::class, 'storeEducation'])->name('profile.education.store');
    Route::delete('/profile/education/{education}', [CandidateProfileController::class, 'destroyEducation'])->name('profile.education.destroy');
    Route::post('/profile/experience', [CandidateProfileController::class, 'storeExperience'])->name('profile.experience.store');
    Route::delete('/profile/experience/{experience}', [CandidateProfileController::class, 'destroyExperience'])->name('profile.experience.destroy');
    Route::post('/profile/skill', [CandidateProfileController::class, 'storeSkill'])->name('profile.skill.store');
    Route::delete('/profile/skill/{skill}', [CandidateProfileController::class, 'destroySkill'])->name('profile.skill.destroy');

    // Candidate Online Assessments
    Route::get('/assessments', [AssessmentController::class, 'candidateIndex'])->name('assessments.index');
    Route::get('/assessments/{assessment}/take', [AssessmentController::class, 'candidateTake'])->name('assessments.take');
    Route::post('/assessments/{assessment}/submit', [AssessmentController::class, 'candidateSubmit'])->name('assessments.submit')->middleware('throttle:10,1');
    Route::get('/assessments/results/{result}', [AssessmentController::class, 'candidateResult'])->name('assessments.result');

    // Candidate Job Offerings
    Route::get('/offerings', [OfferingController::class, 'candidateIndex'])->name('offerings.index');
    Route::get('/offerings/{offering}', [OfferingController::class, 'candidateShow'])->name('offerings.show');
    Route::get('/offerings/{offering}/print', [OfferingController::class, 'printLetter'])->name('offerings.print');
    Route::post('/offerings/{offering}/respond', [OfferingController::class, 'candidateRespond'])->name('offerings.respond');

    // Candidate Employee Onboarding
    Route::get('/onboarding', [OnboardingController::class, 'candidateIndex'])->name('onboarding.index');
    Route::post('/onboarding/{onboarding}/submit', [OnboardingController::class, 'candidateSubmit'])->name('onboarding.submit');
});

// Shared & Aliases for convenience
Route::middleware(['auth'])->group(function () {
    Route::get('/offerings/{offering}/print', [OfferingController::class, 'printLetter'])->name('offerings.print');
    Route::get('/kandidat/dashboard', function () {
        return redirect()->route('kadidat.dashboard');
    })->name('kandidat.dashboard');

    Route::get('/interviewer/dashboard', function () {
        return redirect()->route('interview.dashboard');
    })->name('interviewer.dashboard');
});
