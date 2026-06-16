<?php

namespace App\Observers;

use App\Models\ProjectInvoice;
use App\Observers\Concerns\LogsToSecureView;
use Illuminate\Support\Facades\Cache;

class ProjectInvoiceObserver
{
    use LogsToSecureView;

    public function created(ProjectInvoice $projectInvoice): void
    {
        $projectInvoice->project?->updateCalculations();
        Cache::increment('berd_stats_version');

        $this->logAction(
            "Création d'une facture de projet",
            "Création de la facture N° {$projectInvoice->invoice_number} pour le projet : {$projectInvoice->project?->title}",
            'Création'
        );
    }

    public function updated(ProjectInvoice $projectInvoice): void
    {
        $projectInvoice->project?->updateCalculations();
        Cache::increment('berd_stats_version');

        $this->logAction(
            "Modification d'une facture de projet",
            "Modification de la facture N° {$projectInvoice->invoice_number} pour le projet : {$projectInvoice->project?->title}",
            'Modification'
        );
    }

    public function deleted(ProjectInvoice $projectInvoice): void
    {
        $projectInvoice->project?->updateCalculations();
        Cache::increment('berd_stats_version');

        $this->logAction(
            "Suppression d'une facture de projet",
            "Suppression de la facture N° {$projectInvoice->invoice_number} pour le projet : {$projectInvoice->project?->title}",
            'Suppression'
        );
    }

    public function restored(ProjectInvoice $projectInvoice): void
    {
        $projectInvoice->project?->updateCalculations();

        $this->logAction(
            "Restauration d'une facture de projet",
            "Restauration de la facture N° {$projectInvoice->invoice_number} pour le projet : {$projectInvoice->project?->title}",
            'Restauration'
        );
    }

    public function forceDeleted(ProjectInvoice $projectInvoice): void
    {
        $this->logAction(
            "Suppression définitive d'une facture de projet",
            "Suppression définitive de la facture N° {$projectInvoice->invoice_number} pour le projet : {$projectInvoice->project?->title}",
            'Suppression définitive'
        );
    }
}
