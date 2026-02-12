<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Expert;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpertPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Expert');
    }

    public function view(AuthUser $authUser, Expert $expert): bool
    {
        return $authUser->can('View:Expert');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Expert');
    }

    public function update(AuthUser $authUser, Expert $expert): bool
    {
        return $authUser->can('Update:Expert');
    }

    public function delete(AuthUser $authUser, Expert $expert): bool
    {
        return $authUser->can('Delete:Expert');
    }

    public function restore(AuthUser $authUser, Expert $expert): bool
    {
        return $authUser->can('Restore:Expert');
    }

    public function forceDelete(AuthUser $authUser, Expert $expert): bool
    {
        return $authUser->can('ForceDelete:Expert');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Expert');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Expert');
    }

    public function replicate(AuthUser $authUser, Expert $expert): bool
    {
        return $authUser->can('Replicate:Expert');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Expert');
    }

}