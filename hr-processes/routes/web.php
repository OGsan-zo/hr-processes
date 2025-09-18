<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\EntretienController;


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

Route::get('/candidatures/selection', [CandidatureController::class, 'selection'])->name('candidatures.selection');
Route::post('/candidatures/{candidature}/selection', [CandidatureController::class, 'updateSelection'])->name('candidatures.updateSelection');

Route::resource('entretiens', EntretienController::class)->only(['index','create','store']);
