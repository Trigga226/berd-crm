<?php

namespace App\Observers;

use App\Models\SecureView;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $authUser = Auth::user();

        if (!$authUser) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'un utilisateur ";
        $secureView->description = "{$authUser->name}, identifier par le mail {$authUser->email} a cree un utilisateur ayant pour email {$user->email}";
        $secureView->auteur = $authUser->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $authUser = Auth::user();

        if (!$authUser) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'un utilisateur ";
        $secureView->description = "{$authUser->name}, identifier par le mail {$authUser->email} a modifié un utilisateur ayant pour email {$user->email}";
        $secureView->auteur = $authUser->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $authUser = Auth::user();

        if (!$authUser) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'un utilisateur ";
        $secureView->description = "{$authUser->name}, identifier par le mail {$authUser->email} a supprimé un utilisateur ayant pour email {$user->email}";
        $secureView->auteur = $authUser->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $authUser = Auth::user();

        if (!$authUser) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'un utilisateur ";
        $secureView->description = "{$authUser->name}, identifier par le mail {$authUser->email} a restauré un utilisateur ayant pour email {$user->email}";
        $secureView->auteur = $authUser->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        $authUser = Auth::user();

        if (!$authUser) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'un utilisateur ";
        $secureView->description = "{$authUser->name}, identifier par le mail {$authUser->email} a supprimé définitivement un utilisateur ayant pour email {$user->email}";
        $secureView->auteur = $authUser->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
