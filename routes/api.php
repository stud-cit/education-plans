<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AsuController,
    CycleController,
    EducationLevelController,
    FormStudyController,
    PlanController,
    SignatureController,
    SubjectController,
    HoursWeekController,
    FormControlController,
    IndividualTaskController,
    SelectiveDisciplineController,
    SettingController,
    StudyTermController,
    FormOrganizationController,
    RoleController,
    UserController,
    VerificationController,
    LoginController,
    NoteController,
    PositionController,
    ListCycleController,
    OpController,
    UserActivityController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/cabinet-service', \App\Http\Controllers\CabinetServiceController::class);

Route::prefix('v1')->group(function () {
    Route::middleware('cabinetAuth')->group(function () {
        Route::apiResource('cycles', CycleController::class);
        Route::patch('/plans/verification/{plan}', [PlanController::class, 'verification'])->name('plans.verification.store');
        Route::patch('/plans/verification-op/{plan}', [PlanController::class, 'verificationOP'])
            ->name('plans.verificationOP.store');
        Route::post('/plans/copy/{plan}', [PlanController::class, 'copy'])->name('plans.copy')
            ->middleware('can:copy-plan');
        Route::post('/plans/cycle/{plan}', [PlanController::class, 'cycleStore'])->name('plans.cycle.store');
        Route::patch('/plans/{plan}/cycles/{cycle}', [PlanController::class, 'cycleUpdate'])->name('plans.cycle.update');
        Route::delete('/plans/{plan}/cycles/{cycle}', [PlanController::class, 'cycleDestroy'])->name('plans.cycle.destroy');
        Route::get('/plans/cycles/{plan}', [PlanController::class, 'cyclesWithSubjects'])->name('plans.cycles.subjects');
        Route::get('plans/additional-data', [PlanController::class, 'additionalDataActionsPlan']);
        Route::get('plans/filters', [PlanController::class, 'getItemsFilters']);
        Route::Resource('plans', PlanController::class);

        Route::apiResource('verifications', VerificationController::class);
        Route::apiResource('form-studies', FormStudyController::class);
        Route::apiResource('form-organizations', FormOrganizationController::class);
        Route::apiResource('education-levels', EducationLevelController::class);
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('form-controls', FormControlController::class);
        Route::apiResource('individual-tasks', IndividualTaskController::class);
        Route::apiResource('hours-weeks', HoursWeekController::class);
        Route::apiResource('selective-discipline', SelectiveDisciplineController::class);
        Route::apiResource('roles', RoleController::class);
        Route::get('workers', [UserController::class, 'workers'])->name('users.workers');
        Route::get('faculty-by-worker', [UserController::class, 'getFacultyByWorker'])->name('users.faculty.worker');
        Route::apiResource('users', UserController::class);
        Route::get('/study-terms/select', [StudyTermController::class, 'select'])->name('study-terms.select');
        Route::apiResource('study-terms', StudyTermController::class)->middleware('can:manage_study_terms');
        Route::apiResource('settings', SettingController::class);
        Route::apiResource('positions', PositionController::class);
        Route::apiResource('signatures', SignatureController::class)
            ->only('store', 'update', 'destroy');
        Route::get('notes/rules', [NoteController::class, 'rules'])->name('notes.rules');
        Route::apiResource('notes', NoteController::class)->except('show');
        Route::apiResource('list-cycles', ListCycleController::class)->only('index');

        Route::get('/departments/{id}', [AsuController::class, 'departmentById'])->name('asu.department.show');
        Route::get('/faculties', [AsuController::class, 'faculties'])->name('asu.faculty');
        Route::get('/specialities/{id}', [AsuController::class, 'getSpecialities'])->name('asu.specialities');
        Route::get('/specializations/{id}', [AsuController::class, 'getSpecializations'])->name('asu.specializations');
        Route::get('/education-programs/{id}', [AsuController::class, 'getEducationPrograms'])->name('asu.education-programs');
        Route::get('/subjects', [AsuController::class, 'getSubjects'])->name('asu.subjects');
        Route::get('/programs', [OpController::class, 'programs'])->name('op.programs');

        Route::get('/user', function (Request $request) {
            return \Illuminate\Support\Facades\Auth::user()->makeHidden('asu_id');
        });

        Route::get('/userName', function (Request $request) {
            return response()->json(['userName' => $request->user()->name]);
        });
        Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('/user-activity', [UserActivityController::class, 'index'])->name('user-activity.index');
    });
});
