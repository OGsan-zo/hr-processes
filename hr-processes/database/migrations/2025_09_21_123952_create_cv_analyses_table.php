<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cv_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->constrained()->onDelete('cascade');
            $table->text('contenu_extrait')->nullable();
            $table->json('mots_cles')->nullable();
            $table->integer('score_motivation')->default(0);
            $table->integer('score_experience')->default(0);
            $table->integer('score_competences')->default(0);
            $table->text('resume')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cv_analyses');
    }
};