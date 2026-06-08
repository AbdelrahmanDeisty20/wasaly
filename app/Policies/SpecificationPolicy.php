<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Specification;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpecificationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Specification');
    }

    public function view(AuthUser $authUser, Specification $specification): bool
    {
        return $authUser->can('View:Specification');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Specification');
    }

    public function update(AuthUser $authUser, Specification $specification): bool
    {
        return $authUser->can('Update:Specification');
    }

    public function delete(AuthUser $authUser, Specification $specification): bool
    {
        return $authUser->can('Delete:Specification');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Specification');
    }

    public function restore(AuthUser $authUser, Specification $specification): bool
    {
        return $authUser->can('Restore:Specification');
    }

    public function forceDelete(AuthUser $authUser, Specification $specification): bool
    {
        return $authUser->can('ForceDelete:Specification');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Specification');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Specification');
    }

    public function replicate(AuthUser $authUser, Specification $specification): bool
    {
        return $authUser->can('Replicate:Specification');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Specification');
    }

}