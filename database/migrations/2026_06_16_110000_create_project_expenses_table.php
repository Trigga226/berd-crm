<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Frais remboursables d'un projet (missions, per diem, reprographie, transport...).
     * Avec le coût réel des contrats experts, ils composent le budget consommé.
     */
    public function up(): void
    {
        Schema::create('project_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            $table->string('label');
            $table->string('category')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('expense_date')->nullable();
            $table->string('status')->default('pending'); // pending / reimbursed
            $table->string('file_path')->nullable();       // justificatif
            $table->text('notes')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        // Recalcule le budget consommé des projets existants selon la nouvelle règle
        // (coût réel experts + frais remboursables), corrigeant les valeurs erronées
        // basées sur les paiements de factures (revenus).
        Project::query()->withoutGlobalScopes()->each(function (Project $project) {
            $project->updateCalculations();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_expenses');
    }
};
