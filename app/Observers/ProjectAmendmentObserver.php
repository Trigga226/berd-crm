<?php

namespace App\Observers;

use App\Models\ProjectAmendment;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ProjectAmendmentObserver
{
    /**
     * Handle the ProjectAmendment "created" event.
     */
    public function created(ProjectAmendment $projectAmendment): void
    {
        $projectAmendment->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un avenant de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un avenant de projet pour : {$projectAmendment->project->title} du nom de {$projectAmendment->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the ProjectAmendment "updated" event.
     */
    public function updated(ProjectAmendment $projectAmendment): void
    {
        $projectAmendment->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un avenant de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un avenant de projet pour : {$projectAmendment->project->title} du nom de {$projectAmendment->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the ProjectAmendment "deleted" event.
     */
    public function deleted(ProjectAmendment $projectAmendment): void
    {
        $projectAmendment->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un avenant de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un avenant de projet pour : {$projectAmendment->project->title} du nom de {$projectAmendment->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the ProjectAmendment "restored" event.
     */
    public function restored(ProjectAmendment $projectAmendment): void
    {
        $projectAmendment->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un avenant de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un avenant de projet pour : {$projectAmendment->project->title} du nom de {$projectAmendment->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the ProjectAmendment "force deleted" event.
     */
    public function forceDeleted(ProjectAmendment $projectAmendment): void
    {
        $projectAmendment->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un avenant de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un avenant de projet pour : {$projectAmendment->project->title} du nom de {$projectAmendment->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
