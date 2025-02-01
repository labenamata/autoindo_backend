<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JaringController;
use App\Http\Controllers\KoinController;
use App\Http\Controllers\KredentialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StateController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('user', [AuthController::class, 'getUser']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/getpairs', [KoinController::class, 'fetchAndSavePairs']);
Route::get('/koin', [KoinController::class, 'getAllKoins']);
Route::get('/koin/{currency}', [KoinController::class, 'getKoin']);
Route::get('/koin/{currency}/{name}', [KoinController::class, 'filterKoin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/kredentials', [KredentialController::class, 'create']);    // Create
    Route::get('/kredentials', [KredentialController::class, 'index']);      // Read all
    Route::put('/kredentials', [KredentialController::class, 'update']);     // Update
    Route::delete('/kredentials', [KredentialController::class, 'destroy']); // Delete
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/jaring', [JaringController::class, 'create']);
    Route::post('/jaring/batch', [JaringController::class, 'createBatch']);
    Route::get('/jaring', [JaringController::class, 'index']);
    Route::get('/jaring/{id}', [JaringController::class, 'search']);
    Route::put('/jaring', [JaringController::class, 'update']);
    Route::delete('/jaring', [JaringController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/bstate', [StateController::class, 'store']);
    Route::get('/bstate', [StateController::class, 'index']);
    Route::put('/bstate', [StateController::class, 'update']);
    Route::delete('/bstate', [StateController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::get('/profile/histori', [ProfileController::class, 'getOrderHistori']);
    Route::post('/profile/order', [ProfileController::class, 'getOrder']);
    Route::get('/profile/status', [ProfileController::class, 'getStatus']);
});
