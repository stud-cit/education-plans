<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CycleController,
    EducationLevelController,
    FromStudyController,
    PlanController,

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

Route::get('/cycles/subindex/{cycle}', [CycleController::class, 'subIndex'])->name('cycles.sub.index');
Route::post('/cycles/sub-store', [CycleController::class, 'subStore'])->name('cycles.sub.store');
Route::patch('/cycles/sub-update/{cycle}', [CycleController::class, 'subUpdate'])->name('cycles.sub.update');
Route::apiResource('cycles', CycleController::class);
Route::apiResource('plans', PlanController::class);
Route::apiResource('form-studies', FromStudyController::class);
Route::apiResource('education-levels', EducationLevelController::class);

Route::get('/test', function (Request $request) {
    $asu = new \App\ExternalServices\ASU();
    $data = $asu->getNameFacultyById(414);
    return response()->json($data);
});


