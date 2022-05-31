<?php

use App\ExternalServices\Asu\Professions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AsuController,
    AuthController,
    CycleController,
    EducationLevelController,
    FormStudyController,
    PlanController,
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
    VerificationController
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::apiResource('cycles', CycleController::class);

    Route::get('/auth', [AuthController::class, 'index']);

    Route::patch('/plans/verification/{plan}', [PlanController::class, 'verification'])->name('plans.verification.store');
    Route::post('/plans/copy/{plan}', [PlanController::class, 'copy'])->name('plans.copy');
    Route::post('/plans/cycle/{plan}', [PlanController::class, 'cycleStore'])->name('plans.cycle.store');
    Route::patch('/plans/{plan}/cycles/{cycle}', [PlanController::class, 'cycleUpdate'])->name('plans.cycle.update');
    Route::delete('/plans/{plan}/cycles/{cycle}', [PlanController::class, 'cycleDestroy'])->name('plans.cycle.destroy');
    Route::get('/plans/cycles/{plan}', [PlanController::class, 'cyclesWithSubjects'])->name('plans.cycles.subjects');
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
    Route::apiResource('study-terms', StudyTermController::class);
    Route::apiResource('settings', SettingController::class);
    Route::get('/departments/{id}', [AsuController::class, 'departmentById'])->name('asu.department.show');
    Route::get('/faculties', [AsuController::class, 'faculties'])->name('asu.faculty');
    Route::get('/specialization/{id}', [AsuController::class, 'getSpecialization'])->name('asu.specialization');

    Route::get('/test', function (Request $request) {
        $model = new Professions();
        // 319
        // 427
//         $data = $model->getSpecialty();
         $data = $model->getSpecialization(319);
//         $data = $model->getFieldKnowledge();

        return response()->json($data);
    });
});


