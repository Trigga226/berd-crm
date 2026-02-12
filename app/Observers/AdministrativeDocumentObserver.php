<?php

namespace App\Observers;

use App\Models\AdministrativeDocument;
use App\Models\SecureView;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use function Symfony\Component\Clock\now;

class AdministrativeDocumentObserver
{
    /**
     * Handle the AdministrativeDocument "created" event.
     */
    public function created(AdministrativeDocument $administrativeDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création du document administratif ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un document administratif nommé : {$administrativeDocument->title}";
        $secureView->auteur = $user->id;
        $secureView->date = $administrativeDocument->created_at;
        $secureView->type = "Création";

        $secureView->save();
    }


    /**
     * Handle the AdministrativeDocument "updated" event.
     */
    public function updated(AdministrativeDocument $administrativeDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification du document administratif ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un document administratif nommé : {$administrativeDocument->title}";
        $secureView->auteur = $user->id;
        $secureView->date = $administrativeDocument->updated_at;
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the AdministrativeDocument "deleted" event.
     */
    public function deleted(AdministrativeDocument $administrativeDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression du document administratif ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un document administratif nommé : {$administrativeDocument->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date("Y-m-d H:i:s");
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the AdministrativeDocument "restored" event.
     */
    public function restored(AdministrativeDocument $administrativeDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }
        $secureView = new SecureView();

        $secureView->titre = "Restauration du document administratif ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un document administratif nommé : {$administrativeDocument->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date("Y-m-d H:i:s");
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the AdministrativeDocument "force deleted" event.
     */
    public function forceDeleted(AdministrativeDocument $administrativeDocument): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive du document administratif ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un document administratif nommé : {$administrativeDocument->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date("Y-m-d H:i:s");
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }

  
}
