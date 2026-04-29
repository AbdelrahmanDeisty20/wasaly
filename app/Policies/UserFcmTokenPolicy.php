<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\UserFcmToken;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserFcmTokenPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:UserFcmToken');
    }

    public function view(AuthUser $authUser, UserFcmToken $model): bool
    {
        return $authUser->can('View:UserFcmToken');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:UserFcmToken');
    }

    public function update(AuthUser $authUser, UserFcmToken $model): bool
    {
        return $authUser->can('Update:UserFcmToken');
    }

    public function delete(AuthUser $authUser, UserFcmToken $model): bool
    {
        return $authUser->can('Delete:UserFcmToken');
    }

    public function restore(AuthUser $authUser, UserFcmToken $model): bool
    {
        return $authUser->can('Restore:UserFcmToken');
    }

    public function forceDelete(AuthUser $authUser, UserFcmToken $model): bool
    {
        return $authUser->can('ForceDelete:UserFcmToken');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:UserFcmToken');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:UserFcmToken');
    }

    public function replicate(AuthUser $authUser, UserFcmToken $model): bool
    {
        return $authUser->can('Replicate:UserFcmToken');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:UserFcmToken');
    }

}
