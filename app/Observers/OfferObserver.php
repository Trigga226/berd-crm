<?php

namespace App\Observers;

use App\Models\Offer;
use App\Models\SecureView;
use App\Services\ArchiveService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OfferObserver
{
    /**
     * Handle the Offer "created" event.
     */
    public function created(Offer $offer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'une offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree une offre  : {$offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();

        Cache::increment('berd_stats_version');
    }

    /**
     * Handle the Offer "updated" event.
     */
    public function updated(Offer $offer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'une offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié une offre : {$offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();

        Cache::increment('berd_stats_version');

        // Archivage automatique lorsque l'offre atteint un résultat terminal
        if ($offer->wasChanged('result') &&
            in_array($offer->result, ['won', 'lost', 'abandoned'])) {
            app(ArchiveService::class)->archiverOffre($offer);
        }
    }

    /**
     * Handle the Offer "deleted" event.
     */
    public function deleted(Offer $offer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'une offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé une offre : {$offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();

        Cache::increment('berd_stats_version');
    }

    /**
     * Handle the Offer "restored" event.
     */
    public function restored(Offer $offer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'une offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré une offre : {$offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the Offer "force deleted" event.
     */
    public function forceDeleted(Offer $offer): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'une offre ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement une offre : {$offer->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
