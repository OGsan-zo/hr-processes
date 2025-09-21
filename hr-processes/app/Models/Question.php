<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'test_id',
        'question',
        'points',
        'type'
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function reponses()
    {
        return $this->hasMany(Reponse::class);
    }

    public function reponseCorrecte()
    {
        return $this->reponses()->where('correcte', true)->first();
    }
}