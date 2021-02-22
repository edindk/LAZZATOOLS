<?php


use App\Http\Controllers\ApiCredentialsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WhoisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;

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

// Whois routes
Route::post('whois/credentials/store', [ApiCredentialsController::class, 'insertWhoisXmlApiCredentials']);
Route::post('whois/store', [WhoisController::class, 'storeWhois']);
Route::get('whois/all', [WhoisController::class, 'getAllWhoisRecords']);
