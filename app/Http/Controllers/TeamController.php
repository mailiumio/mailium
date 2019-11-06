<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Resources\TeamResource;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    
    public function show(Request $request, Team $team)
    {
        $this->authorize('view', $team);

        return TeamResource::make($team);
    }

    public function index()
    {
        return TeamResource::collection(
            Auth::user()->teams->merge(
                Auth::user()->linkedTeams
            )
        );
    }

}
