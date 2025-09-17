<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Candidat;
use App\Models\Annonce;
use Illuminate\Http\Request;

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
}
