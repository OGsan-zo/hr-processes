<?php

namespace App\Http\Controllers;

use App\Models\Candidat;
use App\Models\Employe;
use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class CandidatController extends Controller
{
    // Formulaire
    public function create()
    {
        return view('candidats.create');
    }

    // Enregistrement
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'age' => 'required|integer|min:18',
            'diplome' => 'nullable|string|max:150',
            'cv' => 'nullable|file|mimes:pdf|max:2048',
            'competences' => 'nullable|string|max:255',  // Facultatif si extrait
        ]);

        $cvPath = null;
        $competences = $request->competences;  // Manuel par défaut

        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
            
            // Analyse CV pour extraire compétences (simulation simple - utilise ton CvController si implémenté)
            $contenu = file_get_contents(Storage::path($cvPath));
            $competences = $this->extraireCompetencesCv($contenu);  // Fonction d'extraction
        }

        Candidat::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'age' => $request->age,
            'diplome' => $request->diplome,
            'cv' => $cvPath,
            'competences' => $competences,
        ]);

        return redirect()->route('candidats.index')->with('success', 'Candidat enregistré avec succès.');
    }


    private function extraireCompetencesCv($contenu)
    {
        try {
            // Parser le contenu PDF
            $parser = new Parser();
            $pdf = $parser->parseContent($contenu);
            $texte = $pdf->getText();
            
            $motsCles = [
                'php', 'laravel', 'javascript', 'react', 'mysql', 'postgresql', 'docker',
                'git', 'aws', 'python', 'java', 'html', 'css', 'bootstrap', 'vue', 'angular',
                'symfony', 'node', 'express', 'mongodb', 'redis', 'linux', 'windows',
                'communication', 'travail d\'équipe', 'agile', 'scrum', 'devops',
                'gestion de projet', 'leadership', 'résolution de problèmes'
            ];
            
            $competencesTrouvees = [];
            $texteMinuscule = strtolower($texte);
            
            foreach ($motsCles as $mot) {
                if (stripos($texteMinuscule, $mot) !== false) {
                    $competencesTrouvees[] = ucfirst($mot);
                }
            }
            
            $competencesTrouvees = array_unique($competencesTrouvees);
            return !empty($competencesTrouvees) ? implode(', ', $competencesTrouvees) : $request->competences;
            
        } catch (\Exception $e) {
            // En cas d'erreur, retourner les compétences manuelles
            return $request->competences;
        }
    }


    public function index(Request $request)
    {
        $query = Candidat::query();

        if ($request->filled('age')) {
            $query->where('age', $request->age);
        }
        if ($request->filled('diplome')) {
            $query->where('diplome', 'like', '%' . $request->diplome . '%');
        }
        // Suppression du filtre adresse car le champ n'existe pas dans le modèle Candidat

        $candidats = $query->get();
        return view('candidats.index', compact('candidats'));
    }

    // Transformation candidat → employé
    public function transform(Request $request, Candidat $candidat)
    {
        $request->validate([
            'poste' => 'required|string|max:100',
            'salaire' => 'required|numeric|min:0',
            'competences' => 'nullable|string|max:255',
        ]);

        // Créer un employé à partir des données du candidat
        $employe = Employe::create([
            'nom' => $candidat->nom,  // Copie du nom du candidat
            'prenom' => $candidat->prenom,  // Copie du prénom du candidat
            'poste' => $request->poste,
            'salaire' => $request->salaire,
            'competences' => $request->competences,
            'historique' => 'Transformé depuis candidat ID ' . $candidat->id . ' le ' . now()->toDateString(),
        ]);

        // Mettre à jour la candidature associée (si elle existe et est acceptée)
        $candidature = Candidature::where('candidat_id', $candidat->id)
                                  ->where('statut', 'accepte')
                                  ->first();
        if ($candidature) {
            $candidature->update(['statut' => 'embauche']);
        }

        return redirect()->route('candidats.index')->with('success', 'Candidat transformé en employé avec succès !');
    }

    public function classify(Request $request)
    {
        $annonce_id = $request->input('annonce_id');
        $annonces = \App\Models\Annonce::where('statut', 'active')->get();
        
        if ($request->filled('calcule_scores')) {
            $candidats = \App\Models\Candidat::whereHas('candidatures', function($q) use ($annonce_id) {
                $q->where('annonce_id', $annonce_id);
            })->get();
            
            // Récupérer les compétences de l'annonce
            $annonce = \App\Models\Annonce::find($annonce_id);
            $annonceCompetences = $this->extraireCompetencesAnnonce($annonce->description ?? '');
            
            foreach ($candidats as $candidat) {
                $candidat->calculerScore($annonceCompetences);
            }
            
            return redirect()->back()->with('success', 'Scores calculés pour ' . $candidats->count() . ' candidats.');
        }
        
        $candidats = \App\Models\Candidat::with('candidatures')
            ->when($request->filled('annonce_id'), function($query) use ($request) {
                $query->whereHas('candidatures', function($q) use ($request) {
                    $q->where('annonce_id', $request->annonce_id);
                });
            })
            ->orderByDesc('score_global')
            ->get();
        
        return view('candidats.classify', compact('candidats', 'annonces'));
    }

    private function extraireCompetencesAnnonce($description)
    {
        // Logique simple pour extraire les compétences de la description
        $motsCles = ['php', 'laravel', 'javascript', 'react', 'mysql', 'postgresql', 'docker'];
        $competences = [];
        
        foreach ($motsCles as $mot) {
            if (stripos($description, $mot) !== false) {
                $competences[] = $mot;
            }
        }
        
        return $competences;
    }

    public function migrate(Candidat $candidat)
    {
        // Vérifier si le candidat a un score suffisant
        if ($candidat->score_global < 80) {
            return redirect()->route('candidats.classify')
                ->with('error', 'Score insuffisant (' . $candidat->score_global . '/100). Seuil minimum : 80.');
        }

        // Créer l'employé (même logique que la méthode transform)
        $employe = Employe::create([
            'nom' => $candidat->nom,
            'prenom' => $candidat->prenom,
            'poste' => $this->determinerPoste($candidat), // Poste automatique basé sur le score
            'salaire' => $this->determinerSalaire($candidat->score_global),
            'competences' => $candidat->competences,
            'historique' => 'Migration automatique depuis candidat ID ' . $candidat->id . 
                        ' (Score: ' . $candidat->score_global . '/100) le ' . now()->toDateString(),
        ]);

        // Marquer la candidature comme embauchée
        $candidature = Candidature::where('candidat_id', $candidat->id)
                                ->where('statut', 'accepte')
                                ->first();
        if ($candidature) {
            $candidature->update(['statut' => 'embauche']);
        }

        // Marquer le candidat comme migré
        $candidat->update(['status' => 'migre']);

        return redirect()->route('candidats.classify')
            ->with('success', 'Candidat "' . $candidat->nom . ' ' . $candidat->prenom . 
                '" migré automatiquement vers employé ID ' . $employe->id);
    }

    private function determinerPoste(Candidat $candidat)
    {
        $score = $candidat->score_global;
        
        return match(true) {
            $score >= 90 => 'Senior ' . ($candidat->diplome ? strtoupper(substr($candidat->diplome, 0, 1)) . ' ' : '') . 'Développeur',
            $score >= 80 => 'Junior ' . ($candidat->diplome ? strtoupper(substr($candidat->diplome, 0, 1)) . ' ' : '') . 'Développeur',
            $score >= 70 => 'Stagiaire ' . ($candidat->diplome ? strtoupper(substr($candidat->diplome, 0, 1)) . ' ' : '') . 'Développeur',
            default => 'Assistant Technique'
        };
    }

    private function determinerSalaire($score)
    {
        return match(true) {
            $score >= 90 => 800000,
            $score >= 80 => 500000,
            $score >= 70 => 300000,
            default => 200000
        };
    }


}