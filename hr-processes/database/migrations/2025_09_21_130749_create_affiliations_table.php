<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('affiliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->constrained('employes')->onDelete('cascade');
            $table->enum('type', ['cnaps', 'ostie', 'amit', 'autre'])->default('cnaps');
            $table->string('numero_affiliation')->unique();
            $table->date('date_debut');
            $table->date('date_expiration')->nullable();
            $table->boolean('valide')->default(true);
            $table->text('documents')->nullable(); // JSON avec chemins fichiers
            $table->timestamps();

            $table->index(['type', 'valide']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('affiliations');
    }
};