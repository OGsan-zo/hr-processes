<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resultats_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidat_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_id')->constrained()->onDelete('cascade');
            $table->integer('score_obtenu')->default(0);
            $table->integer('score_max')->default(0);
            $table->decimal('pourcentage', 5, 2)->default(0);
            $table->json('reponses_utilisateur')->nullable();
            $table->timestamp('date_passe')->nullable();
            $table->timestamps();
            
            $table->unique(['candidat_id', 'test_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('resultats_tests');
    }
};