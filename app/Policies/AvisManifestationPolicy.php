<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AvisManifestation;
use Illuminate\Auth\Access\HandlesAuthorization;

class AvisManifestationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AvisManifestation');
    }

    public function view(AuthUser $authUser, AvisManifestation $avisManifestation): bool
    {
        return $authUser->can('View:AvisManifestation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AvisManifestation');
    }

    public function update(AuthUser $authUser, AvisManifestation $avisManifestation): bool
    {
        return $authUser->can('Update:AvisManifestation');
    }

    public function delete(AuthUser $authUser, AvisManifestation $avisManifestation): bool
    {
        return $authUser->can('Delete:AvisManifestation');
    }

    public function restore(AuthUser $authUser, AvisManifestation $avisManifestation): bool
    {
        return $authUser->can('Restore:AvisManifestation');
    }

    public function forceDelete(AuthUser $authUser, AvisManifestation $avisManifestation): bool
    {
        return $authUser->can('ForceDelete:AvisManifestation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AvisManifestation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AvisManifestation');
    }

    public function replicate(AuthUser $authUser, AvisManifestation $avisManifestation): bool
    {
        return $authUser->can('Replicate:AvisManifestation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AvisManifestation');
    }

}