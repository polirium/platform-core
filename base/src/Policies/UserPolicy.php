<?php

namespace Polirium\Core\Base\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Polirium\Core\Base\Http\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): mixed
    {
        //
    }

    public function view(User $user, User $model): mixed
    {
        //
    }

    public function create(User $user): mixed
    {
        //
    }

    public function update(User $user, User $model): mixed
    {
        //
    }

    public function delete(User $user, User $model): mixed
    {
        //
    }

    public function restore(User $user, User $model): mixed
    {
        //
    }

    public function forceDelete(User $user, User $model): mixed
    {
        //
    }
}
