<?php

namespace App\Observers;

use App\Models\ManifestationUser;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ManifestationUserObserver
{
    /**
     * Handle the ManifestationUser "created" event.
     */
    public function created(ManifestationUser $manifestationUser): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un membre d'equipe de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un membre d'equipe de manifestation pour : {$manifestationUser->manifestation->avisManifestation->title} du nom de {$manifestationUser->user->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the ManifestationUser "updated" event.
     */
    public function updated(ManifestationUser $manifestationUser): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un membre d'equipe de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un membre d'equipe de manifestation pour : {$manifestationUser->manifestation->avisManifestation->title} du nom de {$manifestationUser->user->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the ManifestationUser "deleted" event.
     */
    public function deleted(ManifestationUser $manifestationUser): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un membre d'equipe de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un membre d'equipe de manifestation pour : {$manifestationUser->manifestation->avisManifestation->title} du nom de {$manifestationUser->user->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the ManifestationUser "restored" event.
     */
    public function restored(ManifestationUser $manifestationUser): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un membre d'equipe de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un membre d'equipe de manifestation pour : {$manifestationUser->manifestation->avisManifestation->title} du nom de {$manifestationUser->user->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the ManifestationUser "force deleted" event.
     */
    public function forceDeleted(ManifestationUser $manifestationUser): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un membre d'equipe de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un membre d'equipe de manifestation pour : {$manifestationUser->manifestation->avisManifestation->title} du nom de {$manifestationUser->user->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
