<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AppNotification;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppNotificationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AppNotification');
    }

    public function view(AuthUser $authUser, AppNotification $model): bool
    {
        return $authUser->can('View:AppNotification');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AppNotification');
    }

    public function update(AuthUser $authUser, AppNotification $model): bool
    {
        return $authUser->can('Update:AppNotification');
    }

    public function delete(AuthUser $authUser, AppNotification $model): bool
    {
        return $authUser->can('Delete:AppNotification');
    }

    public function restore(AuthUser $authUser, AppNotification $model): bool
    {
        return $authUser->can('Restore:AppNotification');
    }

    public function forceDelete(AuthUser $authUser, AppNotification $model): bool
    {
        return $authUser->can('ForceDelete:AppNotification');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AppNotification');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AppNotification');
    }

    public function replicate(AuthUser $authUser, AppNotification $model): bool
    {
        return $authUser->can('Replicate:AppNotification');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AppNotification');
    }

}
