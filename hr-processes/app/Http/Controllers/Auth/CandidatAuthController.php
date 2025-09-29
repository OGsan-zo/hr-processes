<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('candidat.login');  // Créez cette vue
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('candidat')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('candidat.annonces');  // Redirige vers liste annonces
        }

        return back()->withErrors(['email' => 'Identifiants incorrects.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('candidat')->logout();
        return redirect('/candidat/login');
    }
}