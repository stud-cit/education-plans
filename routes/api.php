<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AsuController,
    CatalogHelperTypeController,
    CycleController,
    CatalogGroupController,
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
    SubjectHelperController,
    UserController,
    VerificationController,
    LoginController,
    NoteController,
    PositionController,
    ListCycleController,
    OpController,
    SubjectLanguageController,
    UserActivityController,
    CatalogSubjectController,
    CatalogSelectiveSubjectController,
    CatalogSpecialityController,
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

        Route::get('/verifications', [VerificationController::class, 'index']);
        Route::get('/verification-subject-statuses', [VerificationController::class, 'getVerificationSubjectStatuses']);

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
        Route::get('list-workers', [UserController::class, 'listWorkers'])->name('users.list-workers');
        Route::get('faculty-by-worker', [UserController::class, 'getFacultyByWorker'])->name('users.faculty.worker');
        Route::apiResource('users', UserController::class);
        Route::get('/study-terms/select', [StudyTermController::class, 'select'])->name('study-terms.select');
        Route::apiResource('study-terms', StudyTermController::class)->middleware('can:manage-study-terms');
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
        Route::get('/education-programs', [AsuController::class, 'getAllEducationPrograms']);
        Route::get('/specialities', [AsuController::class, 'getAllSpecialities']);

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
        Route::apiResource('subject-languages', SubjectLanguageController::class);
        Route::apiResource('subject-helpers', SubjectHelperController::class);
        Route::apiResource('catalog-helper-types', CatalogHelperTypeController::class)->only('index');

        Route::get('catalog-groups/list', [CatalogGroupController::class, 'list']);
        Route::patch('catalog-groups/restore/{id}', [CatalogGroupController::class, 'restore'])->middleware('can:restore-catalog-group');
        Route::apiResource('catalog-groups', CatalogGroupController::class);

        Route::get('catalog-subjects/years', [CatalogSubjectController::class, 'getYears']);
        Route::get('catalog-subjects/catalog-titles', [CatalogSubjectController::class, 'getCatalogs']);
        Route::get('/catalog-subjects/generate-pdf', [CatalogSubjectController::class, 'generateSubjectsPDF']);
        Route::apiResource('catalog-subjects', CatalogSubjectController::class);

        Route::patch('/catalog-selective-subjects/verification/{catalog_selective_subject}', [
            CatalogSelectiveSubjectController::class, 'verification'
        ]);
        Route::patch('/catalog-selective-subjects/toggle-to-verification/{catalog_selective_subject}', [
            CatalogSelectiveSubjectController::class, 'toggleToVerification'
        ]);
        Route::get('catalog-selective-subjects/filters', [CatalogSelectiveSubjectController::class, 'getItemsFilters']);
        Route::Resource('catalog-selective-subjects', CatalogSelectiveSubjectController::class);
        Route::Resource('catalog-specialties', CatalogSpecialityController::class);
    });
});
