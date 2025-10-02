<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetDatabase extends Command
{
    protected $signature = 'db:reset-project';
    protected $description = 'Réinitialiser uniquement les tables métiers sans toucher aux utilisateurs et rôles (PostgreSQL)';

    public function handle()
    {
        $tablesToTruncate = [
            'employes',
            'candidatures',
            'annonces',
            'entretiens',
            'contrats',
            'cv_analyses',
            'candidats',
            'questions',
            'reponses',
            'resultats_tests',
            'affiliations',
            'tests',
        ];

        foreach ($tablesToTruncate as $table) {
            // Réinitialise l'auto-incrément et gère les contraintes FK
            DB::statement("TRUNCATE TABLE {$table} RESTART IDENTITY CASCADE;");
            $this->info("Table {$table} vidée ✅");
        }

        $this->info('✅ Réinitialisation terminée sans toucher aux utilisateurs et rôles !');
    }
}
