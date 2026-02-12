<?php

namespace App\Observers;

use App\Models\ManifestationExpert;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ManifestationExpertObserver
{
    /**
     * Handle the ManifestationExpert "created" event.
     */
    public function created(ManifestationExpert $manifestationExpert): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'expert de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un expert de manifestation pour : {$manifestationExpert->manifestation->avisManifestation->title} du nom de {$manifestationExpert->expert->first_name} {$manifestationExpert->expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the ManifestationExpert "updated" event.
     */
    public function updated(ManifestationExpert $manifestationExpert): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'expert de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un expert de manifestation pour : {$manifestationExpert->manifestation->avisManifestation->title} du nom de {$manifestationExpert->expert->first_name} {$manifestationExpert->expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the ManifestationExpert "deleted" event.
     */
    public function deleted(ManifestationExpert $manifestationExpert): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'expert de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un expert de manifestation pour : {$manifestationExpert->manifestation->avisManifestation->title} du nom de {$manifestationExpert->expert->first_name} {$manifestationExpert->expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the ManifestationExpert "restored" event.
     */
    public function restored(ManifestationExpert $manifestationExpert): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'expert de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un expert de manifestation pour : {$manifestationExpert->manifestation->avisManifestation->title} du nom de {$manifestationExpert->expert->first_name} {$manifestationExpert->expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the ManifestationExpert "force deleted" event.
     */
    public function forceDeleted(ManifestationExpert $manifestationExpert): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'expert de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un expert de manifestation pour : {$manifestationExpert->manifestation->avisManifestation->title} du nom de {$manifestationExpert->expert->first_name} {$manifestationExpert->expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
