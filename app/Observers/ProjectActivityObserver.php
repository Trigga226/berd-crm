<?php

namespace App\Observers;

use App\Models\ProjectActivity;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ProjectActivityObserver
{
    /**
     * Handle the ProjectActivity "created" event.
     */
    public function created(ProjectActivity $projectActivity): void
    {
        $projectActivity->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'une activité de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree une activité de projet pour : {$projectActivity->project->title} du nom de {$projectActivity->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the ProjectActivity "updated" event.
     */
    public function updated(ProjectActivity $projectActivity): void
    {
        $projectActivity->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'une activité de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié une activité de projet pour : {$projectActivity->project->title} du nom de {$projectActivity->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the ProjectActivity "deleted" event.
     */
    public function deleted(ProjectActivity $projectActivity): void
    {
        $projectActivity->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'une activité de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé une activité de projet pour : {$projectActivity->project->title} du nom de {$projectActivity->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the ProjectActivity "restored" event.
     */
    public function restored(ProjectActivity $projectActivity): void
    {
        $projectActivity->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'une activité de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré une activité de projet pour : {$projectActivity->project->title} du nom de {$projectActivity->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the ProjectActivity "force deleted" event.
     */
    public function forceDeleted(ProjectActivity $projectActivity): void
    {
        $projectActivity->project->updateCalculations();

        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'une activité de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement une activité de projet pour : {$projectActivity->project->title} du nom de {$projectActivity->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
