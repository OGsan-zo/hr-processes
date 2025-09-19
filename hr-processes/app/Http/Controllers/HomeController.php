<?php

namespace App\Http\Controllers;

use App\Models\Candidat;
use App\Models\Annonce;
use App\Models\Candidature;
use App\Models\Employe;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->roles->first()->name ?? 'unknown';

        $stats = [
            'candidats' => Candidat::count(),
            'annonces' => Annonce::where('statut', 'active')->count(),
            'candidatures' => Candidature::count(),
            'employes' => Employe::count(),
        ];

        $candidaturesParStatut = Candidature::selectRaw('statut, count(*) as count')
            ->groupBy('statut')
            ->pluck('count', 'statut');

        $topCandidats = Candidat::orderByDesc('score_global')
            ->limit(5)
            ->select('nom', 'prenom', 'score_global', 'id')
            ->get();

        return view('dashboard', compact('role', 'stats', 'candidaturesParStatut', 'topCandidats'));
    }
}