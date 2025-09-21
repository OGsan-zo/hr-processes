<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvAnalyse extends Model
{
    protected $fillable = [
        'candidature_id',
        'contenu_extrait',
        'mots_cles',
        'score_motivation',
        'score_experience',
        'score_competences',
        'resume'
    ];

    protected $casts = [
        'mots_cles' => 'array'
    ];

    public function candidature()
    {
        return $this->belongsTo(Candidature::class);
    }
}