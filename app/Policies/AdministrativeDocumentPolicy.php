<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AdministrativeDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdministrativeDocumentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AdministrativeDocument');
    }

    public function view(AuthUser $authUser, AdministrativeDocument $administrativeDocument): bool
    {
        return $authUser->can('View:AdministrativeDocument');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AdministrativeDocument');
    }

    public function update(AuthUser $authUser, AdministrativeDocument $administrativeDocument): bool
    {
        return $authUser->can('Update:AdministrativeDocument');
    }

    public function delete(AuthUser $authUser, AdministrativeDocument $administrativeDocument): bool
    {
        return $authUser->can('Delete:AdministrativeDocument');
    }

    public function restore(AuthUser $authUser, AdministrativeDocument $administrativeDocument): bool
    {
        return $authUser->can('Restore:AdministrativeDocument');
    }

    public function forceDelete(AuthUser $authUser, AdministrativeDocument $administrativeDocument): bool
    {
        return $authUser->can('ForceDelete:AdministrativeDocument');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AdministrativeDocument');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AdministrativeDocument');
    }

    public function replicate(AuthUser $authUser, AdministrativeDocument $administrativeDocument): bool
    {
        return $authUser->can('Replicate:AdministrativeDocument');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AdministrativeDocument');
    }

}