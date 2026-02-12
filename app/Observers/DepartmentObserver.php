<?php

namespace App\Observers;

use App\Models\Department;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class DepartmentObserver
{
    /**
     * Handle the Department "created" event.
     */
    public function created(Department $department): void
    {
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création du département ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree un département nommé : {$department->name}";
        $secureView->auteur = $user->id;
        $secureView->date = $department->created_at;
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the Department "updated" event.
     */
    public function updated(Department $department): void
    {  
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification du département ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié un département nommé : {$department->name}";
        $secureView->auteur = $user->id;
        $secureView->date = $department->updated_at;
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the Department "deleted" event.
     */
    public function deleted(Department $department): void
    {  
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression du département ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé un département nommé : {$department->name}";
        $secureView->auteur = $user->id;
        $secureView->date = $department->deleted_at;
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the Department "restored" event.
     */
    public function restored(Department $department): void
    {  
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration du département ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré un département nommé : {$department->name}";
        $secureView->auteur = $user->id;
        $secureView->date = $department->updated_at;
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the Department "force deleted" event.
     */
    public function forceDeleted(Department $department): void
    {    
         $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive du département ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement un département nommé : {$department->name}";
        $secureView->auteur = $user->id;
        $secureView->date = $department->updated_at;
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
