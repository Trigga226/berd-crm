<?php

namespace App\Observers;

use App\Models\ManifestationPartner;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ManifestationPartnerObserver
{
    /**
     * Handle the ManifestationPartner "created" event.
     */
    public function created(ManifestationPartner $manifestationPartner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un partenaire de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un partenaire de manifestation pour : {$manifestationPartner->manifestation->avisManifestation->title} du nom de {$manifestationPartner->partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the ManifestationPartner "updated" event.
     */
    public function updated(ManifestationPartner $manifestationPartner): void
    {
            $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un partenaire de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un partenaire de manifestation pour : {$manifestationPartner->manifestation->avisManifestation->title} du nom de {$manifestationPartner->partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the ManifestationPartner "deleted" event.
     */
    public function deleted(ManifestationPartner $manifestationPartner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un partenaire de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un partenaire de manifestation pour : {$manifestationPartner->manifestation->avisManifestation->title} du nom de {$manifestationPartner->partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the ManifestationPartner "restored" event.
     */
    public function restored(ManifestationPartner $manifestationPartner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un partenaire de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un partenaire de manifestation pour : {$manifestationPartner->manifestation->avisManifestation->title} du nom de {$manifestationPartner->partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the ManifestationPartner "force deleted" event.
     */
    public function forceDeleted(ManifestationPartner $manifestationPartner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un partenaire de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un partenaire de manifestation pour : {$manifestationPartner->manifestation->avisManifestation->title} du nom de {$manifestationPartner->partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
