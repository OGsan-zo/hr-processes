<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    protected $fillable = [
        'employe_id',
        'type',
        'date_debut',
        'date_fin',
        'renouvellements',
        'statut'
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
}
