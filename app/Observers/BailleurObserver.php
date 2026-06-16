<?php

namespace App\Observers;

use App\Models\Bailleur;
use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

class BailleurObserver
{
    public function created(Bailleur $bailleur): void
    {
        $this->log("Création d'un bailleur ", 'a cree un bailleur du nom de', $bailleur, 'Création');
    }

    public function updated(Bailleur $bailleur): void
    {
        $this->log("Modification d'un bailleur ", 'a modifié un bailleur du nom de', $bailleur, 'Modification');
    }

    public function deleted(Bailleur $bailleur): void
    {
        $this->log("Suppression d'un bailleur ", 'a supprimé un bailleur du nom de', $bailleur, 'Suppression');
    }

    public function restored(Bailleur $bailleur): void
    {
        $this->log("Restauration d'un bailleur ", 'a restauré un bailleur du nom de', $bailleur, 'Restauration');
    }

    public function forceDeleted(Bailleur $bailleur): void
    {
        $this->log("Suppression définitive d'un bailleur ", 'a supprimé définitivement un bailleur du nom de', $bailleur, 'Suppression définitive');
    }

    private function log(string $titre, string $verbe, Bailleur $bailleur, string $type): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $secureView = new SecureView();
        $secureView->titre = $titre;
        $secureView->description = "{$user->name}, identifier par le mail {$user->email} {$verbe} : {$bailleur->name}";
        $secureView->auteur = $user->id;
        $secureView->date = date('Y-m-d H:i:s');
        $secureView->type = $type;
        $secureView->save();
    }
}
