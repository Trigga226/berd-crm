<?php

namespace App\Observers;

use App\Models\PartnerDocument;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class PartnerDocumentObserver
{
    /**
     * Handle the PartnerDocument "created" event.
     */
    public function created(PartnerDocument $partnerDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un document de partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un document de partenaire pour : {$partnerDocument->partner->name} du nom de {$partnerDocument->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the PartnerDocument "updated" event.
     */
    public function updated(PartnerDocument $partnerDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un document de partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un document de partenaire pour : {$partnerDocument->partner->name} du nom de {$partnerDocument->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the PartnerDocument "deleted" event.
     */
    public function deleted(PartnerDocument $partnerDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un document de partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un document de partenaire pour : {$partnerDocument->partner->name} du nom de {$partnerDocument->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the PartnerDocument "restored" event.
     */
    public function restored(PartnerDocument $partnerDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un document de partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un document de partenaire pour : {$partnerDocument->partner->name} du nom de {$partnerDocument->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the PartnerDocument "force deleted" event.
     */
    public function forceDeleted(PartnerDocument $partnerDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un document de partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un document de partenaire pour : {$partnerDocument->partner->name} du nom de {$partnerDocument->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
