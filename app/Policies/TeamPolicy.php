<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Team $team)
    {
        if ($team->user_id === $user->id) {
            return true;
        }

        return $user->linkedTeams()
            ->pluck('id')
            ->contains($team->id);
    }

}
