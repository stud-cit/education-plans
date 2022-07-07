<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlanController;

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

Route::get('/', function () {

    $params = [
        'key' => 'SyZAuCrz6zjJsxFeRfQQdsU7oSSsuYWlBHWRLlQSd7ONolzvCU49',
        'token' => config('app.cabinet_app_token'),
    ];

    clock("Authorization: {$params['key']}");
    clock("Token: {$params['token']}");

    $response = Http::retry(3, 100)->get('https://cabinet.sumdu.edu.ua/api/getPersonInfo', $params)->json();
    clock($response);
    return view('welcome');
});

// test auth
Route::get('/register', [AuthController::class, 'register']);
// Route::get('/logout', [AuthController::class, 'logout']);

