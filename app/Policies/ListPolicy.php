<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SubscriberList;
use Illuminate\Auth\Access\HandlesAuthorization;

class ListPolicy
{
    use HandlesAuthorization;

    public function view(User $user, SubscriberList $list)
    {
        return $user->isAssociatedWith($list->team);
    }

    public function delete(User $user, SubscriberList $list)
    {
        return $user->isAssociatedWith($list->team);
    }

    public function update(User $user, SubscriberList $list)
    {
        return $user->isAssociatedWith($list->team);
    }

}
