<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\EntretienController;
use App\Http\Controllers\ContratController;

// Routes publiques (Breeze gère l'accueil)
Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification Breeze
Auth::routes();

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // Candidats (accès contrôlé par permissions dans contrôleurs)
    Route::get('/candidats', [CandidatController::class, 'index'])->name('candidats.index');
    Route::get('/candidats/create', [CandidatController::class, 'create'])->name('candidats.create');
    Route::post('/candidats', [CandidatController::class, 'store'])->name('candidats.store');
    Route::get('/candidats/classify', [CandidatController::class, 'classify'])->name('candidats.classify');
    Route::post('/candidats/classify', [CandidatController::class, 'classify'])->name('candidats.classify.post');
    Route::get('/candidats/{candidat}/migrate', [CandidatController::class, 'migrate'])->name('candidats.migrate');
    Route::post('/candidats/{candidat}/transform', [CandidatController::class, 'transform'])->name('candidats.transform');
    
    // Annonces
    Route::resource('annonces', AnnonceController::class);
    
    // Candidatures
    Route::get('candidatures/create', [CandidatureController::class, 'create'])->name('candidatures.create');
    Route::post('candidatures', [CandidatureController::class, 'store'])->name('candidatures.store');
    Route::get('/candidatures/selection', [CandidatureController::class, 'selection'])->name('candidatures.selection');
    Route::post('/candidatures/{candidature}/selection', [CandidatureController::class, 'updateSelection'])->name('candidatures.updateSelection');
    
    // Employés
    Route::get('/employes/create', [EmployeController::class, 'create'])->name('employes.create');
    Route::post('/employes', [EmployeController::class, 'store'])->name('employes.store');
    Route::get('/profile', [EmployeController::class, 'profile'])->name('employes.profile');
    Route::put('/profile', [EmployeController::class, 'updateProfile'])->name('employes.profile.update');
    
    // Entretiens et contrats
    Route::resource('entretiens', EntretienController::class)->only(['index','create','store']);
    Route::resource('contrats', ContratController::class)->only(['index','create','store']);
});