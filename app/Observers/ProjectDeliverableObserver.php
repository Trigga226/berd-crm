<?php

namespace App\Observers;

use App\Models\ProjectDeliverable;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ProjectDeliverableObserver
{
    /**
     * Handle the ProjectDeliverable "created" event.
     */
    public function created(ProjectDeliverable $projectDeliverable): void
    {
        $projectDeliverable->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un livrable de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un livrable de projet pour : {$projectDeliverable->project->title} du nom de {$projectDeliverable->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the ProjectDeliverable "updated" event.
     */
    public function updated(ProjectDeliverable $projectDeliverable): void
    {
        $projectDeliverable->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un livrable de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un livrable de projet pour : {$projectDeliverable->project->title} du nom de {$projectDeliverable->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the ProjectDeliverable "deleted" event.
     */
    public function deleted(ProjectDeliverable $projectDeliverable): void
    {
        $projectDeliverable->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un livrable de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un livrable de projet pour : {$projectDeliverable->project->title} du nom de {$projectDeliverable->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the ProjectDeliverable "restored" event.
     */
    public function restored(ProjectDeliverable $projectDeliverable): void
    {
        $projectDeliverable->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un livrable de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un livrable de projet pour : {$projectDeliverable->project->title} du nom de {$projectDeliverable->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the ProjectDeliverable "force deleted" event.
     */
    public function forceDeleted(ProjectDeliverable $projectDeliverable): void
    {
        $projectDeliverable->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un livrable de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un livrable de projet pour : {$projectDeliverable->project->title} du nom de {$projectDeliverable->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
