<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\WordController;

Route::get('/words', [WordController::class, 'index']);
Route::post('/words', [WordController::class, 'store']);
Route::put('/words/{id}', [WordController::class, 'update']);
Route::delete('/words/{id}', [WordController::class, 'destroy']);



Route::post('/search', [WordController::class, 'search'])->name('api.words.search');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
