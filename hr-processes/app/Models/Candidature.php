<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidature extends Model
{
    protected $fillable = [
        'candidat_id',
        'annonce_id',
        'cv',
        'statut'
    ];

    /**
     * RELATION : Une candidature appartient à un candidat
     */
    public function candidat()
    {
        return $this->belongsTo(Candidat::class, 'candidat_id');
    }

    /**
     * RELATION : Une candidature appartient à une annonce
     */
    public function annonce()
    {
        return $this->belongsTo(Annonce::class, 'annonce_id');
    }

    /**
     * RELATION : Une candidature a une analyse de CV
     */
    public function cvAnalyse()
    {
        return $this->hasOne(CvAnalyse::class, 'candidature_id');
    }
}