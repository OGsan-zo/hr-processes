<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Employe;
use Illuminate\Http\Request;

class ContratController extends Controller
{
    public function index()
    {
        $contrats = Contrat::with('employe')->get();

        foreach ($contrats as $contrat) {
            $contrat->statut_calcule = $contrat->estExpire() ? 'expiré' : 'en cours';
            $contrat->duree_jours = $contrat->duree();
        }

        return view('contrats.index', compact('contrats'));
    }


    public function create()
    {
        $employes = Employe::all();
        return view('contrats.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'type' => 'required|in:essai,cdi,cdd',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'renouvellements' => 'integer|min:0|max:1'
        ]);

        // Vérifier durée max pour essai (6 mois)
        if ($request->type === 'essai') {
            $diff = now()->parse($request->date_debut)->diffInMonths(now()->parse($request->date_fin));
            if ($diff > 6) {
                return back()->withErrors(['date_fin' => 'La durée d\'un contrat d\'essai ne peut pas dépasser 6 mois.']);
            }
        }

        Contrat::create($request->all());

        return redirect()->route('contrats.index')->with('success', 'Contrat ajouté avec succès.');
    }
}
