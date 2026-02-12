<?php

namespace App\Observers;

use App\Models\AvisManifestation;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class AvisManifestationObserver
{
    /**
     * Handle the AvisManifestation "created" event.
     */
    public function created(AvisManifestation $avisManifestation): void
    {
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'avis de manifestation ";
        $secureView->description = "{$user->name}, identifié par le mail {$user->email} a créé un avis de manifestation nommé : {$avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = $avisManifestation->created_at;
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the AvisManifestation "updated" event.
     */
    public function updated(AvisManifestation $avisManifestation): void
    {
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'avis de manifestation ";
        $secureView->description = "{$user->name}, identifié par le mail {$user->email} a modifié un avis de manifestation nommé : {$avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = $avisManifestation->updated_at;
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the AvisManifestation "deleted" event.
     */
    public function deleted(AvisManifestation $avisManifestation): void
    {
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'avis de manifestation ";
        $secureView->description = "{$user->name}, identifié par le mail {$user->email} a supprimé un avis de manifestation nommé : {$avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = $avisManifestation->deleted_at;
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the AvisManifestation "restored" event.
     */
    public function restored(AvisManifestation $avisManifestation): void
    {  
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'avis de manifestation ";
        $secureView->description = "{$user->name}, identifié par le mail {$user->email} a restauré un avis de manifestation nommé : {$avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = $avisManifestation->updated_at;
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the AvisManifestation "force deleted" event.
     */
    public function forceDeleted(AvisManifestation $avisManifestation): void
    {  
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'avis de manifestation ";
        $secureView->description = "{$user->name}, identifié par le mail {$user->email} a supprimé définitivement un avis de manifestation nommé : {$avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = $avisManifestation->updated_at;
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
