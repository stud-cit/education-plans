<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CycleController,
    EducationLevelController,
    FormStudyController,
    PlanController,
    SubjectController,
    HoursWeekController,
    FormControlController,
    IndividualTaskController,
    SelectiveDisciplineController


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
    Route::apiResource('plans', PlanController::class);
    Route::apiResource('form-studies', FormStudyController::class);
    Route::apiResource('education-levels', EducationLevelController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('form-controls', FormControlController::class);
    Route::apiResource('individual-tasks', IndividualTaskController::class);
    Route::apiResource('hours-weeks', HoursWeekController::class);
    Route::apiResource('selective-discipline', SelectiveDisciplineController::class);

    

    Route::get('/test', function (Request $request) {
        $data = __('messages.Updated');
        //$model = App\Models\Plan::with('cycles')->find(18);
        //$data = $model->cycles;
        return response()->json($data);
    });
});


