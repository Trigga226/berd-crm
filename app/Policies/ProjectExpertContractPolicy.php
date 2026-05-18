<?php

namespace App\Policies;

use App\Models\ProjectExpertContract;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectExpertContractPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut voir la liste des contrats experts.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge', 'Comptable', 'secretaire de direction']);
    }

    /**
     * Détermine si l'utilisateur peut voir un contrat expert.
     */
    public function view(User $user, ProjectExpertContract $contract): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Détermine si l'utilisateur peut créer un contrat expert.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge', 'Comptable', 'secretaire de direction']);
    }

    /**
     * Détermine si l'utilisateur peut modifier un contrat expert.
     */
    public function update(User $user, ProjectExpertContract $contract): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge', 'Comptable', 'secretaire de direction']);
    }

    /**
     * Détermine si l'utilisateur peut supprimer un contrat expert.
     */
    public function delete(User $user, ProjectExpertContract $contract): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant']);
    }

    /**
     * Détermine si l'utilisateur peut restaurer un contrat expert.
     */
    public function restore(User $user, ProjectExpertContract $contract): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant']);
    }
}
