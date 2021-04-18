<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KostController;
use App\Http\Controllers\AvailabilityController;
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

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/kost/list', [KostController::class, 'getKostByOwnerId']);
    Route::post('/kost/create', [KostController::class, 'insert']);
    Route::put('/kost/edit/{id}', [KostController::class, 'update']);
    Route::delete('/kost/delete/{id}', [KostController::class, 'destroy']);
    
    Route::post('/kost/{kostId}/availability/ask', [AvailabilityController::class, 'askRoomAvailability']);
    
    Route::get('/availability', [AvailabilityController::class, 'index']);
    Route::get('/kost/{kostId}/availability', [AvailabilityController::class, 'show']);
    Route::post('/kost/{kostId}/availability/give', [AvailabilityController::class, 'giveRoomAvailability']);

    Route::post('/auth/logout', [AuthController::class, 'logout']);
});

Route::get('/kost', [KostController::class, 'index']);
Route::get('/kost/{id}', [KostController::class, 'show']);
Route::post('/kost/search', [KostController::class, 'find']);
