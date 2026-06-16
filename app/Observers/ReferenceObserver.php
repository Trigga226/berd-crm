<?php

namespace App\Observers;

use App\Models\Reference;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReferenceObserver
{
    public function created(Reference $reference): void
    {
        $user = Auth::user();
        if (!$user) return;

        $secureView = new SecureView();
        $secureView->titre = "Création d'une référence";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a créé une référence : {$reference->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";
        $secureView->save();

        Cache::increment('berd_stats_version');
    }

    public function updated(Reference $reference): void
    {
        $user = Auth::user();
        if (!$user) return;

        $secureView = new SecureView();
        $secureView->titre = "Modification d'une référence";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié une référence : {$reference->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";
        $secureView->save();

        Cache::increment('berd_stats_version');
    }

    public function deleted(Reference $reference): void
    {
        $user = Auth::user();
        if (!$user) return;

        $secureView = new SecureView();
        $secureView->titre = "Suppression d'une référence";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé une référence : {$reference->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";
        $secureView->save();

        Cache::increment('berd_stats_version');
    }

    public function restored(Reference $reference): void
    {
        $user = Auth::user();
        if (!$user) return;

        $secureView = new SecureView();
        $secureView->titre = "Restauration d'une référence";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré une référence : {$reference->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";
        $secureView->save();
    }

    public function forceDeleted(Reference $reference): void
    {
        $user = Auth::user();
        if (!$user) return;

        $secureView = new SecureView();
        $secureView->titre = "Suppression définitive d'une référence";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement une référence : {$reference->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";
        $secureView->save();
    }
}
