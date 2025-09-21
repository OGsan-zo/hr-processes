<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Affiliation extends Model
{
    protected $fillable = [
        'employe_id',
        'type',
        'numero_affiliation',
        'date_debut',
        'date_expiration',
        'valide',
        'documents'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_expiration' => 'date',
        'documents' => 'array',
        'valide' => 'boolean'
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    // Scope pour affiliations expirées
    public function scopeExpirant($query)
    {
        return $query->where('valide', true)
                     ->whereNotNull('date_expiration')
                     ->whereDate('date_expiration', '<=', Carbon::now()->addMonth());
    }

    // Scope pour affiliations valides
    public function scopeValide($query)
    {
        return $query->where('valide', true);
    }

    public function getTypeFormattedAttribute()
    {
        $types = [
            'cnaps' => 'CNAPS',
            'ostie' => 'OSTIE',
            'amit' => 'AMIT',
            'autre' => 'Autre'
        ];
        return $types[$this->type] ?? $this->type;
    }

    public function getStatutAffiliationAttribute()
    {
        if (!$this->valide) {
            return 'Invalide';
        }

        if (!$this->date_expiration) {
            return 'Permanent';
        }

        if (Carbon::now()->gt($this->date_expiration)) {
            return 'Expiré';
        }

        $joursRestants = Carbon::now()->diffInDays($this->date_expiration, false);
        if ($joursRestants <= 30) {
            return 'Expirant (' . $joursRestants . 'j)';
        }

        return 'Valide';
    }

    public function getStatutBadgeAttribute()
    {
        $statut = $this->statut_affiliation;
        return match($statut) {
            'Expiré' => 'bg-danger text-white',
            'Expirant' => 'bg-warning text-dark',
            'Invalide' => 'bg-secondary text-white',
            'Permanent', 'Valide' => 'bg-success text-white',
            default => 'bg-info text-white'
        };
    }
}