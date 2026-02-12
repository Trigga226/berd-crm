<?php

namespace App\Observers;

use App\Models\ProjectExpertContract;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ProjectExpertContractObserver
{
    /**
     * Handle the ProjectExpertContract "created" event.
     */
    public function created(ProjectExpertContract $projectExpertContract): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un contrat expert de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un contrat expert de projet pour : {$projectExpertContract->project->title} du nom de {$projectExpertContract->expert->first_name} {$projectExpertContract->expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the ProjectExpertContract "updated" event.
     */
    public function updated(ProjectExpertContract $projectExpertContract): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un contrat expert de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un contrat expert de projet pour : {$projectExpertContract->project->title} du nom de {$projectExpertContract->expert->first_name} {$projectExpertContract->expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the ProjectExpertContract "deleted" event.
     */
    public function deleted(ProjectExpertContract $projectExpertContract): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un contrat expert de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un contrat expert de projet pour : {$projectExpertContract->project->title} du nom de {$projectExpertContract->expert->first_name} {$projectExpertContract->expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the ProjectExpertContract "restored" event.
     */
    public function restored(ProjectExpertContract $projectExpertContract): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un contrat expert de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un contrat expert de projet pour : {$projectExpertContract->project->title} du nom de {$projectExpertContract->expert->first_name} {$projectExpertContract->expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the ProjectExpertContract "force deleted" event.
     */
    public function forceDeleted(ProjectExpertContract $projectExpertContract): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un contrat expert de projet ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un contrat expert de projet pour : {$projectExpertContract->project->title} du nom de {$projectExpertContract->expert->first_name} {$projectExpertContract->expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
