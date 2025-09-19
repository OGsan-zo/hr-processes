<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\EntretienController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\HomeController;

// Routes publiques
Route::get('/', function () {
    return view('welcome');
});

// Routes d'auth Breeze (gérées par require __DIR__.'/auth.php')
require __DIR__.'/auth.php';

// Dashboard Breeze
Route::get('/dashboard', [HomeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Routes profil Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes RH - PROTÉGÉES PAR AUTHENTIFICATION
Route::middleware(['auth'])->group(function () {
    // Candidats (Tâches 1, 5, 6, 12, 13)
    Route::get('/candidats', [CandidatController::class, 'index'])->name('candidats.index');
    Route::get('/candidats/create', [CandidatController::class, 'create'])->name('candidats.create');
    Route::post('/candidats', [CandidatController::class, 'store'])->name('candidats.store');
    Route::post('/candidats/{candidat}/transform', [CandidatController::class, 'transform'])->name('candidats.transform');
    Route::get('/candidats/classify', [CandidatController::class, 'classify'])->name('candidats.classify');
    Route::post('/candidats/classify', [CandidatController::class, 'classify'])->name('candidats.classify.post');
    Route::get('/candidats/{candidat}/migrate', [CandidatController::class, 'migrate'])->name('candidats.migrate');

    // Annonces (Tâche 3)
    Route::resource('annonces', AnnonceController::class);

    // Candidatures (Tâche 4, 6)
    Route::get('candidatures/create', [CandidatureController::class, 'create'])->name('candidatures.create');
    Route::post('candidatures', [CandidatureController::class, 'store'])->name('candidatures.store');
    Route::get('/candidatures/selection', [CandidatureController::class, 'selection'])->name('candidatures.selection');
    Route::post('/candidatures/{candidature}/selection', [CandidatureController::class, 'updateSelection'])->name('candidatures.updateSelection');

    // Employés (Tâche 2, 16)
    Route::get('/employes/create', [EmployeController::class, 'create'])->name('employes.create');
    Route::post('/employes', [EmployeController::class, 'store'])->name('employes.store');
    Route::get('/profile/employe', [EmployeController::class, 'profile'])->name('employes.profile');  // Évite conflit avec Breeze
    Route::put('/profile/employe', [EmployeController::class, 'updateProfile'])->name('employes.profile.update');

    // Entretiens (Tâche 1 jeudi)
    Route::resource('entretiens', EntretienController::class)->only(['index','create','store']);

    // Contrats (Tâches 2-4 jeudi)
    Route::resource('contrats', ContratController::class)->only(['index','create','store']);
});