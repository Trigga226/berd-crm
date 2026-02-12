<?php

namespace App\Observers;

use App\Models\SecureView;
use App\Models\TechnicalOffer;
use Illuminate\Support\Facades\Auth;

class TechnicalOfferObserver
{
    /**
     * Handle the TechnicalOffer "created" event.
     */
    public function created(TechnicalOffer $technicalOffer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'une offre technique ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree une offre technique pour : {$technicalOffer->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the TechnicalOffer "updated" event.
     */
    public function updated(TechnicalOffer $technicalOffer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'une offre technique ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié une offre technique pour : {$technicalOffer->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the TechnicalOffer "deleted" event.
     */
    public function deleted(TechnicalOffer $technicalOffer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'une offre technique ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé une offre technique pour : {$technicalOffer->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the TechnicalOffer "restored" event.
     */
    public function restored(TechnicalOffer $technicalOffer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'une offre technique ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré une offre technique pour : {$technicalOffer->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the TechnicalOffer "force deleted" event.
     */
    public function forceDeleted(TechnicalOffer $technicalOffer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'une offre technique ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement une offre technique pour : {$technicalOffer->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
