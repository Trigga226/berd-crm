<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Manifestation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManifestationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Manifestation');
    }

    public function view(AuthUser $authUser, Manifestation $manifestation): bool
    {
        return $authUser->can('View:Manifestation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Manifestation');
    }

    public function update(AuthUser $authUser, Manifestation $manifestation): bool
    {
        return $authUser->can('Update:Manifestation');
    }

    public function delete(AuthUser $authUser, Manifestation $manifestation): bool
    {
        return $authUser->can('Delete:Manifestation');
    }

    public function restore(AuthUser $authUser, Manifestation $manifestation): bool
    {
        return $authUser->can('Restore:Manifestation');
    }

    public function forceDelete(AuthUser $authUser, Manifestation $manifestation): bool
    {
        return $authUser->can('ForceDelete:Manifestation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Manifestation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Manifestation');
    }

    public function replicate(AuthUser $authUser, Manifestation $manifestation): bool
    {
        return $authUser->can('Replicate:Manifestation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Manifestation');
    }

}