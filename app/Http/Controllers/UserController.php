<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    
    public function show()
    {
        return UserResource::make(Auth::user());
    }

    public function logout()
    {
        Auth::user()->token()->revoke();

        return response(200);
    }

}
