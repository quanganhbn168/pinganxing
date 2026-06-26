<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SampleReview;
use Illuminate\Auth\Access\HandlesAuthorization;

class SampleReviewPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SampleReview');
    }

    public function view(AuthUser $authUser, SampleReview $sampleReview): bool
    {
        return $authUser->can('View:SampleReview');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SampleReview');
    }

    public function update(AuthUser $authUser, SampleReview $sampleReview): bool
    {
        return $authUser->can('Update:SampleReview');
    }

    public function delete(AuthUser $authUser, SampleReview $sampleReview): bool
    {
        return $authUser->can('Delete:SampleReview');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SampleReview');
    }

    public function restore(AuthUser $authUser, SampleReview $sampleReview): bool
    {
        return $authUser->can('Restore:SampleReview');
    }

    public function forceDelete(AuthUser $authUser, SampleReview $sampleReview): bool
    {
        return $authUser->can('ForceDelete:SampleReview');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SampleReview');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SampleReview');
    }

    public function replicate(AuthUser $authUser, SampleReview $sampleReview): bool
    {
        return $authUser->can('Replicate:SampleReview');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SampleReview');
    }

}