<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Favorite;
use Illuminate\Auth\Access\HandlesAuthorization;

class FavoritePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Favorite');
    }

    public function view(AuthUser $authUser, Favorite $model): bool
    {
        return $authUser->can('View:Favorite');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Favorite');
    }

    public function update(AuthUser $authUser, Favorite $model): bool
    {
        return $authUser->can('Update:Favorite');
    }

    public function delete(AuthUser $authUser, Favorite $model): bool
    {
        return $authUser->can('Delete:Favorite');
    }

    public function restore(AuthUser $authUser, Favorite $model): bool
    {
        return $authUser->can('Restore:Favorite');
    }

    public function forceDelete(AuthUser $authUser, Favorite $model): bool
    {
        return $authUser->can('ForceDelete:Favorite');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Favorite');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Favorite');
    }

    public function replicate(AuthUser $authUser, Favorite $model): bool
    {
        return $authUser->can('Replicate:Favorite');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Favorite');
    }

}
