<?php

namespace App\Observers;

use App\Models\ProjectExpertContract;
use App\Observers\Concerns\LogsToSecureView;

class ProjectExpertContractObserver
{
    use LogsToSecureView;

    /**
     * Handle the ProjectExpertContract "created" event.
     */
    public function created(ProjectExpertContract $projectExpertContract): void
    {
        // Recalcul du budget consommé du projet
        $projectExpertContract->project?->updateCalculations();

        $expertName = $projectExpertContract->expert
            ? "{$projectExpertContract->expert->first_name} {$projectExpertContract->expert->last_name}"
            : 'N/A';

        $this->logAction(
            "Création d'un contrat expert de projet",
            "Création du contrat expert ({$expertName}) pour le projet : {$projectExpertContract->project?->title}",
            'Création'
        );
    }

    /**
     * Handle the ProjectExpertContract "updated" event.
     */
    public function updated(ProjectExpertContract $projectExpertContract): void
    {
        $projectExpertContract->project?->updateCalculations();

        $expertName = $projectExpertContract->expert
            ? "{$projectExpertContract->expert->first_name} {$projectExpertContract->expert->last_name}"
            : 'N/A';

        $this->logAction(
            "Modification d'un contrat expert de projet",
            "Modification du contrat expert ({$expertName}) pour le projet : {$projectExpertContract->project?->title}",
            'Modification'
        );
    }

    /**
     * Handle the ProjectExpertContract "deleted" event.
     */
    public function deleted(ProjectExpertContract $projectExpertContract): void
    {
        $projectExpertContract->project?->updateCalculations();

        $expertName = $projectExpertContract->expert
            ? "{$projectExpertContract->expert->first_name} {$projectExpertContract->expert->last_name}"
            : 'N/A';

        $this->logAction(
            "Suppression d'un contrat expert de projet",
            "Suppression du contrat expert ({$expertName}) pour le projet : {$projectExpertContract->project?->title}",
            'Suppression'
        );
    }

    /**
     * Handle the ProjectExpertContract "restored" event.
     */
    public function restored(ProjectExpertContract $projectExpertContract): void
    {
        $projectExpertContract->project?->updateCalculations();

        $expertName = $projectExpertContract->expert
            ? "{$projectExpertContract->expert->first_name} {$projectExpertContract->expert->last_name}"
            : 'N/A';

        $this->logAction(
            "Restauration d'un contrat expert de projet",
            "Restauration du contrat expert ({$expertName}) pour le projet : {$projectExpertContract->project?->title}",
            'Restauration'
        );
    }

    /**
     * Handle the ProjectExpertContract "force deleted" event.
     */
    public function forceDeleted(ProjectExpertContract $projectExpertContract): void
    {
        $this->logAction(
            "Suppression définitive d'un contrat expert de projet",
            "Suppression définitive du contrat expert pour le projet : {$projectExpertContract->project?->title}",
            'Suppression définitive'
        );
    }
}
