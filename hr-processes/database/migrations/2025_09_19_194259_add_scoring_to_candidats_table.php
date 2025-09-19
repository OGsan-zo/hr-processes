<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('candidats', function (Blueprint $table) {
            $table->integer('score_competences')->default(0)->after('diplome');
            $table->integer('score_profil')->default(0)->after('score_competences');
            $table->integer('score_global')->default(0)->after('score_profil');
            $table->text('competences')->nullable()->after('diplome'); // Ajout du champ compétences
        });
    }

    public function down()
    {
        Schema::table('candidats', function (Blueprint $table) {
            $table->dropColumn(['score_competences', 'score_profil', 'score_global', 'competences']);
        });
    }
};