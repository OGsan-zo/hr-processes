<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultatTest extends Model
{
    protected $table = 'resultats_tests';

    protected $fillable = [
        'candidat_id',
        'test_id',
        'score_obtenu',
        'score_max',
        'pourcentage',
        'reponses_utilisateur',
        'date_passe'
    ];

    protected $casts = [
        'reponses_utilisateur' => 'array',
        'date_passe' => 'datetime',
        'pourcentage' => 'decimal:2'
    ];

    public function candidat()
    {
        return $this->belongsTo(Candidat::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function getNoteFormattedAttribute()
    {
        return $this->pourcentage . '%';
    }
}