<?php

namespace App\Policies;

use App\Models\ProjectRisk;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectRiskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge']);
    }

    public function view(User $user, ProjectRisk $risk): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge']);
    }

    public function update(User $user, ProjectRisk $risk): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge']);
    }

    public function delete(User $user, ProjectRisk $risk): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant']);
    }
}
