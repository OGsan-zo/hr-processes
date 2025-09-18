<?php

namespace App\Http\Controllers;

use App\Models\Entretien;
use App\Models\Candidat;
use App\Models\Annonce;
use Illuminate\Http\Request;

class EntretienController extends Controller
{
    public function index()
    {
        $entretiens = Entretien::with(['candidat','annonce'])->get();
        return view('entretiens.index', compact('entretiens'));
    }

    public function create()
    {
        $candidats = Candidat::all();
        $annonces = Annonce::all();
        return view('entretiens.create', compact('candidats', 'annonces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'candidat_id' => 'required|exists:candidats,id',
            'annonce_id' => 'required|exists:annonces,id',
            'date_entretien' => 'required|date',
            'duree' => 'nullable|integer|min:15',
        ]);

        Entretien::create($request->all());

        return redirect()->route('entretiens.index')->with('success', 'Entretien planifié avec succès.');
    }
}
