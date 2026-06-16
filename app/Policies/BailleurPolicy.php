<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Bailleur;
use Illuminate\Auth\Access\HandlesAuthorization;

class BailleurPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Bailleur');
    }

    public function view(AuthUser $authUser, Bailleur $bailleur): bool
    {
        return $authUser->can('View:Bailleur');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Bailleur');
    }

    public function update(AuthUser $authUser, Bailleur $bailleur): bool
    {
        return $authUser->can('Update:Bailleur');
    }

    public function delete(AuthUser $authUser, Bailleur $bailleur): bool
    {
        return $authUser->can('Delete:Bailleur');
    }

    public function restore(AuthUser $authUser, Bailleur $bailleur): bool
    {
        return $authUser->can('Restore:Bailleur');
    }

    public function forceDelete(AuthUser $authUser, Bailleur $bailleur): bool
    {
        return $authUser->can('ForceDelete:Bailleur');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Bailleur');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Bailleur');
    }

    public function replicate(AuthUser $authUser, Bailleur $bailleur): bool
    {
        return $authUser->can('Replicate:Bailleur');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Bailleur');
    }
}
