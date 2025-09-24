<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('candidats', function (Blueprint $table) {
            $table->string('poste_suggere')->nullable()->after('score_global');
        });
    }

    public function down()
    {
        Schema::table('candidats', function (Blueprint $table) {
            $table->dropColumn('poste_suggere');
        });
    }
};