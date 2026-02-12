<?php

namespace App\Observers;

use App\Models\OfferPartner;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class OfferPartnerObserver
{
    /**
     * Handle the OfferPartner "created" event.
     */
    public function created(OfferPartner $offerPartner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un partenaire d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un partenaire d'offre pour : {$offerPartner->offer->title} avec le partenaire : {$offerPartner->partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the OfferPartner "updated" event.
     */
    public function updated(OfferPartner $offerPartner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un partenaire d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un partenaire d'offre pour : {$offerPartner->offer->title} avec le partenaire : {$offerPartner->partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the OfferPartner "deleted" event.
     */
    public function deleted(OfferPartner $offerPartner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un partenaire d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un partenaire d'offre pour : {$offerPartner->offer->title} avec le partenaire : {$offerPartner->partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the OfferPartner "restored" event.
     */
    public function restored(OfferPartner $offerPartner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un partenaire d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un partenaire d'offre pour : {$offerPartner->offer->title} avec le partenaire : {$offerPartner->partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the OfferPartner "force deleted" event.
     */
    public function forceDeleted(OfferPartner $offerPartner): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un partenaire d'offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un partenaire d'offre pour : {$offerPartner->offer->title} avec le partenaire : {$offerPartner->partner->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
