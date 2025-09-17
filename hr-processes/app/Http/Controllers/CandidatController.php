<?php

namespace App\Http\Controllers;

use App\Models\Candidat;
use Illuminate\Http\Request;

class CandidatController extends Controller
{
    // Formulaire
    public function create()
    {
        return view('candidats.create');
    }

    // Enregistrement
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'age' => 'required|integer|min:18',
            'diplome' => 'nullable|string|max:150',
            'cv' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
        }

        Candidat::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'age' => $request->age,
            'diplome' => $request->diplome,
            'cv' => $cvPath,
        ]);

        return redirect()->route('candidats.create')->with('success', 'Candidat enregistré avec succès.');
    }
}
