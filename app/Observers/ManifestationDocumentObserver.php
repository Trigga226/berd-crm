<?php

namespace App\Observers;

use App\Models\ManifestationDocument;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ManifestationDocumentObserver
{
    /**
     * Handle the ManifestationDocument "created" event.
     */
    public function created(ManifestationDocument $manifestationDocument): void
    {
          $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création de document de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un document de manifestation pour : {$manifestationDocument->manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the ManifestationDocument "updated" event.
     */
    public function updated(ManifestationDocument $manifestationDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification de document de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un document de manifestation pour : {$manifestationDocument->manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the ManifestationDocument "deleted" event.
     */
    public function deleted(ManifestationDocument $manifestationDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression de document de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un document de manifestation pour : {$manifestationDocument->manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the ManifestationDocument "restored" event.
     */
    public function restored(ManifestationDocument $manifestationDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration de document de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un document de manifestation pour : {$manifestationDocument->manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the ManifestationDocument "force deleted" event.
     */
    public function forceDeleted(ManifestationDocument $manifestationDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive de document de manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un document de manifestation pour : {$manifestationDocument->manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
