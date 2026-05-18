<?php

namespace App\Policies;

use App\Models\ProjectAmendment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectAmendmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge', 'secretaire de direction']);
    }

    public function view(User $user, ProjectAmendment $amendment): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge', 'secretaire de direction']);
    }

    public function update(User $user, ProjectAmendment $amendment): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge', 'secretaire de direction']);
    }

    public function delete(User $user, ProjectAmendment $amendment): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant']);
    }

    public function restore(User $user, ProjectAmendment $amendment): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant']);
    }
}
