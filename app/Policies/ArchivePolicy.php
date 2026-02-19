<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Archive;
use Illuminate\Auth\Access\HandlesAuthorization;

class ArchivePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Archive');
    }

    public function view(AuthUser $authUser, Archive $archive): bool
    {
        return $authUser->can('View:Archive');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Archive');
    }

    public function update(AuthUser $authUser, Archive $archive): bool
    {
        return $authUser->can('Update:Archive');
    }

    public function delete(AuthUser $authUser, Archive $archive): bool
    {
        return $authUser->can('Delete:Archive');
    }

    public function restore(AuthUser $authUser, Archive $archive): bool
    {
        return $authUser->can('Restore:Archive');
    }

    public function forceDelete(AuthUser $authUser, Archive $archive): bool
    {
        return $authUser->can('ForceDelete:Archive');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Archive');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Archive');
    }

    public function replicate(AuthUser $authUser, Archive $archive): bool
    {
        return $authUser->can('Replicate:Archive');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Archive');
    }

}