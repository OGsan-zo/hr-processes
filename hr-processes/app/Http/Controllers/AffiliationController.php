<?php

namespace App\Http\Controllers;

use App\Models\Affiliation;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AffiliationController extends Controller
{
    private function checkPermission($permission)
    {
        if (!auth()->check() || !auth()->user()->can($permission)) {
            abort(403, 'Accès non autorisé.');
        }
    }

    public function index()
    {
        $this->checkPermission('view-employes');
        $affiliations = Affiliation::with('employe')
            ->orderBy('date_expiration')
            ->orderBy('valide', 'desc')
            ->paginate(15);

        $stats = [
            'total' => Affiliation::count(),
            'valides' => Affiliation::valide()->count(),
            'expirants' => Affiliation::expirant()->count(),
            'expirés' => Affiliation::where('valide', false)->orWhere('date_expiration', '<', now())->count(),
        ];

        return view('affiliations.index', compact('affiliations', 'stats'));
    }

    public function create()
    {
        $this->checkPermission('create-employes');
        $employes = Employe::select('id', 'nom', 'prenom', 'poste')->get();
        return view('affiliations.create', compact('employes'));
    }

    public function store(Request $request)
    {
        $this->checkPermission('create-employes');
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'type' => 'required|in:cnaps,ostie,amit,autre',
            'numero_affiliation' => 'required|string|max:50|unique:affiliations',
            'date_debut' => 'required|date',
            'date_expiration' => 'nullable|date|after:date_debut',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,png|max:2048',
        ]);

        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $document) {
                $path = $document->store('affiliations/' . $request->type, 'public');
                $documents[$index] = $path;
            }
        }

        Affiliation::create([
            'employe_id' => $request->employe_id,
            'type' => $request->type,
            'numero_affiliation' => $request->numero_affiliation,
            'date_debut' => $request->date_debut,
            'date_expiration' => $request->date_expiration,
            'valide' => true,
            'documents' => $documents,
        ]);

        return redirect()->route('affiliations.index')->with('success', 'Affiliation créée avec succès.');
    }

    public function show(Affiliation $affiliation)
    {
        $this->checkPermission('view-employes');
        $affiliation->load('employe');
        return view('affiliations.show', compact('affiliation'));
    }

    public function edit(Affiliation $affiliation)
    {
        $this->checkPermission('create-employes');
        $employes = Employe::select('id', 'nom', 'prenom', 'poste')->get();
        return view('affiliations.edit', compact('affiliation', 'employes'));
    }

    public function update(Request $request, Affiliation $affiliation)
    {
        $this->checkPermission('create-employes');
        $request->validate([
            'type' => 'required|in:cnaps,ostie,amit,autre',
            'numero_affiliation' => 'required|string|max:50|unique:affiliations,numero_affiliation,' . $affiliation->id,
            'date_debut' => 'required|date',
            'date_expiration' => 'nullable|date|after:date_debut',
            'valide' => 'required|boolean',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,jpg,png|max:2048',
        ]);

        $documents = $affiliation->documents ?? [];
        if ($request->hasFile('documents')) {
            // Supprimer anciens documents
            foreach ($documents as $path) {
                Storage::disk('public')->delete($path);
            }
            
            // Ajouter nouveaux
            $newDocuments = [];
            foreach ($request->file('documents') as $index => $document) {
                $path = $document->store('affiliations/' . $request->type, 'public');
                $newDocuments[$index] = $path;
            }
            $documents = $newDocuments;
        }

        $affiliation->update([
            'type' => $request->type,
            'numero_affiliation' => $request->numero_affiliation,
            'date_debut' => $request->date_debut,
            'date_expiration' => $request->date_expiration,
            'valide' => $request->valide,
            'documents' => $documents,
        ]);

        return redirect()->route('affiliations.index')->with('success', 'Affiliation mise à jour.');
    }

    public function destroy(Affiliation $affiliation)
    {
        $this->checkPermission('create-employes');
        
        // Supprimer documents
        if ($affiliation->documents) {
            foreach ($affiliation->documents as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $affiliation->delete();
        return redirect()->route('affiliations.index')->with('success', 'Affiliation supprimée.');
    }

    public function renouveler(Affiliation $affiliation)
    {
        $this->checkPermission('create-employes');
        
        $nouvelle = $affiliation->replicate();
        $nouvelle->date_debut = now();
        $nouvelle->date_expiration = now()->addYear();
        $nouvelle->save();

        return redirect()->route('affiliations.show', $nouvelle)->with('success', 'Affiliation renouvelée.');
    }
}