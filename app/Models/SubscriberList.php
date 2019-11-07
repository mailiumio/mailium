<?php

namespace App\Models;

use App\Models\Team;
use Illuminate\Database\Eloquent\Model;

class SubscriberList extends Model
{
    protected $table = 'lists';

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

}
