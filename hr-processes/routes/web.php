<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidatController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\EntretienController;
use App\Http\Controllers\ContratController;

// Routes publiques
Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification Breeze (REMPLACE Auth::routes())
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Illuminate\Http\Request $request, $id, $hash) {
    $request->user()->markEmailAsVerified();
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // Candidats
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