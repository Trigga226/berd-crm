<?php

namespace App\Observers;

use App\Models\OfferUser;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class OfferUserObserver
{
    /**
     * Handle the OfferUser "created" event.
     */
    public function created(OfferUser $offerUser): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un membre d'equipe d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un membre d'equipe d'offre pour : {$offerUser->offer->title} du nom de {$offerUser->user->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the OfferUser "updated" event.
     */
    public function updated(OfferUser $offerUser): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un membre d'equipe d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un membre d'equipe d'offre pour : {$offerUser->offer->title} du nom de {$offerUser->user->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the OfferUser "deleted" event.
     */
    public function deleted(OfferUser $offerUser): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un membre d'equipe d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un membre d'equipe d'offre pour : {$offerUser->offer->title} du nom de {$offerUser->user->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the OfferUser "restored" event.
     */
    public function restored(OfferUser $offerUser): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un membre d'equipe d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un membre d'equipe d'offre pour : {$offerUser->offer->title} du nom de {$offerUser->user->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the OfferUser "force deleted" event.
     */
    public function forceDeleted(OfferUser $offerUser): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un membre d'equipe d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un membre d'equipe d'offre pour : {$offerUser->offer->title} du nom de {$offerUser->user->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
