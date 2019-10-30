<?php

namespace App\Http\Controllers;

use Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->stateless()->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $user = $this->findOrCreateGithubUser(
            Socialite::driver('github')->stateless()->user()
        );

        Auth::login($user, true);

        return Redirect::to(config('client.auth.redirect_url') . "?token=${Auth::user()->createToken('Client')->accessToken}");
    }

    private function findOrCreateGithubUser($githubUser) {
        $user = User::firstOrNew([
            'github_id' => $githubUser->id,
        ]);


        if ($user->exists) return $user;

        $user->fill([
            'email' => $githubUser->email,
            'name' => $githubUser->nickname,
            'avatar' => $githubUser->avatar,
        ])->save();

        return $user;
    }
}


