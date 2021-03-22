<?php


use App\Http\Controllers\ApiCredentialsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\WhoisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\FileController;

/*
|--------------------------------------------------------------------------
| api routes
|--------------------------------------------------------------------------
|
| here is where you can register api routes for your application. these
| routes are loaded by the routeserviceprovider within a group which
| is assigned the "api" middleware group. enjoy building your api!
|
*/

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    // Whois routes
    Route::post('whois/credentials/store', [ApiCredentialsController::class, 'insertWhoisXmlApiCredentials']);
    Route::post('whois/store', [WhoisController::class, 'storeWhois']);
    Route::post('whois/delete', [WhoisController::class, 'deleteDomain']);
    Route::get('whois/all', [WhoisController::class, 'getAllWhoisRecords']);
    Route::post('whois/statuscode', [WhoisController::class, 'getStatusCode']);
    Route::get('whois/updateAll', [WhoisController::class, 'updateAllWhoisRecords']);
    Route::post('whois/updatewhois', [WhoisController::class, 'updateWhoisRecord']);

// City routes
    Route::Get('city/all', [CityController::class, 'getCities']);
});

//Verification routes
Route::get('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/verification', [AuthController::class, 'sendVerification']);

// Auth routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::middleware('auth:api')->post('logout', 'AuthController@logout');

// Forgot password routes
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');

// Insert WhoisBulkRecords
Route::get('whois/bulk', [WhoisController::class, 'insertWhoisRecordsBulk']);

// Upload sheet files
Route::post('upload', [FileController::class, 'uploadFiles']);
