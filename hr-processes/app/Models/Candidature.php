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

    public function candidat()
    {
        return $this->belongsTo(Candidat::class);
    }

    public function annonce()
    {
        return $this->belongsTo(Annonce::class);
    }
}
