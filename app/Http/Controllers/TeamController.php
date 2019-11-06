<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Resources\TeamResource;

class TeamController extends Controller
{
    
    public function show(Request $request, Team $team)
    {
        $this->authorize('view', $team);

        return response()->json(
            TeamResource::make($team)
        );
    }

}
