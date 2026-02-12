<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Poste;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Poste');
    }

    public function view(AuthUser $authUser, Poste $poste): bool
    {
        return $authUser->can('View:Poste');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Poste');
    }

    public function update(AuthUser $authUser, Poste $poste): bool
    {
        return $authUser->can('Update:Poste');
    }

    public function delete(AuthUser $authUser, Poste $poste): bool
    {
        return $authUser->can('Delete:Poste');
    }

    public function restore(AuthUser $authUser, Poste $poste): bool
    {
        return $authUser->can('Restore:Poste');
    }

    public function forceDelete(AuthUser $authUser, Poste $poste): bool
    {
        return $authUser->can('ForceDelete:Poste');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Poste');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Poste');
    }

    public function replicate(AuthUser $authUser, Poste $poste): bool
    {
        return $authUser->can('Replicate:Poste');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Poste');
    }

}