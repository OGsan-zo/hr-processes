<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidat extends Model
{
    protected $table = 'candidats';

    protected $fillable = [
        'nom',
        'prenom',
        'age',
        'diplome',
        'cv',
        'status',
        'competences',           // Nouveau
        'score_competences',     // Nouveau
        'score_profil',          // Nouveau
        'score_global'           // Nouveau
    ];

    // Nouvelle méthode pour calculer le score automatique
    public function calculerScore($annonceCompetences = [])
    {
        $score = 0;
        $this->score_competences = 0;
        $this->score_profil = 0;

        // Score compétences (comparaison avec l'annonce)
        if ($this->competences && $annonceCompetences) {
            $candidatComps = explode(',', strtolower(trim($this->competences)));
            $annonceComps = array_map('strtolower', $annonceCompetences);
            
            $matchCount = count(array_intersect($candidatComps, $annonceComps));
            $this->score_competences = min($matchCount * 20, 100); // Max 100
            $score += $this->score_competences;
        }

        // Score profil (basé sur l'âge et diplôme)
        $this->score_profil = $this->calculerScoreProfil();
        $score += $this->score_profil;

        // Score global (moyenne pondérée)
        $this->score_global = round($score / 2);
        
        $this->save();
        return $this->score_global;
    }

    private function calculerScoreProfil()
    {
        $score = 0;
        
        // Âge (optimal 25-35 ans)
        if ($this->age >= 25 && $this->age <= 35) {
            $score += 50;
        } elseif ($this->age >= 22 && $this->age <= 40) {
            $score += 30;
        }
        
        // Diplôme
        if ($this->diplome) {
            $diplomeScore = match(strtolower($this->diplome)) {
                'master', 'bac+5', 'ingénieur' => 50,
                'licence', 'bac+3', 'bts' => 40,
                'bac', 'bac+2' => 30,
                default => 20
            };
            $score += $diplomeScore;
        }
        
        return min($score, 100);
    }
}