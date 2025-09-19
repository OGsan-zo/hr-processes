<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;

class EmployeController extends Controller
{
    private function checkPermission($permission)
    {
        if (!auth()->check() || !auth()->user()->can($permission)) {
            abort(403, 'Accès non autorisé. Permission requise : ' . $permission);
        }
    }

    public function create()
    {
        $this->checkPermission('create-employes');
        return view('employes.create');
    }

    public function store(Request $request)
    {
        $this->checkPermission('create-employes');
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'poste' => 'required|string|max:100',
            'salaire' => 'required|numeric|min:0',
            'competences' => 'nullable|string|max:255',
            'historique' => 'nullable|string',
        ]);

        Employe::create($request->all());

        return redirect()->route('employes.create')->with('success', 'Employé créé avec succès.');
    }

    public function profile()
    {
        $this->checkPermission('view-profile');
        $user = auth()->user();
        $employe = Employe::where('nom', $user->name)->firstOrFail();  // Assumer nom = user name

        return view('employes.profile', compact('employe'));
    }

    public function updateProfile(Request $request)
    {
        $this->checkPermission('update-profile');
        $user = auth()->user();
        $employe = Employe::where('nom', $user->name)->firstOrFail();

        $request->validate([
            'competences' => 'nullable|string|max:255',
            'historique' => 'nullable|string',
        ]);

        $employe->update([
            'competences' => $request->competences,
            'historique' => $request->historique,
        ]);

        return redirect()->route('employes.profile')->with('success', 'Profil mis à jour.');
    }
}