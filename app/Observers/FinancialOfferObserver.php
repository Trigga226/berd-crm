<?php

namespace App\Observers;

use App\Models\FinancialOffer;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class FinancialOfferObserver
{
    /**
     * Handle the FinancialOffer "created" event.
     */
    public function created(FinancialOffer $financialOffer): void
    {
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création de l'offre financière ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree une offre financière pour : {$financialOffer->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the FinancialOffer "updated" event.
     */
    public function updated(FinancialOffer $financialOffer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification de l'offre financière ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié l'offre financière pour : {$financialOffer->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the FinancialOffer "deleted" event.
     */
    public function deleted(FinancialOffer $financialOffer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression de l'offre financière ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé l'offre financière pour : {$financialOffer->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the FinancialOffer "restored" event.
     */
    public function restored(FinancialOffer $financialOffer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration de l'offre financière ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré l'offre financière pour : {$financialOffer->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the FinancialOffer "force deleted" event.
     */
    public function forceDeleted(FinancialOffer $financialOffer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive de l'offre financière ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement l'offre financière pour : {$financialOffer->offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
