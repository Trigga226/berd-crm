<?php

namespace App\Observers;

use App\Models\Poste;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class PosteObserver
{
    /**
     * Handle the Poste "created" event.
     */
    public function created(Poste $poste): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un poste ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un poste du nom de {$poste->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the Poste "updated" event.
     */
    public function updated(Poste $poste): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un poste ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un poste du nom de {$poste->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the Poste "deleted" event.
     */
    public function deleted(Poste $poste): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un poste ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un poste du nom de {$poste->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the Poste "restored" event.
     */
    public function restored(Poste $poste): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un poste ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un poste du nom de {$poste->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the Poste "force deleted" event.
     */
    public function forceDeleted(Poste $poste): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un poste ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un poste du nom de {$poste->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
