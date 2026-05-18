<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le flag use_manual_percentage pour permettre de débrayer le calcul
     * automatique de l'avancement projet (basé sur les livrables/activités)
     * au profit d'une saisie manuelle par le chef de projet.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('use_manual_percentage')->default(false)->after('execution_percentage')
                ->comment('Si vrai, l\'avancement n\'est plus recalculé automatiquement');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('use_manual_percentage');
        });
    }
};
