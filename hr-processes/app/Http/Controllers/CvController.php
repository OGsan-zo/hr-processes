<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\CvAnalyse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CvController extends Controller
{
    private function checkPermission($permission)
    {
        if (!auth()->check() || !auth()->user()->can($permission)) {
            abort(403, 'Accès non autorisé.');
        }
    }

    public function index()
    {
        $this->checkPermission('view-candidatures');
        $candidatures = Candidature::with(['candidat', 'cvAnalyse'])->get();
        return view('cvs.index', compact('candidatures'));
    }

    public function analyse(Request $request, Candidature $candidature)
    {
        $this->checkPermission('manage-selections');
        
        if (!$candidature->cv) {
            return redirect()->back()->with('error', 'Aucun CV à analyser.');
        }

        $cvPath = Storage::disk('public')->path($candidature->cv);
        
        if (!file_exists($cvPath)) {
            return redirect()->back()->with('error', 'Fichier CV introuvable.');
        }

        // Analyse simple (simulation - tu peux utiliser un package comme Smalot/PdfParser)
        $contenu = $this->extraireTexteCv($cvPath);
        $motsCles = $this->extraireMotsCles($contenu);
        
        $analyse = CvAnalyse::updateOrCreate(
            ['candidature_id' => $candidature->id],
            [
                'contenu_extrait' => substr($contenu, 0, 500) . '...',
                'mots_cles' => $motsCles,
                'score_motivation' => $this->calculerScoreMotivation($contenu),
                'score_experience' => $this->calculerScoreExperience($contenu),
                'score_competences' => $this->calculerScoreCompetences($contenu, $motsCles),
                'resume' => $this->genererResume($contenu, $motsCles),
            ]
        );

        return redirect()->back()->with('success', 'CV analysé avec succès !');
    }

    private function extraireTexteCv($path)
    {
        // Simulation - utilise un vrai parser PDF en production
        $contenu = file_get_contents($path);
        // Extraction basique des mots (améliore avec un vrai parser)
        $texte = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $contenu);
        return strtolower(trim($texte));
    }

    private function extraireMotsCles($texte)
    {
        $mots = explode(' ', $texte);
        $motsFrequents = array_count_values($mots);
        arsort($motsFrequents);
        
        $motsCles = [];
        foreach (array_slice($motsFrequents, 0, 10, true) as $mot => $freq) {
            if (strlen($mot) > 3) { // Ignorer les mots courts
                $motsCles[] = $mot;
            }
        }
        
        return array_slice($motsCles, 0, 5);
    }

    private function calculerScoreMotivation($texte)
    {
        $motsMotivation = ['motivation', 'passion', 'enthousiasme', 'dévoué', 'engagé', 'ambition'];
        $score = 0;
        foreach ($motsMotivation as $mot) {
            if (stripos($texte, $mot) !== false) {
                $score += 20;
            }
        }
        return min($score, 100);
    }

    private function calculerScoreExperience($texte)
    {
        $motsExperience = ['expérience', 'années', 'projet', 'réalisé', 'géré', 'développé'];
        $score = 0;
        foreach ($motsExperience as $mot) {
            if (stripos($texte, $mot) !== false) {
                $score += 15;
            }
        }
        return min($score, 100);
    }

    private function calculerScoreCompetences($texte, $motsCles)
    {
        $competencesTech = ['php', 'laravel', 'javascript', 'react', 'mysql', 'postgresql', 'docker', 'git'];
        $score = 0;
        
        foreach ($competencesTech as $comp) {
            if (stripos($texte, $comp) !== false) {
                $score += 10;
            }
        }
        
        return min($score, 100);
    }

    private function genererResume($texte, $motsCles)
    {
        $phrases = explode('.', $texte);
        $resume = 'Profil candidat : ';
        
        foreach (array_slice($motsCles, 0, 3) as $mot) {
            $resume .= ucfirst($mot) . ', ';
        }
        
        $resume .= 'expérience pertinente détectée.';
        return trim($resume, ', ');
    }
}