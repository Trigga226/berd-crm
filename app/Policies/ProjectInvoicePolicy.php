<?php

namespace App\Policies;

use App\Models\ProjectInvoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectInvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Comptable']);
    }

    public function view(User $user, ProjectInvoice $invoice): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Comptable']);
    }

    public function update(User $user, ProjectInvoice $invoice): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Comptable']);
    }

    public function delete(User $user, ProjectInvoice $invoice): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Comptable']);
    }

    public function restore(User $user, ProjectInvoice $invoice): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Comptable']);
    }
}
