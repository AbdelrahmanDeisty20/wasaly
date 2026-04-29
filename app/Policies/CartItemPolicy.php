<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CartItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class CartItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CartItem');
    }

    public function view(AuthUser $authUser, CartItem $model): bool
    {
        return $authUser->can('View:CartItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CartItem');
    }

    public function update(AuthUser $authUser, CartItem $model): bool
    {
        return $authUser->can('Update:CartItem');
    }

    public function delete(AuthUser $authUser, CartItem $model): bool
    {
        return $authUser->can('Delete:CartItem');
    }

    public function restore(AuthUser $authUser, CartItem $model): bool
    {
        return $authUser->can('Restore:CartItem');
    }

    public function forceDelete(AuthUser $authUser, CartItem $model): bool
    {
        return $authUser->can('ForceDelete:CartItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CartItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CartItem');
    }

    public function replicate(AuthUser $authUser, CartItem $model): bool
    {
        return $authUser->can('Replicate:CartItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CartItem');
    }

}
