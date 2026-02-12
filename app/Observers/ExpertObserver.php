<?php

namespace App\Observers;

use App\Models\Expert;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class ExpertObserver
{
    /**
     * Handle the Expert "created" event.
     */
    public function created(Expert $expert): void
    {
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création de l'expert ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un expert nommé : {$expert->first_name} {$expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = $expert->created_at;
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the Expert "updated" event.
     */
    public function updated(Expert $expert): void
    {  
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification de l'expert ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un expert nommé : {$expert->first_name} {$expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = $expert->updated_at;
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the Expert "deleted" event.
     */
    public function deleted(Expert $expert): void
    {  
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression de l'expert ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un expert nommé : {$expert->first_name} {$expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = $expert->deleted_at;
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the Expert "restored" event.
     */
    public function restored(Expert $expert): void
    {  
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration de l'expert ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un expert nommé : {$expert->first_name} {$expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = $expert->updated_at;
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the Expert "force deleted" event.
     */
    public function forceDeleted(Expert $expert): void
    {  
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive de l'expert ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un expert nommé : {$expert->first_name} {$expert->last_name}";
        $secureView->auteur = $user->id;
        $secureView->date = $expert->updated_at;
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
