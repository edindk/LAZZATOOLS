<?php

use App\Http\Controllers\ApiCredentialsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\WhoisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\CityController;

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
Auth::routes(['verify' => true]);
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});
// Auth routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::middleware('auth:api')->post('logout', 'AuthController@logout');

// Forgot password routes
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');

// Email verification
Route::get('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.resend');

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
