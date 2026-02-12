<?php

namespace App\Observers;

use App\Models\ProjectReport;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ProjectReportObserver
{
    /**
     * Handle the ProjectReport "created" event.
     */
    public function created(ProjectReport $projectReport): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un rapport de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un rapport de projet pour : {$projectReport->project->title} ayant pour titre {$projectReport->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the ProjectReport "updated" event.
     */
    public function updated(ProjectReport $projectReport): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un rapport de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un rapport de projet pour : {$projectReport->project->title} ayant pour titre {$projectReport->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the ProjectReport "deleted" event.
     */
    public function deleted(ProjectReport $projectReport): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un rapport de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un rapport de projet pour : {$projectReport->project->title} ayant pour titre {$projectReport->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the ProjectReport "restored" event.
     */
    public function restored(ProjectReport $projectReport): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un rapport de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un rapport de projet pour : {$projectReport->project->title} ayant pour titre {$projectReport->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the ProjectReport "force deleted" event.
     */
    public function forceDeleted(ProjectReport $projectReport): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un rapport de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un rapport de projet pour : {$projectReport->project->title} ayant pour titre {$projectReport->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
