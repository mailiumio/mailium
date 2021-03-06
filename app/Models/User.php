<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'github_id', 'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function linkedSocialAccounts()
    {
        return $this->hasMany(LinkedSocialAccount::class);
    }

    public function ownedTeams()
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function linkedTeams()
    {
        return $this->belongsToMany(Team::class, 'invitations', 'owner_id')
            ->as('invitation')
            ->withTimestamps();
    }

    public function teams()
    {
        return $this->ownedTeams->merge(
            $this->linkedTeams
        );
    }

    public function isAssociatedWith(Team $team)
    {
        if ($this->id === $team->owner->id) {
            return true;
        }

        return $this->linkedTeams()
            ->pluck('id')
            ->contains($team->id);
    }

    public function lists()
    {
        return $this->teams()->flatMap(function ($team) {
            return $team->lists;
        });
    }

}
