<?php

namespace App\Models;

use App\Models\Team;
use Illuminate\Database\Eloquent\Model;

class SubscriberList extends Model
{
    protected $table = 'lists';

    protected $fillable = [
        'name',
        'team_id'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

}
