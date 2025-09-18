<?php

namespace App\Http\Controllers;

use App\Models\Candidat;
use App\Models\Employe;
use App\Models\Candidature;
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

        return redirect()->route('candidats.index')->with('success', 'Candidat enregistré avec succès.');
    }

    public function index(Request $request)
    {
        $query = Candidat::query();

        if ($request->filled('age')) {
            $query->where('age', $request->age);
        }
        if ($request->filled('diplome')) {
            $query->where('diplome', 'like', '%' . $request->diplome . '%');
        }
        // Suppression du filtre adresse car le champ n'existe pas dans le modèle Candidat

        $candidats = $query->get();
        return view('candidats.index', compact('candidats'));
    }

    // NOUVELLE MÉTHODE pour Tâche 5 : Transformation candidat → employé
    public function transform(Request $request, Candidat $candidat)
    {
        $request->validate([
            'poste' => 'required|string|max:100',
            'salaire' => 'required|numeric|min:0',
            'competences' => 'nullable|string|max:255',
        ]);

        // Créer un employé à partir des données du candidat
        $employe = Employe::create([
            'nom' => $candidat->nom,  // Copie du nom du candidat
            'prenom' => $candidat->prenom,  // Copie du prénom du candidat
            'poste' => $request->poste,
            'salaire' => $request->salaire,
            'competences' => $request->competences,
            'historique' => 'Transformé depuis candidat ID ' . $candidat->id . ' le ' . now()->toDateString(),
        ]);

        // Mettre à jour la candidature associée (si elle existe et est acceptée)
        $candidature = Candidature::where('candidat_id', $candidat->id)
                                  ->where('statut', 'accepte')
                                  ->first();
        if ($candidature) {
            $candidature->update(['statut' => 'embauche']);
        }

        return redirect()->route('candidats.index')->with('success', 'Candidat transformé en employé avec succès !');
    }
}