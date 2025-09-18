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

    public function estActif()
    {
        return $this->date_fin >= now() && $this->statut === 'actif';
    }

    public function estExpire()
    {
        return $this->date_fin < now();
    }

    public function duree()
    {
        return now()->parse($this->date_debut)->diffInDays(now()->parse($this->date_fin));
    }

}
