<?php

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PdfController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if (config('app.debug')) {
    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/pdf/{id}', [PdfController::class, 'pdfview']);

    Route::get('/catalogs', function () {
        DB::table('catalog_subjects')
            ->whereNotNull('catalog_education_level_id')
            ->where('catalog_education_level_id', 3)
            ->update(['catalog_education_level_id' => 8]);

        DB::table('catalog_subjects')
            ->whereNotNull('catalog_education_level_id')
            ->where('catalog_education_level_id', 2)
            ->update(['catalog_education_level_id' => 4]);

        DB::table('catalog_subjects')
            ->whereNotNull('catalog_education_level_id')
            ->where('catalog_education_level_id', 1)
            ->increment('catalog_education_level_id');
    });

    Route::get('/subjects', function () {

        DB::table('catalog_selective_subjects')
            ->whereNotNull('catalog_education_level_id')
            ->where('catalog_education_level_id', 3)
            ->update(['catalog_education_level_id' => 8]);

        DB::table('catalog_selective_subjects')
            ->whereNotNull('catalog_education_level_id')
            ->where('catalog_education_level_id', 2)
            ->update(['catalog_education_level_id' => 4]);

        DB::table('catalog_selective_subjects')
            ->whereNotNull('catalog_education_level_id')
            ->where('catalog_education_level_id', 1)
            ->increment('catalog_education_level_id');
    });

    Route::get('/fix', function () {
        DB::table('catalog_subjects')->select('*')
            ->where('selective_discipline_id', 3)
            ->where('education_program_id', '!=', null)
            ->update(['speciality_id' => null]);
    });
}
