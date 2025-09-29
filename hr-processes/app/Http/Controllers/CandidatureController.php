<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Candidat;
use App\Models\Annonce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CandidatureController extends Controller
{
    public function create()
    {
        $candidats = Candidat::all();
        $annonces = Annonce::where('statut', 'active')->get();
        return view('candidatures.create', compact('candidats', 'annonces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'candidat_id' => 'required|exists:candidats,id',
            'annonce_id' => 'required|exists:annonces,id',
            'cv' => 'required|mimes:pdf,doc,docx|max:2048'
        ]);

        $cvPath = $request->file('cv')->store('cvs', 'public');

        Candidature::create([
            'candidat_id' => $request->candidat_id,
            'annonce_id' => $request->annonce_id,
            'cv' => $cvPath,
            'statut' => 'en_attente'
        ]);

        return redirect()->back()->with('success', 'Candidature enregistrée avec succès.');
    }

    public function selection()
    {
        $candidatures = Candidature::with(['candidat', 'annonce'])->get();
        return view('candidats.selection', compact('candidatures'));
    }

    public function updateSelection(Request $request, Candidature $candidature)
    {
        $request->validate([
            'statut' => 'required|in:accepte,refuse'
        ]);

        $candidature->update(['statut' => $request->statut]);

        return redirect()->route('candidatures.selection')->with('success', 'Statut mis à jour.');
    }

    public function showPostulerForm(Annonce $annonce)
    {
        return view('candidatures.postuler', compact('annonce'));
    }

    public function postuler(Request $request, Annonce $annonce)
    {
        $candidat = Auth::guard('candidat')->user();  // Candidat authentifié

        // Valider (pas de CV upload)
        $request->validate([
            // Ajoutez d'autres champs si besoin, ex. lettre motivation
        ]);

        Candidature::create([
            'candidat_id' => $candidat->id,
            'annonce_id' => $annonce->id,
            'cv' => $candidat->cv,  // Utiliser le CV existant du candidat
            'statut' => 'en_attente'
        ]);

        // Rediriger vers le test si existe
        if ($annonce->test) {
            return redirect()->route('tests.pass', $annonce->test->id)->with('success', 'Candidature soumise. Passez le test maintenant.');
        }

        return redirect()->route('candidats.annonces')->with('success', 'Candidature soumise.');
    }


}
