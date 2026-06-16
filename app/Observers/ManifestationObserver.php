<?php

namespace App\Observers;

use App\Models\Expert;
use App\Models\Manifestation;
use App\Models\SecureView;
use App\Services\ArchiveService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ManifestationObserver
{
    /**
     * Handle the Manifestation "created" event.
     */
    public function created(Manifestation $manifestation): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Création d'une manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a cree une manifestation pour : {$manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Création";

        $secureView->save();

        Cache::increment('berd_stats_version');
    }

    /**
     * Handle the Manifestation "updated" event.
     */
    public function updated(Manifestation $manifestation): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Modification d'une manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a modifié une manifestation pour : {$manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Modification";

        $secureView->save();

        Cache::increment('berd_stats_version');

        // Archivage automatique lorsque la manifestation atteint un statut terminal
        if ($manifestation->wasChanged('status') &&
            in_array($manifestation->status, ['won', 'lost', 'abandoned'])) {
            app(ArchiveService::class)->archiverManifestation($manifestation);
        }

        // Mise à jour du compteur de manifestations gagnées sur les experts liés
        if ($manifestation->wasChanged('status') && $manifestation->status === 'won') {
            $manifestation->loadMissing('experts');
            $manifestation->experts->each(function (Expert $expert) {
                $count = $expert->manifestations()->where('status', 'won')->count();
                $expert->updateQuietly(['won_manifestations_count' => $count]);
            });
        }
    }

    /**
     * Handle the Manifestation "deleted" event.
     */
    public function deleted(Manifestation $manifestation): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression d'une manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé une manifestation pour : {$manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression";

        $secureView->save();

        Cache::increment('berd_stats_version');
    }

    /**
     * Handle the Manifestation "restored" event.
     */
    public function restored(Manifestation $manifestation): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Restauration d'une manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a restauré une manifestation pour : {$manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Restauration";

        $secureView->save();
    }

    /**
     * Handle the Manifestation "force deleted" event.
     */
    public function forceDeleted(Manifestation $manifestation): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();

        $secureView->titre = "Suppression définitive d'une manifestation ";
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} a supprimé définitivement une manifestation pour : {$manifestation->avisManifestation->title}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = "Suppression définitive";

        $secureView->save();
    }
}
