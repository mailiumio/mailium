<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Resources\ListResource;

class TeamListController extends Controller
{
    
    public function index(Team $team)
    {
        $this->authorize('view', $team);

        return ListResource::collection($team->lists);
    }

}
