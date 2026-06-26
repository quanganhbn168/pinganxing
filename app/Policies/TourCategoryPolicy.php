<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TourCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class TourCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TourCategory');
    }

    public function view(AuthUser $authUser, TourCategory $tourCategory): bool
    {
        return $authUser->can('View:TourCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TourCategory');
    }

    public function update(AuthUser $authUser, TourCategory $tourCategory): bool
    {
        return $authUser->can('Update:TourCategory');
    }

    public function delete(AuthUser $authUser, TourCategory $tourCategory): bool
    {
        return $authUser->can('Delete:TourCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TourCategory');
    }

    public function restore(AuthUser $authUser, TourCategory $tourCategory): bool
    {
        return $authUser->can('Restore:TourCategory');
    }

    public function forceDelete(AuthUser $authUser, TourCategory $tourCategory): bool
    {
        return $authUser->can('ForceDelete:TourCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TourCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TourCategory');
    }

    public function replicate(AuthUser $authUser, TourCategory $tourCategory): bool
    {
        return $authUser->can('Replicate:TourCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TourCategory');
    }

}