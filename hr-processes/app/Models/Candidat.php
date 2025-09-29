<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;  // Utiliser Authenticatable au lieu de Model
use Illuminate\Notifications\Notifiable;

class Candidat extends Authenticatable  // Changez de Model à Authenticatable
{
    protected $table = 'candidats';
    protected $hidden = ['password'];  // Cacher le password

    protected $fillable = [
        'nom',
        'prenom',
        'age',
        'diplome',
        'cv',
        'status',
        'competences',
        'score_competences',
        'score_profil',
        'score_global',
        'poste_suggere',
        'email',  // Ajoutez si pas déjà (pour login)
        'password',  // Ajoutez un champ password dans la migration si pas déjà (hashé)
    ];

    /**
     * RELATION : Un candidat a plusieurs candidatures
     */
    public function candidatures()
    {
        return $this->hasMany(Candidature::class, 'candidat_id');
    }

    // Méthodes de scoring
    public function calculerScore($annonceCompetences = [])
    {
        $score = 0;
        $this->score_competences = 0;
        $this->score_profil = 0;

        if ($this->competences && $annonceCompetences) {
            $candidatComps = array_map('trim', explode(',', strtolower(trim($this->competences))));
            $annonceComps = array_map('strtolower', $annonceCompetences);
            $matchCount = count(array_intersect($candidatComps, $annonceComps));
            $this->score_competences = min($matchCount * 20, 100);
            $score += $this->score_competences;
        }

        $this->score_profil = $this->calculerScoreProfil();
        $score += $this->score_profil;

        $this->score_global = round($score / 2);
        $this->save();
        return $this->score_global;
    }

    private function calculerScoreProfil()
    {
        $score = 0;
        
        // Score âge
        if ($this->age >= 25 && $this->age <= 35) {
            $score += 50;
        } elseif ($this->age >= 22 && $this->age <= 40) {
            $score += 30;
        }
        
        // Score diplôme
        if ($this->diplome) {
            $diplome = strtolower(trim($this->diplome));
            $diplomeScore = match(true) {
                str_contains($diplome, 'master') || str_contains($diplome, 'bac+5') || str_contains($diplome, 'ingénieur') => 50,
                str_contains($diplome, 'licence') || str_contains($diplome, 'bac+3') || str_contains($diplome, 'bts') => 40,
                str_contains($diplome, 'bac') || str_contains($diplome, 'bac+2') => 30,
                default => 20
            };
            $score += $diplomeScore;
        }
        
        return min($score, 100);
    }
}