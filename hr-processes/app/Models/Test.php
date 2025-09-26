<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'duree_minutes',
        'nombre_questions',
        'statut',
        'annonce_id'  // Ajout
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function annonce()  // Nouvelle relation
    {
        return $this->belongsTo(Annonce::class);
    }

    public function getDureeFormattedAttribute()
    {
        return $this->duree_minutes . ' minutes';
    }
}