<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->unsignedSmallInteger('annee')->nullable()->after('type');
            $table->string('domaine')->nullable()->after('annee');
            $table->nullableMorphs('entite');           // entite_type + entite_id
            $table->string('folder_path')->nullable();   // chemin de référence MediaManager
            $table->string('statut')->default('archived')->after('resultat');
            $table->json('tags')->nullable()->after('statut');

            $table->index(['type', 'annee', 'domaine'], 'archives_type_annee_domaine_idx');
            $table->index(['entite_type', 'entite_id'], 'archives_entite_idx');
            $table->index('statut',                     'archives_statut_idx');
        });
    }

    public function down(): void
    {
        Schema::table('archives', function (Blueprint $table) {
            $table->dropIndex('archives_type_annee_domaine_idx');
            $table->dropIndex('archives_entite_idx');
            $table->dropIndex('archives_statut_idx');

            $table->dropMorphs('entite');
            $table->dropColumn(['annee', 'domaine', 'folder_path', 'statut', 'tags']);
        });
    }
};
