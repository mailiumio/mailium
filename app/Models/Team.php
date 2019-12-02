<?php

namespace App\Models;

use App\Models\SubscriberList;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name'
    ];
    
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function lists()
    {
        return $this->hasMany(SubscriberList::class);
    }

}
