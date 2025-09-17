<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;

class AnnonceController extends Controller
{
    public function index()
    {
        $annonces = Annonce::all();
        return view('annonces.index', compact('annonces'));
    }

    public function create()
    {
        return view('annonces.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:150',
            'description' => 'required|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
        ]);

        Annonce::create($request->all());

        return redirect()->route('annonces.index')->with('success', 'Annonce créée avec succès.');
    }

    public function edit(Annonce $annonce)
    {
        return view('annonces.edit', compact('annonce'));
    }

    public function update(Request $request, Annonce $annonce)
    {
        $request->validate([
            'titre' => 'required|string|max:150',
            'description' => 'required|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'in:active,inactive,expiree',
        ]);

        $annonce->update($request->all());

        return redirect()->route('annonces.index')->with('success', 'Annonce mise à jour.');
    }

    public function destroy(Annonce $annonce)
    {
        $annonce->delete();
        return redirect()->route('annonces.index')->with('success', 'Annonce supprimée.');
    }
}
