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
        'status'
    ];
}
