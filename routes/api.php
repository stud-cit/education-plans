<?php

use App\ExternalServices\Asu\Profession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AsuController,
    AuthController,
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
    PositionController};

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
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login');
    Route::get('/auth', [AuthController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('cycles', CycleController::class);
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
        Route::apiResource('positions', PositionController::class);
        Route::apiResource('signatures', SignatureController::class)
            ->only('store', 'update', 'destroy');

        Route::get('/departments/{id}', [AsuController::class, 'departmentById'])->name('asu.department.show');
        Route::get('/faculties', [AsuController::class, 'faculties'])->name('asu.faculty');
        Route::get('/specialities/{id}', [AsuController::class, 'getSpecialities'])->name('asu.specialities');
        Route::get('/specializations/{id}', [AsuController::class, 'getSpecializations'])->name('asu.specializations');
        Route::get('/education-programs/{id}', [AsuController::class, 'getEducationPrograms'])->name('asu.education-programs');
        Route::get('/subjects', [AsuController::class, 'getSubjects'])->name('asu.subjects');

        Route::get('/user', function (Request $request) {
            return $request->user()->makeHidden(['asu_id','created_at','updated_at']);
        });
        Route::get('/userName', function (Request $request) {
            return response()->json(['userName' => $request->user()->name]);
        });
        Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    });
    Route::get('/test', function (Request $request) {
        $model = new \App\ExternalServices\Asu\Profession();
        // 319
        // 427
         $data = $model->getFieldKnowledge();
//         $data = $model->getSpecialization(319);
//         $data = $model->getFieldKnowledge();
    //     $data = $model->getQualifications();

        return response()->json($data);
    });
});


