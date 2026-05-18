<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le champ actual_days pour suivre les jours réellement travaillés
     * par un expert sur un projet (vs planned_days = jours prévus).
     * Permet de calculer le glissement de coût expert.
     */
    public function up(): void
    {
        Schema::table('project_expert_contracts', function (Blueprint $table) {
            $table->integer('actual_days')->nullable()->after('planned_days')
                ->comment('Jours réellement travaillés (vs planned_days prévu)');
        });
    }

    public function down(): void
    {
        Schema::table('project_expert_contracts', function (Blueprint $table) {
            $table->dropColumn('actual_days');
        });
    }
};
