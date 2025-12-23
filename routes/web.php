<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileUploadTestController;
use App\Http\Controllers\OpenAIConfigController;
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
    Route::get('/import', [DashboardController::class, 'import'])->name('dashboard.import');
    Route::post('/import/process', [DashboardController::class, 'import_process'])->name('dashboard.process.import');
    Route::post('/import/map', [DashboardController::class, 'import_map'])->name('dashboard.import.map');
    Route::get('/import/template', [DashboardController::class, 'download_template'])->name('dashboard.download.template');
    Route::get('/import/template/admin', [DashboardController::class, 'download_admin_template'])->name('dashboard.download.template.admin');
    Route::get('/import/template/institution', [DashboardController::class, 'download_institution_template'])->name('dashboard.download.template.institution');
    Route::get('/import/summary', [DashboardController::class, 'import_summary'])->name('dashboard.import.summary');
    Route::post('remove-notification', [DashboardController::class, 'remove_notification'])->name('dashboard.remove.notification');

    Route::post('category/updateNow', [CategoryController::class, 'assignCategory'])->name('category.updateNow');
    Route::post('category/get', [CategoryController::class, 'getCategory'])->name('category.get');
    Route::put('category/update/new', [CategoryController::class, 'updateCategory'])->name('category.updateNoww');
    Route::post('category/delete', [CategoryController::class, 'deleteCategory'])->name('category.delete');
    Route::resource('category', CategoryController::class);
    
    Route::get('/storage-link', function() {
        Artisan::call('storage:link');
        return response()->json(['message' => 'Storage link created successfully']);
    });
    
    //Tools
    Route::get('/test-api', [TokenController::class, 'testConnection'])->name('test.api');
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
        Artisan::call('optimize:clear');
        return response()->json(['message' => 'Se han limpiado todas las caches.']);
    });

    //Users
    Route::post('users/email/welcome', [UserController::class, 'sendEmailWelcome'])->name('users.email.welcome');
    Route::resource('users',UserController::class);
    
    //OpenAI Config - Solo para administradores
    Route::group(['middleware' => ['role:administrator']], function () {
        Route::get('/openai/config', [OpenAIConfigController::class, 'index'])->name('openai.config');
        Route::post('/openai/config', [OpenAIConfigController::class, 'store'])->name('openai.config.store');
    });

    //Reports
    Route::post('/reports/results', [ReportController::class, 'getReportAssessments'])->name('report.results');

    //Assessments
    Route::get('/assessment/{respondentId?}', [AssessmentController::class, 'getAssessments'])->name('assessments.index');
    Route::get('/assessment/welcome', [AssessmentController::class, 'welcome'])->name('assessments.welcome');
    Route::get('/assessment/start/{id}/{token}/{lang}', [AssessmentController::class, 'startEvaluate'])->name('assessments.start');
    Route::get('/assessment/continue/{userId}/{id}/{token}/{lang}', [AssessmentController::class, 'continueEvaluate'])->name('assessments.continue');
    Route::post('/assessment/update', [AssessmentController::class, 'updateAnswersAssessment'])->name('assessments.update');
    Route::post('/assessment/new', [AssessmentController::class, 'newEvaluation'])->name('assessments.new');
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

// Ruta de diagnóstico de subida de archivos
Route::get('/upload-test', [FileUploadTestController::class, 'test'])->name('upload.test');
Route::post('/upload-test/manual', [FileUploadTestController::class, 'testManual'])->name('upload.test.manual');
