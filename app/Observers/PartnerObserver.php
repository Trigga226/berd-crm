<?php

namespace App\Observers;

use App\Models\Partner;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class PartnerObserver
{
    /**
     * Handle the Partner "created" event.
     */
    public function created(Partner $partner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un partenaire du nom de : {$partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the Partner "updated" event.
     */
    public function updated(Partner $partner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un partenaire du nom de : {$partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the Partner "deleted" event.
     */
    public function deleted(Partner $partner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un partenaire du nom de : {$partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the Partner "restored" event.
     */
    public function restored(Partner $partner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un partenaire du nom de : {$partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the Partner "force deleted" event.
     */
    public function forceDeleted(Partner $partner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un partenaire du nom de : {$partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
