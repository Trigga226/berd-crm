<?php

namespace App\Observers;

use App\Models\Manifestation;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ManifestationObserver
{
    /**
     * Handle the Manifestation "created" event.
     */
    public function created(Manifestation $manifestation): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'une manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree une manifestation pour : {$manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the Manifestation "updated" event.
     */
    public function updated(Manifestation $manifestation): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'une manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié une manifestation pour : {$manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the Manifestation "deleted" event.
     */
    public function deleted(Manifestation $manifestation): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'une manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé une manifestation pour : {$manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the Manifestation "restored" event.
     */
    public function restored(Manifestation $manifestation): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'une manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré une manifestation pour : {$manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the Manifestation "force deleted" event.
     */
    public function forceDeleted(Manifestation $manifestation): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'une manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement une manifestation pour : {$manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
