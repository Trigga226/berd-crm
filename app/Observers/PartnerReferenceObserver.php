<?php

namespace App\Observers;

use App\Models\PartnerReference;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class PartnerReferenceObserver
{
    /**
     * Handle the PartnerReference "created" event.
     */
    public function created(PartnerReference $partnerReference): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'une référence de partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree une référence de partenaire pour : {$partnerReference->partner->name} du nom de {$partnerReference->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();
    }

    /**
     * Handle the PartnerReference "updated" event.
     */
    public function updated(PartnerReference $partnerReference): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'une référence de partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié une référence de partenaire pour : {$partnerReference->partner->name} du nom de {$partnerReference->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();
    }

    /**
     * Handle the PartnerReference "deleted" event.
     */
    public function deleted(PartnerReference $partnerReference): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'une référence de partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé une référence de partenaire pour : {$partnerReference->partner->name} du nom de {$partnerReference->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();
    }

    /**
     * Handle the PartnerReference "restored" event.
     */
    public function restored(PartnerReference $partnerReference): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'une référence de partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré une référence de partenaire pour : {$partnerReference->partner->name} du nom de {$partnerReference->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the PartnerReference "force deleted" event.
     */
    public function forceDeleted(PartnerReference $partnerReference): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'une référence de partenaire ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement une référence de partenaire pour : {$partnerReference->partner->name} du nom de {$partnerReference->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
