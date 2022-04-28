<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AsuController,
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
    UserController
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
    Route::get('/cycles/subindex/{cycle}', [CycleController::class, 'subIndex'])->name('cycles.sub.index');
    Route::post('/cycles/sub-store', [CycleController::class, 'subStore'])->name('cycles.sub.store');
    Route::patch('/cycles/sub-update/{cycle}', [CycleController::class, 'subUpdate'])->name('cycles.sub.update');
    Route::apiResource('cycles', CycleController::class);
    Route::post('/plans/copy/{plan}', [PlanController::class, 'copy'])->name('plans.copy');
    Route::Resource('plans', PlanController::class);
    Route::apiResource('form-studies', FormStudyController::class);
    Route::apiResource('form-organization', FormOrganizationController::class);
    Route::apiResource('education-levels', EducationLevelController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('form-controls', FormControlController::class);
    Route::apiResource('individual-tasks', IndividualTaskController::class);
    Route::apiResource('hours-weeks', HoursWeekController::class);
    Route::apiResource('selective-discipline', SelectiveDisciplineController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('users', UserController::class);
    Route::get('/study-terms/select', [StudyTermController::class, 'select'])->name('study-terms.select');
    Route::apiResource('study-terms', StudyTermController::class);
    Route::apiResource('settings', SettingController::class);
    Route::get('/departments/{id}', [AsuController::class, 'departmentById'])->name('asu.department.show');
    Route::get('/faculties', [AsuController::class, 'faculty'])->name('asu.faculty');

    Route::get('/test', function (Request $request) {
        $data = __('messages.Updated');
        //$model = App\Models\Plan::with('cycles')->find(18);
        //$data = $model->cycles;
        return response()->json($data);
    });
});


