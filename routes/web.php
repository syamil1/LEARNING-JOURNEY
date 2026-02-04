<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\IntroductionController;
use App\Http\Controllers\Admin\ChecklistController as AdminChecklistController;
use App\Http\Controllers\User\ChecklistController as UserChecklistController;
use App\Http\Controllers\Admin\MentoringController as AdminMentoringController;
use App\Http\Controllers\User\MentoringController as UserMentoringController;
use App\Http\Controllers\Admin\EmployeeTrainingScoreController;
use App\Http\Controllers\Admin\EmployeeEvaluationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ChecklistController as OnboardingChecklistController;
use App\Http\Controllers\Admin\EmployeeReportController;

use Illuminate\Support\Facades\Route;
    Route::get('/', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // DASHBOARD
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // EMPLOYEES
        Route::get('search/employees',[EmployeeController::class, 'search'])->name('search.employees');
        Route::resource('employees', EmployeeController::class);
        Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
        Route::get('employees/template-csv', [EmployeeController::class, 'downloadTemplate'])->name('employees.template');

        // STORES
        Route::resource('stores', StoreController::class);
        Route::post('stores/{store}/reset-password', [StoreController::class, 'resetPassword'])->name('stores.reset-password');

        // INTRODUCTIONS
        Route::resource('introductions', IntroductionController::class);
        Route::post('introductions/import', [IntroductionController::class, 'import'])->name('introductions.import');

        // EVALUATIONS
        Route::get('evaluations/export', [EmployeeEvaluationController::class, 'export'])->name('evaluations.export');
        Route::post('employee-evaluations/import/{period}', [EmployeeEvaluationController::class, 'import'])->name('employee-evaluations.import');
        Route::post('employee-evaluations/import-assessment', [EmployeeEvaluationController::class, 'importAssessment'])->name('employee-evaluations.import-assessment');
        Route::resource('evaluations', EmployeeEvaluationController::class);
        Route::get('evaluations/template/{type}',[EmployeeEvaluationController::class, 'downloadTemplate'])->name('evaluations.template');

                
        // DEVELOPMENT (TRAINING SCORE)
        Route::resource('development', EmployeeTrainingScoreController::class);
        Route::post('development/import', [EmployeeTrainingScoreController::class, 'import'])->name('development.import');

        // CHECKLIST
        Route::get('checklist', [AdminChecklistController::class, 'index'])->name('checklist.index');
        Route::get('checklist/{id}', [AdminChecklistController::class, 'show'])->name('checklist.show');
        Route::put('checklist/{id}/status', [AdminChecklistController::class, 'updateStatus'])->name('checklist.updateStatus');
        Route::post('checklist/month/update-status', [AdminChecklistController::class, 'updateMonthStatus'])->name('checklist.updateMonthStatus');
        Route::get('checklist/summary/{employee}', [AdminChecklistController::class, 'summary'])->name('checklist.summary');

        // MENTORING
        Route::get('mentoring', [AdminMentoringController::class, 'index'])->name('mentoring.index');
        Route::get('mentoring/{employee_id}', [AdminMentoringController::class, 'show'])->name('mentoring.show');
        Route::patch('mentoring/verify/{id}', [AdminMentoringController::class, 'verify'])->name('mentoring.verify');

        // VIEW REPORT (WEB)
        Route::get('/employees/{employee}/report',[EmployeeReportController::class, 'show'])->name('employees.report.show');

        // DOWNLOAD PDF
        Route::get('/employees/{employee}/report/pdf',[EmployeeReportController::class, 'pdf'])->name('employees.report.pdf');
    });


    Route::prefix('user')->middleware(['auth', 'role:user'])->name('user.')->group(function () {

        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

        Route::get('/mentoring', [UserMentoringController::class, 'index'])->name('mentoring.index');
        Route::get('/mentoring/create', [UserMentoringController::class, 'create'])->name('mentoring.create');
        Route::post('/mentoring', [UserMentoringController::class, 'store'])->name('mentoring.store');

        Route::get('/onboarding-checklist', [OnboardingChecklistController::class, 'index'])->name('onboarding.checklist.index');
        Route::get(  '/onboarding-checklist/{employeeId}/{month}/{week}',[UserChecklistController::class, 'show'])->name('onboarding.checklist.show');
        Route::post('/onboarding-checklist/{employeeId}/{month}/{week}',[UserChecklistController::class, 'store'])->name('onboarding.checklist.store');
        });


});




require __DIR__.'/auth.php';
