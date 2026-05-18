<?php

declare(strict_types=1);

namespace App\Models\Concerns;

/**
 * Centralise les vérifications de rôles métier.
 *
 * Usage : Auth::user()->isSuperAdmin()  /  Auth::user()->canViewFinancials()
 *
 * @mixin \App\Models\User
 */
trait HasBusinessRoles
{
    /**
     * L'utilisateur est super-administrateur.
     * Donne accès aux actions destructives (ForceDelete, Restore, TrashedFilter).
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * L'utilisateur peut consulter les données financières
     * (budgets, factures, taux de recouvrement...).
     */
    public function canViewFinancials(): bool
    {
        return $this->hasAnyRole(['super_admin', 'Gerant', 'Comptable']);
    }

    /**
     * L'utilisateur peut gérer les projets
     * (création, modification, assignation d'experts...).
     */
    public function canManageProjects(): bool
    {
        return $this->hasAnyRole(['super_admin', 'Gerant', 'Chef de Projet']);
    }


}
