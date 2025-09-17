<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\CandidatureController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/candidats/create', [CandidatController::class, 'create'])->name('candidats.create');
Route::post('/candidats', [CandidatController::class, 'store'])->name('candidats.store');
Route::get('/candidats', [CandidatController::class, 'index'])->name('candidats.index');

Route::get('/employes/create', [EmployeController::class, 'create'])->name('employes.create');
Route::post('/employes', [EmployeController::class, 'store'])->name('employes.store');

Route::resource('annonces', AnnonceController::class);

Route::get('candidatures/create', [CandidatureController::class, 'create'])->name('candidatures.create');
Route::post('candidatures', [CandidatureController::class, 'store'])->name('candidatures.store');
