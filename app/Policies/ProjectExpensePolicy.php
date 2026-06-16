<?php

namespace App\Policies;

use App\Models\ProjectExpense;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectExpensePolicy
{
    use HandlesAuthorization;

    private const MANAGE_ROLES = ['super_admin', 'Gerant', 'Charge', 'Assistant charge', 'Comptable', 'secretaire de direction'];

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGE_ROLES);
    }

    public function view(User $user, ProjectExpense $expense): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(self::MANAGE_ROLES);
    }

    public function update(User $user, ProjectExpense $expense): bool
    {
        return $user->hasAnyRole(self::MANAGE_ROLES);
    }

    public function delete(User $user, ProjectExpense $expense): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant']);
    }

    public function restore(User $user, ProjectExpense $expense): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant']);
    }
}
