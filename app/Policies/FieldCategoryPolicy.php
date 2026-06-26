<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\FieldCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class FieldCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FieldCategory');
    }

    public function view(AuthUser $authUser, FieldCategory $fieldCategory): bool
    {
        return $authUser->can('View:FieldCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FieldCategory');
    }

    public function update(AuthUser $authUser, FieldCategory $fieldCategory): bool
    {
        return $authUser->can('Update:FieldCategory');
    }

    public function delete(AuthUser $authUser, FieldCategory $fieldCategory): bool
    {
        return $authUser->can('Delete:FieldCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FieldCategory');
    }

    public function restore(AuthUser $authUser, FieldCategory $fieldCategory): bool
    {
        return $authUser->can('Restore:FieldCategory');
    }

    public function forceDelete(AuthUser $authUser, FieldCategory $fieldCategory): bool
    {
        return $authUser->can('ForceDelete:FieldCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FieldCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FieldCategory');
    }

    public function replicate(AuthUser $authUser, FieldCategory $fieldCategory): bool
    {
        return $authUser->can('Replicate:FieldCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FieldCategory');
    }

}