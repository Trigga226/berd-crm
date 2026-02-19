<?php

namespace App\Observers;

use App\Models\Archive;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ArchiveObserver
{
    /**
     * Handle the Archive "created" event.
     */
    public function created(Archive $archive): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'une archive ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree une archive nommée : {$archive->titre}";
        $secureView->auteur = $user->id;
        $secureView->date = $archive->created_at;
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the Archive "updated" event.
     */
    public function updated(Archive $archive): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'une archive ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifie une archive nommée : {$archive->titre}";
        $secureView->auteur = $user->id;
        $secureView->date = $archive->updated_at;
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the Archive "deleted" event.
     */
    public function deleted(Archive $archive): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'une archive ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprime une archive nommée : {$archive->titre}";
        $secureView->auteur = $user->id;
        $secureView->date = $archive->deleted_at;
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the Archive "restored" event.
     */
    public function restored(Archive $archive): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'une archive ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restaure une archive nommée : {$archive->titre}";
        $secureView->auteur = $user->id;
        $secureView->date = $archive->updated_at;
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the Archive "force deleted" event.
     */
    public function forceDeleted(Archive $archive): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression definitive d'une archive ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprime definitivement une archive nommée : {$archive->titre}";
        $secureView->auteur = $user->id;
        $secureView->date = $archive->deleted_at;
        $secureView->type = "Suppression definitive";

        $secureView->save();
    }
}
