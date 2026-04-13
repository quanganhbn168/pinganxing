<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ConsultingRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsultingRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ConsultingRequest');
    }

    public function view(AuthUser $authUser, ConsultingRequest $consultingRequest): bool
    {
        return $authUser->can('View:ConsultingRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ConsultingRequest');
    }

    public function update(AuthUser $authUser, ConsultingRequest $consultingRequest): bool
    {
        return $authUser->can('Update:ConsultingRequest');
    }

    public function delete(AuthUser $authUser, ConsultingRequest $consultingRequest): bool
    {
        return $authUser->can('Delete:ConsultingRequest');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ConsultingRequest');
    }

    public function restore(AuthUser $authUser, ConsultingRequest $consultingRequest): bool
    {
        return $authUser->can('Restore:ConsultingRequest');
    }

    public function forceDelete(AuthUser $authUser, ConsultingRequest $consultingRequest): bool
    {
        return $authUser->can('ForceDelete:ConsultingRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ConsultingRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ConsultingRequest');
    }

    public function replicate(AuthUser $authUser, ConsultingRequest $consultingRequest): bool
    {
        return $authUser->can('Replicate:ConsultingRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ConsultingRequest');
    }

}