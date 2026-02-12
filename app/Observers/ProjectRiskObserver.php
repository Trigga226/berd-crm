<?php

namespace App\Observers;

use App\Models\ProjectRisk;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ProjectRiskObserver
{
    /**
     * Handle the ProjectRisk "created" event.
     */
    public function created(ProjectRisk $projectRisk): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un risque de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un risque de projet pour : {$projectRisk->project->title} ayant pour titre {$projectRisk->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the ProjectRisk "updated" event.
     */
    public function updated(ProjectRisk $projectRisk): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un risque de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un risque de projet pour : {$projectRisk->project->title} ayant pour titre {$projectRisk->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the ProjectRisk "deleted" event.
     */
    public function deleted(ProjectRisk $projectRisk): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un risque de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un risque de projet pour : {$projectRisk->project->title} ayant pour titre {$projectRisk->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the ProjectRisk "restored" event.
     */
    public function restored(ProjectRisk $projectRisk): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un risque de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un risque de projet pour : {$projectRisk->project->title} ayant pour titre {$projectRisk->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the ProjectRisk "force deleted" event.
     */
    public function forceDeleted(ProjectRisk $projectRisk): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un risque de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un risque de projet pour : {$projectRisk->project->title} ayant pour titre {$projectRisk->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
