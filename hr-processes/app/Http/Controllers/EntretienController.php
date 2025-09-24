<?php

namespace App\Http\Controllers;

use App\Models\Entretien;
use App\Models\Candidat;
use App\Models\Annonce;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EntretienController extends Controller
{
    public function index()
    {
        $entretiens = Entretien::with(['candidat','annonce'])->get();
        return view('entretiens.index', compact('entretiens'));
    }

    public function calendar()
    {
        $entretiens = Entretien::with(['candidat','annonce'])
            ->orderBy('date_entretien', 'asc')
            ->get();
        return view('entretiens.calendar', compact('entretiens'));
    }

    public function create(Request $request)
    {
        $candidats = Candidat::all();
        $annonces = Annonce::all();
        
        // Pré-remplir la date si elle vient du calendrier
        $selectedDate = $request->get('date');
        
        return view('entretiens.create', compact('candidats', 'annonces', 'selectedDate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'candidat_id' => 'required|exists:candidats,id',
            'annonce_id' => 'required|exists:annonces,id',
            'date_entretien' => 'required|date',
            'duree' => 'nullable|integer|min:15',
            'statut' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['statut'] = $data['statut'] ?? 'planifié';

        Entretien::create($data);

        return redirect()->route('entretiens.calendar')->with('success', 'Entretien planifié avec succès.');
    }

    /**
     * API endpoint pour récupérer les entretiens au format JSON pour FullCalendar
     */
    public function getEvents()
    {
        $entretiens = Entretien::with(['candidat','annonce'])->get();
        
        $events = [];
        foreach ($entretiens as $entretien) {
            $events[] = [
                'id' => $entretien->id,
                'title' => $entretien->candidat->nom . ' ' . $entretien->candidat->prenom . ' - ' . $entretien->annonce->titre,
                'start' => $entretien->date_entretien,
                'end' => Carbon::parse($entretien->date_entretien)->addMinutes($entretien->duree ?? 60),
                'className' => 'statut-' . strtolower($entretien->statut ?? 'planifie'),
                'extendedProps' => [
                    'candidat' => $entretien->candidat->nom . ' ' . $entretien->candidat->prenom,
                    'annonce' => $entretien->annonce->titre,
                    'duree' => $entretien->duree ?? 60,
                    'statut' => $entretien->statut ?? 'planifié'
                ]
            ];
        }
        
        return response()->json($events);
    }
}