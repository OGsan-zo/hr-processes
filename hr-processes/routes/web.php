<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/candidats/create', [CandidatController::class, 'create'])->name('candidats.create');
Route::post('/candidats', [CandidatController::class, 'store'])->name('candidats.store');
