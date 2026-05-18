<?php

namespace App\Policies;

use App\Models\ProjectReport;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge']);
    }

    public function view(User $user, ProjectReport $report): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge']);
    }

    public function update(User $user, ProjectReport $report): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant', 'Charge', 'Assistant charge']);
    }

    public function delete(User $user, ProjectReport $report): bool
    {
        return $user->hasAnyRole(['super_admin', 'Gerant']);
    }
}
