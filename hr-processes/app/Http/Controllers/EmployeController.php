<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;

class EmployeController extends Controller
{
    public function create()
    {
        return view('employes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'poste' => 'nullable|string|max:100',
            'salaire' => 'nullable|numeric|min:0',
            'competences' => 'nullable|string',
            'historique' => 'nullable|string',
        ]);

        Employe::create($request->all());

        return redirect()->route('employes.create')->with('success', 'Employé enregistré avec succès.');
    }
}
