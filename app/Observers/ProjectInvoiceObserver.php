<?php

namespace App\Observers;

use App\Models\ProjectInvoice;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ProjectInvoiceObserver
{
    /**
     * Handle the ProjectInvoice "created" event.
     */
    public function created(ProjectInvoice $projectInvoice): void
    {
        $projectInvoice->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'une facture de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree une facture de projet pour : {$projectInvoice->project->title} du numero {$projectInvoice->invoice_number}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the ProjectInvoice "updated" event.
     */
    public function updated(ProjectInvoice $projectInvoice): void
    {
        $projectInvoice->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'une facture de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié une facture de projet pour : {$projectInvoice->project->title} du numero {$projectInvoice->invoice_number}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the ProjectInvoice "deleted" event.
     */
    public function deleted(ProjectInvoice $projectInvoice): void
    {
        $projectInvoice->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'une facture de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé une facture de projet pour : {$projectInvoice->project->title} du numero {$projectInvoice->invoice_number}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the ProjectInvoice "restored" event.
     */
    public function restored(ProjectInvoice $projectInvoice): void
    {
        $projectInvoice->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'une facture de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré une facture de projet pour : {$projectInvoice->project->title} du numero {$projectInvoice->invoice_number}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the ProjectInvoice "force deleted" event.
     */
    public function forceDeleted(ProjectInvoice $projectInvoice): void
    {
        $projectInvoice->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'une facture de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement une facture de projet pour : {$projectInvoice->project->title} du numero {$projectInvoice->invoice_number}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
