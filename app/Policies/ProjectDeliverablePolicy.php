<?php

namespace App\Policies;

use App\Models\ProjectDeliverable;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectDeliverablePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge', 'secretaire de direction']);
    }

    public function view(User $user, ProjectDeliverable $deliverable): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge', 'secretaire de direction']);
    }

    public function update(User $user, ProjectDeliverable $deliverable): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge', 'secretaire de direction']);
    }

    public function delete(User $user, ProjectDeliverable $deliverable): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant']);
    }

    public function restore(User $user, ProjectDeliverable $deliverable): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant']);
    }
}
