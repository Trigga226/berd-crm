<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ClientObserver
{
    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client): void
    {
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création du client ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un client nommé : {$client->name}";
        $secureView->auteur = $user->id;
        $secureView->date = $client->created_at;
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the Client "updated" event.
     */
    public function updated(Client $client): void
    {
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification du client ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un client nommé : {$client->name}";
        $secureView->auteur = $user->id;
        $secureView->date = $client->updated_at;
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression du client ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un client nommé : {$client->name}";
        $secureView->auteur = $user->id;
        $secureView->date = $client->deleted_at;
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the Client "restored" event.
     */
    public function restored(Client $client): void
    {  
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration du client ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un client nommé : {$client->name}";
        $secureView->auteur = $user->id;
        $secureView->date = $client->updated_at;
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the Client "force deleted" event.
     */
    public function forceDeleted(Client $client): void
    {  
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive du client ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un client nommé : {$client->name}";
        $secureView->auteur = $user->id;
        $secureView->date = $client->updated_at;
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
