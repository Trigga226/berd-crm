<?php

namespace App\Observers;

use App\Models\ProjectExpense;
use App\Observers\Concerns\LogsToSecureView;
use Illuminate\Support\Facades\Cache;

class ProjectExpenseObserver
{
    use LogsToSecureView;

    public function created(ProjectExpense $expense): void
    {
        $expense->project?->updateCalculations();
        Cache::increment('berd_stats_version');

        $this->logAction(
            "Création d'un frais remboursable",
            "Création du frais « {$expense->label} » pour le projet : {$expense->project?->title}",
            'Création'
        );
    }

    public function updated(ProjectExpense $expense): void
    {
        $expense->project?->updateCalculations();
        Cache::increment('berd_stats_version');

        $this->logAction(
            "Modification d'un frais remboursable",
            "Modification du frais « {$expense->label} » pour le projet : {$expense->project?->title}",
            'Modification'
        );
    }

    public function deleted(ProjectExpense $expense): void
    {
        $expense->project?->updateCalculations();
        Cache::increment('berd_stats_version');

        $this->logAction(
            "Suppression d'un frais remboursable",
            "Suppression du frais « {$expense->label} » pour le projet : {$expense->project?->title}",
            'Suppression'
        );
    }

    public function restored(ProjectExpense $expense): void
    {
        $expense->project?->updateCalculations();

        $this->logAction(
            "Restauration d'un frais remboursable",
            "Restauration du frais « {$expense->label} » pour le projet : {$expense->project?->title}",
            'Restauration'
        );
    }

    public function forceDeleted(ProjectExpense $expense): void
    {
        $this->logAction(
            "Suppression définitive d'un frais remboursable",
            "Suppression définitive d'un frais pour le projet : {$expense->project?->title}",
            'Suppression définitive'
        );
    }
}
