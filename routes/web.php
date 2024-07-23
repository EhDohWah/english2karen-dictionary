<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\WordController;

Route::get('/search', [WordController::class, 'searchIndex']);
Route::get('/create/index', [WordController::class, 'createIndex']);


// Route::get('/words/create', [WordController::class, 'create'])->name('words.create');
// Route::post('/words', [WordController::class, 'store'])->name('words.store');


Route::get('/', function () {
    return view('welcome');
});
