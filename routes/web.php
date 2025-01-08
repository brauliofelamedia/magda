<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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

Route::get('/home', function () {
    return redirect()->route('dashboard.welcome');
});

Route::get('/',[DashboardController::class,'index'])->name('index');

//Rutas protegidas
Route::post('users/reset', [UserController::class, 'resetPassword'])->name('users.password.reset');

Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'welcome'])->name('dashboard.welcome');
    Route::get('/syncUsers', [DashboardController::class, 'syncUsers'])->name('dashboard.sync');
    Route::post('remove-notification', [DashboardController::class, 'remove_notification'])->name('dashboard.remove.notification');

    //Tools
    Route::get('/migrate', function(){
        Artisan::call('migrate');
        return response()->json(['message' => 'Migraciones ejecutadas correctamente.']);
    });

    //Limpia todos los tipos de caché
    Route::get('/clean', function(){
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        return response()->json(['message' => 'Se han limpiado todas las caches.']);
    });

    //Users
    Route::post('users/email/welcome', [UserController::class, 'sendEmailWelcome'])->name('users.email.welcome');
    Route::resource('users',UserController::class);

    //Reports
    Route::post('/reports/results', [ReportController::class, 'getReportAssessments'])->name('report.results');

    //Assessments
    Route::get('/assessment/{respondentId?}', [AssessmentController::class, 'getAssessments'])->name('assessments.index');
    Route::get('/assessment/welcome', [AssessmentController::class, 'welcome'])->name('assessments.welcome');
    Route::get('/assessment/start/{id}/{token}/{lang}', [AssessmentController::class, 'startEvaluate'])->name('assessments.start');
    Route::get('/assessment/continue/{userId}/{id}/{token}/{lang}', [AssessmentController::class, 'continueEvaluate'])->name('assessments.continue');
    Route::post('/assessment/update', [AssessmentController::class, 'updateAnswersAssessment'])->name('assessments.update');
    Route::get('/assessment/new/{id}/{lang}', [AssessmentController::class, 'newEvaluation'])->name('assessments.new');
    Route::post('/assessment/user/new', [AssessmentController::class, 'createNewUser'])->name('assessments.user.new');
    Route::post('/assessment/close', [AssessmentController::class, 'closeAssessment'])->name('assessments.close');
    Route::get('/assessment/finish/{id}', [AssessmentController::class, 'finish'])->name('assessments.finish');

    //SuperLink
    Route::get('/superlink/{email}/{idTemplate}', [DashboardController::class, 'superLink'])->name('dashboard.superlink');
    Route::get('getRespondents',[ApiController::class,'getRespondents'])->name('api.getRespondents');

    //Token test
    Route::get('getToken',[TokenController::class,'getToken'])->name('get.token');
    Route::get('expirationToken',[TokenController::class,'expirationToken'])->name('get.expirationToken');
    Route::get('refreshToken',[TokenController::class,'refreshToken'])->name('get.refreshToken');
});

Auth::routes();
