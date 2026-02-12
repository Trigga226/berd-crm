<?php

namespace App\Observers;

use App\Models\OfferDocument;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class OfferDocumentObserver
{
    /**
     * Handle the OfferDocument "created" event.
     */
    public function created(OfferDocument $offerDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un document d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un document d'offre pour : {$offerDocument->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the OfferDocument "updated" event.
     */
    public function updated(OfferDocument $offerDocument): void
    {
            $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un document d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un document d'offre pour : {$offerDocument->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the OfferDocument "deleted" event.
     */
    public function deleted(OfferDocument $offerDocument): void
    {
            $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un document d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un document d'offre pour : {$offerDocument->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the OfferDocument "restored" event.
     */
    public function restored(OfferDocument $offerDocument): void
    {
            $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un document d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un document d'offre pour : {$offerDocument->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the OfferDocument "force deleted" event.
     */
    public function forceDeleted(OfferDocument $offerDocument): void
    {
            $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un document d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un document d'offre pour : {$offerDocument->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
