<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('candidats', function (Blueprint $table) {
            // Ajouter email d'abord comme nullable
            $table->string('email')->nullable()->after('prenom');
        });

        // Mettre à jour les enregistrements existants avec des emails temporaires
        DB::table('candidats')->whereNull('email')->update([
            'email' => DB::raw("CONCAT('temp_', id, '@example.com')")
        ]);

        Schema::table('candidats', function (Blueprint $table) {
            // Maintenant rendre email NOT NULL et unique
            $table->string('email')->nullable(false)->unique()->change();
            
            // Ajouter password avec une valeur par défaut
            $table->string('password')->default('$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')->after('email'); // "password"
            
            $table->rememberToken()->after('password');  // Pour "remember me"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidats', function (Blueprint $table) {
            $table->dropColumn(['email', 'password', 'remember_token']);
        });
    }
};