<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AgencyRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class AgencyRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AgencyRequest');
    }

    public function view(AuthUser $authUser, AgencyRequest $agencyRequest): bool
    {
        return $authUser->can('View:AgencyRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AgencyRequest');
    }

    public function update(AuthUser $authUser, AgencyRequest $agencyRequest): bool
    {
        return $authUser->can('Update:AgencyRequest');
    }

    public function delete(AuthUser $authUser, AgencyRequest $agencyRequest): bool
    {
        return $authUser->can('Delete:AgencyRequest');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AgencyRequest');
    }

    public function restore(AuthUser $authUser, AgencyRequest $agencyRequest): bool
    {
        return $authUser->can('Restore:AgencyRequest');
    }

    public function forceDelete(AuthUser $authUser, AgencyRequest $agencyRequest): bool
    {
        return $authUser->can('ForceDelete:AgencyRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AgencyRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AgencyRequest');
    }

    public function replicate(AuthUser $authUser, AgencyRequest $agencyRequest): bool
    {
        return $authUser->can('Replicate:AgencyRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AgencyRequest');
    }

}