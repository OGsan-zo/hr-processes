<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    protected $table = 'annonces';

    protected $fillable = [
        'titre',
        'description',
        'date_debut',
        'date_fin',
        'statut'
    ];
    
    public function test()  // Nouvelle relation
    {
        return $this->hasOne(Test::class);
    }
}

