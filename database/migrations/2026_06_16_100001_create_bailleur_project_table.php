<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table pivot Projet ↔ Bailleur (co-financement).
     * Porte le montant de financement et l'indicateur de chef de file (bailleur principal).
     */
    public function up(): void
    {
        Schema::create('bailleur_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('bailleur_id')->constrained('bailleurs')->cascadeOnDelete();

            $table->decimal('financing_amount', 15, 2)->nullable();
            $table->boolean('is_lead')->default(false);

            $table->timestamps();

            $table->unique(['project_id', 'bailleur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bailleur_project');
    }
};
