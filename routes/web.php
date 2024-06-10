<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/',[DashboardController::class,'index'])->name('index');

//Rutas protegidas
Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'welcome'])->name('dashboard.welcome');
    Route::get('/syncUsers', [DashboardController::class, 'syncUsers'])->name('dashboard.sync');

    //Users
    Route::get('/assessments/{respondentId}', [UserController::class, 'getAssessment'])->name('users.assessments');
    Route::post('/reportassessment', [UserController::class, 'getReportAssessment'])->name('users.report');
    Route::resource('users',UserController::class);

    //SuperLink
    Route::get('/superlink/{email}/{idTemplate}', [DashboardController::class, 'superLink'])->name('dashboard.superlink');

    Route::get('getRespondents',[ApiController::class,'getRespondents'])->name('api.getRespondents');

    //Pruebas
    Route::get('/test/create', [TestController::class, 'create'])->name('test.create');
    Route::get('/test/results', [TestController::class, 'viewResult'])->name('test.results');
    Route::get('/test/settings', [TestController::class, 'settings'])->name('test.settings');

    //Token
    Route::get('getToken',[TokenController::class,'getToken'])->name('get.token');
    Route::get('expirationToken',[TokenController::class,'expirationToken'])->name('get.expirationToken');
    Route::get('refreshToken',[TokenController::class,'refreshToken'])->name('get.refreshToken');
});

Auth::routes();
