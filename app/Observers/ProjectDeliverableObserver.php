<?php

namespace App\Observers;

use App\Models\ProjectDeliverable;
use App\Observers\Concerns\LogsToSecureView;

class ProjectDeliverableObserver
{
    use LogsToSecureView;

    public function created(ProjectDeliverable $projectDeliverable): void
    {
        $projectDeliverable->project?->updateCalculations();

        $this->logAction(
            "Création d'un livrable de projet",
            "Création du livrable '{$projectDeliverable->title}' pour le projet : {$projectDeliverable->project?->title}",
            'Création'
        );
    }

    public function updated(ProjectDeliverable $projectDeliverable): void
    {
        $projectDeliverable->project?->updateCalculations();

        $this->logAction(
            "Modification d'un livrable de projet",
            "Modification du livrable '{$projectDeliverable->title}' pour le projet : {$projectDeliverable->project?->title}",
            'Modification'
        );
    }

    public function deleted(ProjectDeliverable $projectDeliverable): void
    {
        $projectDeliverable->project?->updateCalculations();

        $this->logAction(
            "Suppression d'un livrable de projet",
            "Suppression du livrable '{$projectDeliverable->title}' pour le projet : {$projectDeliverable->project?->title}",
            'Suppression'
        );
    }

    public function restored(ProjectDeliverable $projectDeliverable): void
    {
        $projectDeliverable->project?->updateCalculations();

        $this->logAction(
            "Restauration d'un livrable de projet",
            "Restauration du livrable '{$projectDeliverable->title}' pour le projet : {$projectDeliverable->project?->title}",
            'Restauration'
        );
    }

    public function forceDeleted(ProjectDeliverable $projectDeliverable): void
    {
        $this->logAction(
            "Suppression définitive d'un livrable de projet",
            "Suppression définitive du livrable '{$projectDeliverable->title}' pour le projet : {$projectDeliverable->project?->title}",
            'Suppression définitive'
        );
    }
}
