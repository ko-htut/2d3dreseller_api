<?php

use App\Http\Controllers\Api as ApiController;
use Illuminate\Support\Facades\Route;

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

// Authentication Routes
Route::post('register', [ApiController\Auth\LoginController::class, 'register']);
Route::post('login', [ApiController\Auth\LoginController::class, 'login']);
Route::post('logout', [ApiController\Auth\LoginController::class, 'logout'])->middleware('auth:sanctum');

// Essentials Data Routes
Route::get('essentials', [ApiController\EssentialController::class, 'get']);

Route::get('user', [ApiController\UserController::class, 'user']);

// Register Routes
Route::apiResource('registers', ApiController\RegisterController::class)->except([
    'update', 'destroy', 'show'
]);

Route::get('registers/current', [ApiController\RegisterController::class, 'current']);
Route::get('registers/current/bet', [ApiController\RegisterController::class, 'list']);
Route::get('registers/current/total', [ApiController\RegisterController::class, 'total']);

Route::post('registers/current/bet', [ApiController\RegisterController::class, 'bet']);
Route::post('registers/current/close', [ApiController\RegisterController::class, 'currentRegisterClose']);

Route::get('/registers/{register}/bets', [ApiController\RegisterController::class, 'betsList']);
Route::post('registers/{register}/close', [ApiController\RegisterController::class, 'close']);

Route::post('twod/history', [ApiController\TwoDController::class, 'history']);
Route::post('twod/live/check', [ApiController\TwoDController::class, 'check2DLive']);

Route::post('voucher', [ApiController\VoucherController::class, 'index']);
Route::post('voucher/open', [ApiController\VoucherController::class, 'store']);
Route::post('voucher/close', [ApiController\VoucherController::class, 'update']);
Route::post('voucher/current', [ApiController\VoucherController::class , 'current']);
Route::post('voucher/bet', [ApiController\VoucherController::class, 'bet']);