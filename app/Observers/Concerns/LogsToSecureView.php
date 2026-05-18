<?php

namespace App\Observers\Concerns;

use App\Models\SecureView;
use Illuminate\Support\Facades\Auth;

/**
 * Trait réutilisable pour enregistrer les actions dans la table secure_views.
 * Évite la duplication de code dans les 30+ Observers du projet.
 */
trait LogsToSecureView
{
    /**
     * Enregistre une action dans la table secure_views si un utilisateur est authentifié.
     */
    protected function logAction(string $titre, string $description, string $type): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        SecureView::create([
            'titre'       => $titre,
            'description' => $description,
            'auteur'      => $user->id,
            'date'        => now()->format('Y-m-d H:i:s'),
            'type'        => $type,
        ]);
    }
}
